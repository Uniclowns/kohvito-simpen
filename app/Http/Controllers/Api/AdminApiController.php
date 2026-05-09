<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriMenu;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminApiController extends Controller
{
    // -------------------------------------------------------------------------
    // Dashboard
    // -------------------------------------------------------------------------

    /**
     * Ringkasan dashboard: omzet hari ini, omzet bulan ini, total pesanan hari ini.
     */
    public function dashboard(): JsonResponse
    {
        $today = Carbon::today();

        $omzetHariIni = Pesanan::where('status_pembayaran', 'lunas')
            ->whereDate('tgl_pembayaran', $today)
            ->sum('total_harga');

        $omzetBulanIni = Pesanan::where('status_pembayaran', 'lunas')
            ->whereYear('tgl_pembayaran', $today->year)
            ->whereMonth('tgl_pembayaran', $today->month)
            ->sum('total_harga');

        $totalPesananHariIni = Pesanan::whereDate('tgl_pembayaran', $today)->count();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'omzetHariIni'        => (int) $omzetHariIni,
                'omzetBulanIni'       => (int) $omzetBulanIni,
                'totalPesananHariIni' => (int) $totalPesananHariIni,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Order Status (buka / tutup)
    // -------------------------------------------------------------------------

    /**
     * Ambil status pemesanan saat ini dari cache.
     */
    public function getOrderStatus(): JsonResponse
    {
        $status = Cache::get('order_status', 'buka');

        return response()->json([
            'status' => 'success',
            'data'   => ['status' => $status],
        ]);
    }

    /**
     * Toggle status pemesanan buka ↔ tutup.
     */
    public function toggleOrderStatus(): JsonResponse
    {
        $current = Cache::get('order_status', 'buka');
        $new     = $current === 'buka' ? 'tutup' : 'buka';
        Cache::forever('order_status', $new);

        return response()->json([
            'status'  => 'success',
            'message' => $new === 'tutup' ? 'Pemesanan berhasil ditutup.' : 'Pemesanan berhasil dibuka.',
            'data'    => ['status' => $new],
        ]);
    }

    // -------------------------------------------------------------------------
    // Menu
    // -------------------------------------------------------------------------

    /**
     * Daftar menu dengan paginasi.
     */
    public function indexMenu(): JsonResponse
    {
        $menus = Menu::with('kategori')->paginate(10);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'current_page' => $menus->currentPage(),
                'data'         => $menus->items(),
                'total'        => $menus->total(),
                'per_page'     => $menus->perPage(),
            ],
        ]);
    }

    /**
     * Simpan menu baru beserta gambar (opsional).
     */
    public function storeMenu(Request $request): JsonResponse
    {
        $request->validate([
            'nama_menu'           => ['required', 'string', 'max:255'],
            'id_kategori'         => ['required', 'exists:kategori_menu,id_kategori'],
            'deskripsi'           => ['nullable', 'string'],
            'harga'               => ['required', 'integer', 'min:0'],
            'status_ketersediaan' => ['required', 'in:tersedia,habis'],
            'gambar_menu'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $gambarFilename = null;
        if ($request->hasFile('gambar_menu')) {
            $stored         = $request->file('gambar_menu')->store('menu-images', 'public');
            $gambarFilename = basename($stored);
        }

        $menu = Menu::create([
            'nama_menu'           => $request->nama_menu,
            'id_kategori'         => $request->id_kategori,
            'deskripsi'           => $request->deskripsi,
            'harga'               => $request->harga,
            'gambar_menu'         => $gambarFilename,
            'status_ketersediaan' => $request->status_ketersediaan,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Menu berhasil ditambahkan',
            'data'    => $menu,
        ], 201);
    }

    /**
     * Update menu yang sudah ada.
     */
    public function updateMenu(Request $request, string $id): JsonResponse
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'nama_menu'           => ['sometimes', 'required', 'string', 'max:255'],
            'id_kategori'         => ['sometimes', 'required', 'exists:kategori_menu,id_kategori'],
            'deskripsi'           => ['nullable', 'string'],
            'harga'               => ['sometimes', 'required', 'integer', 'min:0'],
            'status_ketersediaan' => ['sometimes', 'required', 'in:tersedia,habis'],
            'gambar_menu'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('gambar_menu')) {
            if ($menu->gambar_menu) {
                Storage::disk('public')->delete('menu-images/' . $menu->gambar_menu);
            }
            $stored              = $request->file('gambar_menu')->store('menu-images', 'public');
            $menu->gambar_menu   = basename($stored);
        }

        $menu->fill($request->only([
            'nama_menu', 'id_kategori', 'deskripsi', 'harga', 'status_ketersediaan',
        ]));
        $menu->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Menu berhasil diupdate',
            'data'    => $menu,
        ]);
    }

    /**
     * Hapus menu dan file gambarnya.
     */
    public function destroyMenu(string $id): JsonResponse
    {
        $menu = Menu::findOrFail($id);

        if ($menu->gambar_menu) {
            Storage::disk('public')->delete('menu-images/' . $menu->gambar_menu);
        }

        $menu->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Menu berhasil dihapus',
            'data'    => null,
        ]);
    }

    // -------------------------------------------------------------------------
    // Kategori Menu
    // -------------------------------------------------------------------------

    /**
     * Daftar kategori menu beserta jumlah menu.
     */
    public function indexKategori(): JsonResponse
    {
        $kategoris = KategoriMenu::withCount('menu')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $kategoris,
        ]);
    }

    /**
     * Simpan kategori baru.
     */
    public function storeKategori(Request $request): JsonResponse
    {
        $request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', 'unique:kategori_menu,nama_kategori'],
        ]);

        $kategori = KategoriMenu::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Kategori berhasil ditambahkan',
            'data'    => $kategori,
        ], 201);
    }

    /**
     * Hapus kategori (ditolak jika masih punya menu).
     */
    public function destroyKategori(string $id): JsonResponse
    {
        $kategori = KategoriMenu::findOrFail($id);

        if ($kategori->menu()->count() > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kategori masih memiliki menu aktif',
                'data'    => null,
            ], 422);
        }

        $kategori->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Kategori berhasil dihapus',
            'data'    => null,
        ]);
    }


    // -------------------------------------------------------------------------
    // Pengguna Kasir
    // -------------------------------------------------------------------------

    /**
     * Daftar semua akun kasir (tanpa password).
     */
    public function indexKasir(): JsonResponse
    {
        $kasirs = User::whereHas('role', fn ($q) => $q->where('nama_role', 'Kasir'))
            ->with('role')
            ->get()
            ->makeHidden('password');

        return response()->json([
            'status' => 'success',
            'data'   => $kasirs,
        ]);
    }

    /**
     * Buat akun kasir baru.
     */
    public function storeKasir(Request $request): JsonResponse
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'username'     => ['required', 'string', 'max:255', 'unique:users,username'],
            'password'     => ['required', 'string', 'min:6'],
        ]);

        $roleKasir = Role::where('nama_role', 'Kasir')->firstOrFail();

        $user = User::create([
            'id_role'      => $roleKasir->id_role,
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'password'     => $request->password,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Akun kasir berhasil ditambahkan',
            'data'    => $user->makeHidden('password'),
        ], 201);
    }

    /**
     * Hapus akun kasir.
     */
    public function destroyKasir(string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if (! $user->role || strtolower($user->role->nama_role) !== 'kasir') {
            return response()->json([
                'status'  => 'error',
                'message' => 'User bukan kasir',
                'data'    => null,
            ], 422);
        }

        $user->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Akun kasir berhasil dihapus',
            'data'    => null,
        ]);
    }

    // -------------------------------------------------------------------------
    // Laporan
    // -------------------------------------------------------------------------

    /**
     * Laporan keuangan dengan filter rentang tanggal.
     */
    public function indexLaporan(Request $request): JsonResponse
    {
        $tanggalMulai = $request->tanggal_mulai
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : Carbon::today()->startOfDay();

        $tanggalSelesai = $request->tanggal_selesai
            ? Carbon::parse($request->tanggal_selesai)->endOfDay()
            : Carbon::today()->endOfDay();

        $pesanans = Pesanan::with(['meja', 'user'])
            ->where('status_pembayaran', 'lunas')
            ->whereBetween('tgl_pembayaran', [$tanggalMulai, $tanggalSelesai])
            ->get();

        $totalOmzet     = $pesanans->sum('total_harga');
        $totalTransaksi = $pesanans->count();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'pesanans'       => $pesanans,
                'totalOmzet'     => (int) $totalOmzet,
                'totalTransaksi' => $totalTransaksi,
                'tanggalMulai'   => $tanggalMulai->toDateString(),
                'tanggalSelesai' => $tanggalSelesai->toDateString(),
            ],
        ]);
    }
}
