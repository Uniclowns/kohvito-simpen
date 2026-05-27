<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriMenu;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\Role;
use App\Models\User;
use App\Traits\ApiResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Class AdminApiController
 * 
 * Controller API ini melayani modul administrasi panel kontrol eksternal (RESTful API).
 * Mengelompokkan fungsionalitas menjadi 5 pilar utama:
 * 1. **Dashboard & Analitik**: Ringkasan finansial harian dan bulanan.
 * 2. **Operasional Toko**: Buka/tutup pemesanan global menggunakan Cache.
 * 3. **Manajemen Menu**: CRUD menu beserta upload/hashing gambar menu.
 * 4. **Manajemen Kategori**: CRUD pengelompokan menu dengan pencegahan restricted delete.
 * 5. **Manajemen Kasir**: Registrasi akun staf kasir terenkripsi.
 *
 * @package App\Http\Controllers\Api
 */
class AdminApiController extends Controller
{
    use ApiResponses; // Menyertakan trait untuk standarisasi format respon JSON

    // ─────────────────────────────────────────────────────────────────────────
    // Dashboard & Analitik
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Menyediakan data ringkasan eksekutif keuangan dan kuantitas transaksi hari ini.
     *
     * @return \Illuminate\Http\JsonResponse Respon sukses berisikan ringkasan data finansial
     */
    public function dashboard(): JsonResponse
    {
        $today = Carbon::today();

        // 1. Akumulasi total omzet bersih yang lunas khusus hari ini
        $omzetHariIni = Pesanan::where('status_pembayaran', 'lunas')
            ->whereDate('tgl_pembayaran', $today)
            ->sum('total_harga');

        // 2. Akumulasi total omzet lunas khusus bulan berjalan
        $omzetBulanIni = Pesanan::where('status_pembayaran', 'lunas')
            ->whereYear('tgl_pembayaran', $today->year)
            ->whereMonth('tgl_pembayaran', $today->month)
            ->sum('total_harga');

        // 3. Hitung jumlah transaksi masuk khusus hari ini
        $totalPesananHariIni = Pesanan::whereDate('tgl_pembayaran', $today)->count();

        return $this->successResponse([
            'omzetHariIni'        => (int) $omzetHariIni,
            'omzetBulanIni'       => (int) $omzetBulanIni,
            'totalPesananHariIni' => (int) $totalPesananHariIni,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Operasional Toko (Order Status)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Mendapatkan status operasional penerimaan pesanan kafe saat ini dari cache global.
     *
     * @return \Illuminate\Http\JsonResponse Respon sukses berisi status ('buka' atau 'tutup')
     */
    public function getOrderStatus(): JsonResponse
    {
        $status = Cache::get('order_status', 'buka');

        return $this->successResponse(['status' => $status]);
    }

    /**
     * Mengubah status operasional toko secara instan (Toggle Buka ↔ Tutup).
     *
     * @return \Illuminate\Http\JsonResponse Respon sukses berisi status baru hasil pembaruan
     */
    public function toggleOrderStatus(): JsonResponse
    {
        $current = Cache::get('order_status', 'buka');
        $new     = $current === 'buka' ? 'tutup' : 'buka';
        
        // Simpan state baru secara permanen di cache
        Cache::forever('order_status', $new);

        $message = $new === 'tutup' ? 'Pemesanan berhasil ditutup.' : 'Pemesanan berhasil dibuka.';

        return $this->successResponse(['status' => $new], $message);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Manajemen Menu
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Menampilkan daftar menu berpaginasi (10 menu per halaman) beserta kategori terikatnya.
     *
     * @return \Illuminate\Http\JsonResponse Respon sukses paginasi data menu
     */
    public function indexMenu(): JsonResponse
    {
        $menus = Menu::with('kategoris')->paginate(10);

        return $this->successResponse([
            'current_page' => $menus->currentPage(),
            'data'         => $menus->items(),
            'total'        => $menus->total(),
            'per_page'     => $menus->perPage(),
        ]);
    }

    /**
     * Validasi dan simpan menu baru beserta unggah berkas gambar menu (opsional).
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP Request pembawa formulir menu baru
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMenu(Request $request): JsonResponse
    {
        // 1. Validasi tipe data dan kriteria wajib isian form menu baru
        $request->validate([
            'nama_menu'           => ['required', 'string', 'max:255'],
            'id_kategori'         => ['nullable', 'array'],
            'id_kategori.*'       => ['exists:kategori_menu,id_kategori'],
            'deskripsi'           => ['nullable', 'string'],
            'harga'               => ['required', 'integer', 'min:0'],
            'status_ketersediaan' => ['required', 'in:tersedia,habis'],
            'gambar_menu'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // 2. Pemrosesan unggah gambar jika dilampirkan
        $gambarFilename = null;
        if ($request->hasFile('gambar_menu')) {
            $stored         = $request->file('gambar_menu')->store('menu-images', 'public');
            $gambarFilename = basename($stored);
        }

        // 3. Simpan baris data menu baru ke database
        $menu = Menu::create([
            'nama_menu'           => $request->nama_menu,
            'deskripsi'           => $request->deskripsi,
            'harga'               => $request->harga,
            'gambar_menu'         => $gambarFilename,
            'status_ketersediaan' => $request->status_ketersediaan,
        ]);

        // 4. Hubungkan menu dengan kategori-kategori master jika ditentukan
        $kategoriIds = $request->input('id_kategori', []);
        if (!empty($kategoriIds)) {
            $menu->kategoris()->sync($kategoriIds);
        }

        return $this->successResponse($menu->load('kategoris'), 'Menu berhasil ditambahkan', 201);
    }

    /**
     * Validasi dan perbarui data produk menu terpilih di database.
     * Mengapus gambar lama secara bersih dari sistem penyimpanan jika melampirkan gambar baru.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request pembawa data edit menu
     * @param  string  $id  ID menu target perubahan
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMenu(Request $request, string $id): JsonResponse
    {
        $menu = Menu::findOrFail($id);

        // 1. Validasi isian formulir perubahan menu (menggunakan parameter kondisional 'sometimes')
        $request->validate([
            'nama_menu'           => ['sometimes', 'required', 'string', 'max:255'],
            'id_kategori'         => ['sometimes', 'array'],
            'id_kategori.*'       => ['exists:kategori_menu,id_kategori'],
            'deskripsi'           => ['nullable', 'string'],
            'harga'               => ['sometimes', 'required', 'integer', 'min:0'],
            'status_ketersediaan' => ['sometimes', 'required', 'in:tersedia,habis'],
            'gambar_menu'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // 2. Pemrosesan penggantian gambar baru
        if ($request->hasFile('gambar_menu')) {
            // Hapus gambar lama agar tidak meninggalkan sampah file di folder publik
            if ($menu->gambar_menu) {
                Storage::disk('public')->delete('menu-images/' . $menu->gambar_menu);
            }
            $stored            = $request->file('gambar_menu')->store('menu-images', 'public');
            $menu->gambar_menu = basename($stored);
        }

        // 3. Isi atribut dan simpan perubahan
        $menu->fill($request->only([
            'nama_menu', 'deskripsi', 'harga', 'status_ketersediaan',
        ]));
        $menu->save();

        // 4. Perbarui data relasi pivot kategori jika disertakan
        if ($request->has('id_kategori')) {
            $menu->kategoris()->sync($request->input('id_kategori', []));
        }

        return $this->successResponse($menu->load('kategoris'), 'Menu berhasil diupdate');
    }

    /**
     * Hapus permanen produk menu beserta berkas gambarnya dari database.
     *
     * @param  string  $id  ID menu target penghapusan
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyMenu(string $id): JsonResponse
    {
        $menu = Menu::findOrFail($id);

        // 1. Hapus berkas gambar menu jika ada
        if ($menu->gambar_menu) {
            Storage::disk('public')->delete('menu-images/' . $menu->gambar_menu);
        }

        // 2. Hapus data menu dari database
        $menu->delete();

        return $this->successResponse(null, 'Menu berhasil dihapus');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Kategori Menu
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Menampilkan daftar seluruh kategori menu beserta hitungan porsi menu terkait.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexKategori(): JsonResponse
    {
        $kategoris = KategoriMenu::withCount('menus')->get();

        return $this->successResponse($kategoris);
    }

    /**
     * Simpan kategori menu baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP Request pembawa nama kategori
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeKategori(Request $request): JsonResponse
    {
        $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori_menu,nama_kategori'],
        ]);

        $kategori = KategoriMenu::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return $this->successResponse($kategori, 'Kategori berhasil ditambahkan', 201);
    }

    /**
     * Menghapus kategori menu terpilih.
     * Mengamankan data: Kategori yang memiliki menu tidak boleh dihapus.
     *
     * @param  string  $id  ID kategori target penghapusan
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyKategori(string $id): JsonResponse
    {
        $kategori = KategoriMenu::findOrFail($id);

        // 1. Proteksi integritas data: Tolak jika kategori memiliki menu terkait
        if ($kategori->menus()->count() > 0) {
            return $this->errorResponse('Kategori masih memiliki menu aktif', 422);
        }

        $kategori->delete();

        return $this->successResponse(null, 'Kategori berhasil dihapus');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Pengguna Kasir
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Menampilkan seluruh staf kasir (tanpa menyertakan kolom password).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexKasir(): JsonResponse
    {
        // 1. Ambil seluruh user yang berelasi dengan role bernama 'Kasir'
        $kasirs = User::whereHas('role', function ($q) {
            $q->where('nama_role', 'Kasir');
        })
        ->with('role')
        ->get()
        ->makeHidden('password'); // Keamanan: Sembunyikan kolom password

        return $this->successResponse($kasirs);
    }

    /**
     * Buat akun Kasir staf baru.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP Request pembawa data kasir baru
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeKasir(Request $request): JsonResponse
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'username'     => ['required', 'string', 'max:255', 'unique:users,username'],
            'password'     => ['required', 'string', 'min:6'],
        ]);

        // 1. Tarik model role Kasir untuk mendapatkan id_role dinamis
        $roleKasir = Role::where('nama_role', 'Kasir')->firstOrFail();

        // 2. Simpan kasir baru ke database (password dihash secara implisit menggunakan casts bcrypt model User)
        $user = User::create([
            'id_role'      => $roleKasir->id_role,
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'password'     => $request->password, // Otomatis hashing berkat casts: password => hashed di model
        ]);

        return $this->successResponse($user->makeHidden('password'), 'Akun kasir berhasil ditambahkan', 201);
    }

    /**
     * Hapus akun Kasir staf dari database.
     *
     * @param  string  $id  ID Kasir target penghapusan
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyKasir(string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // 1. Proteksi Keamanan: Tolak jika target penghapusan bukan akun kasir (misal akun Admin)
        if (! $user->role || strtolower($user->role->nama_role) !== 'kasir') {
            return $this->errorResponse('User bukan kasir', 422);
        }

        $user->delete();

        return $this->successResponse(null, 'Akun kasir berhasil dihapus');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Laporan Keuangan
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Menyediakan data agregasi laporan keuangan detail (lunas) berbasis filter rentang tanggal.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request pembawa filter tanggal
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexLaporan(Request $request): JsonResponse
    {
        // 1. Parsing filter tanggal mulai dan tanggal selesai
        $tanggalMulai = $request->tanggal_mulai
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : Carbon::today()->startOfDay();

        $tanggalSelesai = $request->tanggal_selesai
            ? Carbon::parse($request->tanggal_selesai)->endOfDay()
            : Carbon::today()->endOfDay();

        // 2. Menarik seluruh pesanan lunas terfilter beserta data meja dan kasir
        $pesanans = Pesanan::with(['meja', 'user'])
            ->where('status_pembayaran', 'lunas')
            ->whereBetween('tgl_pembayaran', [$tanggalMulai, $tanggalSelesai])
            ->get();

        // 3. Hitung omzet tagihan bersih dan hitungan transaksi
        $totalOmzet     = $pesanans->sum('total_harga');
        $totalTransaksi = $pesanans->count();

        return $this->successResponse([
            'pesanans'       => $pesanans,
            'totalOmzet'     => (int) $totalOmzet,
            'totalTransaksi' => $totalTransaksi,
            'tanggalMulai'   => $tanggalMulai->toDateString(),
            'tanggalSelesai' => $tanggalSelesai->toDateString(),
        ]);
    }
}
