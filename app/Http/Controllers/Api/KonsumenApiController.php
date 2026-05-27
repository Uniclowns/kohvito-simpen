<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\KategoriMenu;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Xendit\Invoice;
use Xendit\Xendit;

/**
 * Class KonsumenApiController
 * 
 * Controller API ini melayani antarmuka Konsumen secara RESTful (RESTful API).
 * Mengelompokkan fungsionalitas menjadi 5 pilar utama:
 * 1. **Beranda & Meja**: Scan QR meja untuk mendaftarkan session meja.
 * 2. **Katalog & Detail**: Menampilkan menu-menu aktif dan rincian produk.
 * 3. **Keranjang Belanja**: CRUD keranjang belanja berbasis sesi API.
 * 4. **Finalisasi Checkout**: Membuat record pesanan dan relasi detail pesanan secara aman (DB transaction).
 * 5. **Pembayaran Gateway**: Pembuatan invoice Xendit dan pelacakan status pembayaran dinamis.
 *
 * @package App\Http\Controllers\Api
 */
class KonsumenApiController extends Controller
{
    use ApiResponses; // Menyertakan trait untuk standarisasi format respon JSON

    /**
     * Landing page konsumen setelah scan QR meja fisik.
     * Mengunci ID Meja ke dalam session server dan mengembalikan detail meja serta katalog menu.
     *
     * @param  string  $noMeja  Nomor identifikasi meja hasil scan QR
     * @return \Illuminate\Http\JsonResponse Respon berisi data meja dan katalog menu
     */
    public function beranda(string $noMeja): JsonResponse
    {
        // 1. Cari data meja berdasarkan nomor meja
        $meja = Meja::where('no_meja', $noMeja)->first();

        if (! $meja) {
            return $this->errorResponse('Meja tidak ditemukan', 404);
        }

        // 2. Kunci ID meja di dalam session server pembeli
        session(['id_meja' => $meja->id_meja]);

        // 3. Tarik seluruh kategori beserta daftar menu terikat berstatus 'tersedia'
        $kategoris = KategoriMenu::with(['menus' => function ($query) {
            $query->where('status_ketersediaan', 'tersedia');
        }])->get();

        return $this->successResponse([
            'meja'      => $meja,
            'kategoris' => $kategoris,
        ]);
    }

    /**
     * Menampilkan daftar menu berstatus 'tersedia' dengan penyaringan opsional by id_kategori.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request pembawa parameter filter id_kategori
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMenu(Request $request): JsonResponse
    {
        // 1. Validasi opsional parameter kategori jika disertakan
        $request->validate([
            'id_kategori' => ['sometimes', 'integer', 'exists:kategori_menu,id_kategori'],
        ]);

        // 2. Susun query pencarian menu tersedia
        $query = Menu::where('status_ketersediaan', 'tersedia')
            ->select('id_menu', 'nama_menu', 'deskripsi', 'harga', 'gambar_menu', 'jenis_menu');

        // 3. Filter berdasarkan kategori menu jika diisi
        if ($request->filled('id_kategori')) {
            $kategoriId = $request->id_kategori;
            $query->whereHas('kategoris', function ($q) use ($kategoriId) {
                $q->where('kategori_menu.id_kategori', $kategoriId);
            });
        }

        $menus = $query->get();

        return $this->successResponse($menus);
    }

    /**
     * Tampilkan detail rincian produk menu tertentu berdasarkan ID.
     *
     * @param  string  $id  ID menu target pencarian
     * @return \Illuminate\Http\JsonResponse
     */
    public function detailMenu(string $id): JsonResponse
    {
        $menu = Menu::select('id_menu', 'nama_menu', 'deskripsi', 'harga', 'gambar_menu', 'jenis_menu')
            ->find($id);

        if (! $menu) {
            return $this->errorResponse('Menu tidak ditemukan', 404);
        }

        return $this->successResponse($menu);
    }

    /**
     * Tampilkan isi keranjang belanja saat ini beserta akumulasi total harganya.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function keranjang(): JsonResponse
    {
        $keranjang  = session('keranjang', []);
        $totalHarga = array_sum(array_column($keranjang, 'subtotal'));

        return $this->successResponse([
            'keranjang'  => $keranjang,
            'totalHarga' => (int) $totalHarga,
        ]);
    }

    /**
     * Tambahkan item menu ke dalam keranjang belanja session API.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request pembawa id_menu, jumlah porsi, dan catatan
     * @return \Illuminate\Http\JsonResponse
     */
    public function tambahKeranjang(Request $request): JsonResponse
    {
        // 1. Validasi tipe data isian item baru
        $request->validate([
            'id_menu' => ['required', 'integer', 'exists:menu,id_menu'],
            'jumlah'  => ['required', 'integer', 'min:1', 'max:99'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $menu      = Menu::findOrFail($request->id_menu);
        $keranjang = session('keranjang', []);
        
        $idMenu    = (int) $request->id_menu;
        $jumlah    = (int) $request->jumlah;

        // 2. Akumulasikan kuantitas porsi jika item menu sudah ada di keranjang
        if (isset($keranjang[$idMenu])) {
            $keranjang[$idMenu]['jumlah']  += $jumlah;
            $keranjang[$idMenu]['subtotal'] = $keranjang[$idMenu]['harga'] * $keranjang[$idMenu]['jumlah'];
        } else {
            // 3. Masukkan item baru jika belum ada
            $keranjang[$idMenu] = [
                'id_menu'   => $idMenu,
                'nama_menu' => $menu->nama_menu,
                'harga'     => $menu->harga,
                'jumlah'    => $jumlah,
                'catatan'   => $request->catatan,
                'subtotal'  => $menu->harga * $jumlah,
            ];
        }

        // 4. Rekam pembaruan di session
        session(['keranjang' => $keranjang]);

        $totalHarga = array_sum(array_column($keranjang, 'subtotal'));

        return $this->successResponse([
            'keranjang'  => $keranjang,
            'totalHarga' => (int) $totalHarga,
        ], 'Item ditambahkan ke keranjang');
    }

    /**
     * Memperbarui kuantitas (jumlah porsi) item di keranjang belanja API.
     * Item akan dihapus otomatis dari list keranjang jika jumlah porsi disetel ke angka 0.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request pembawa id_menu dan jumlah porsi baru
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateKeranjang(Request $request): JsonResponse
    {
        $request->validate([
            'id_menu' => ['required', 'integer'],
            'jumlah'  => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $keranjang = session('keranjang', []);
        $idMenu    = (int) $request->id_menu;

        if (! isset($keranjang[$idMenu])) {
            return $this->errorResponse('Item tidak ditemukan di keranjang', 404);
        }

        // 1. Unset (hapus) jika kuantitas di-set 0
        if ((int) $request->jumlah === 0) {
            unset($keranjang[$idMenu]);
        } else {
            // 2. Perbarui kuantitas dan hitung ulang subtotal
            $keranjang[$idMenu]['jumlah']  = (int) $request->jumlah;
            $keranjang[$idMenu]['subtotal'] = $keranjang[$idMenu]['harga'] * $keranjang[$idMenu]['jumlah'];
        }

        session(['keranjang' => $keranjang]);

        $totalHarga = array_sum(array_column($keranjang, 'subtotal'));

        return $this->successResponse([
            'keranjang'  => $keranjang,
            'totalHarga' => (int) $totalHarga,
        ], 'Keranjang berhasil diupdate');
    }

    /**
     * Memperbarui instruksi/catatan khusus (notes) per item di keranjang belanja API.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request pembawa id_menu dan string catatan
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNotes(Request $request): JsonResponse
    {
        $request->validate([
            'id_menu' => ['required', 'integer'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $keranjang = session('keranjang', []);
        $idMenu    = (int) $request->id_menu;

        if (! isset($keranjang[$idMenu])) {
            return $this->errorResponse('Item tidak ditemukan di keranjang', 404);
        }

        // 1. Simpan catatan baru
        $keranjang[$idMenu]['catatan'] = $request->catatan;
        session(['keranjang' => $keranjang]);

        $totalHarga = array_sum(array_column($keranjang, 'subtotal'));

        return $this->successResponse([
            'keranjang'  => $keranjang,
            'totalHarga' => (int) $totalHarga,
        ], 'Catatan berhasil disimpan');
    }

    /**
     * Finalisasi pesanan (Checkout API): Menulis data pesanan dan rincian ke database.
     * Mengamankan data dengan memproses query di dalam transaksi database atomik (DB::transaction).
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request pembawa nama_konsumen
     * @return \Illuminate\Http\JsonResponse
     */
    public function storePesan(Request $request): JsonResponse
    {
        // 1. Pastikan nomor meja tersimpan di session
        if (! session('id_meja')) {
            return $this->errorResponse('Sesi meja tidak valid. Silakan scan QR Code kembali.', 422);
        }

        $keranjang = session('keranjang', []);

        // 2. Pastikan keranjang belanja tidak kosong
        if (empty($keranjang)) {
            return $this->errorResponse('Keranjang kosong. Tambahkan menu terlebih dahulu.', 422);
        }

        $request->validate([
            'nama_konsumen' => ['required', 'string', 'max:255'],
        ]);

        // 3. Generasi nomor unik pesanan kustom
        $noPesanan  = 'PS-' . date('YmdHis') . '-' . strtoupper(Str::random(4));
        $totalHarga = array_sum(array_column($keranjang, 'subtotal'));

        try {
            // 4. Proses simpan database dalam satu transaksi terisolasi
            DB::transaction(function () use ($noPesanan, $totalHarga, $keranjang, $request) {
                // A. Buat header transaksi
                Pesanan::create([
                    'no_pesanan'        => $noPesanan,
                    'id_user'           => null,
                    'id_meja'           => session('id_meja'),
                    'nama_konsumen'     => $request->nama_konsumen,
                    'total_harga'       => $totalHarga,
                    'status_pembayaran' => 'belum bayar', // State awal sebelum dialihkan ke payment gateway
                    'status_pesanan'    => 'menunggu konfirmasi',
                ]);

                // B. Buat detail item belanjaan
                foreach ($keranjang as $item) {
                    DetailPesanan::create([
                        'no_pesanan' => $noPesanan,
                        'id_menu'    => $item['id_menu'],
                        'jumlah'     => $item['jumlah'],
                        'catatan'    => $item['catatan'],
                        'subtotal'   => $item['subtotal'],
                    ]);
                }
            });
        } catch (\Throwable $e) {
            return $this->errorResponse('Gagal membuat pesanan, coba lagi.', 500);
        }

        // 5. Bersihkan keranjang belanja session
        session()->forget('keranjang');

        return $this->successResponse([
            'no_pesanan'  => $noPesanan,
            'total_harga' => (int) $totalHarga,
        ], 'Pesanan berhasil dibuat', 201);
    }

    /**
     * Cek status terbaru pesanan konsumen berdasarkan no_pesanan.
     *
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi
     * @return \Illuminate\Http\JsonResponse
     */
    public function statusPesanan(string $noPesanan): JsonResponse
    {
        $pesanan = Pesanan::select('no_pesanan', 'status_pesanan', 'status_pembayaran', 'tgl_pembayaran')
            ->find($noPesanan);

        if (! $pesanan) {
            return $this->errorResponse('Pesanan tidak ditemukan', 404);
        }

        return $this->successResponse([
            'no_pesanan'        => $pesanan->no_pesanan,
            'status_pesanan'    => $pesanan->status_pesanan,
            'status_pembayaran' => $pesanan->status_pembayaran,
            'tgl_pembayaran'    => $pesanan->tgl_pembayaran,
        ]);
    }

    /**
     * Membuat Invoice instan di platform payment gateway Xendit untuk transaksi terkait.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request pembawa no_pesanan target
     * @return \Illuminate\Http\JsonResponse Respon berisi tautan checkout payment link
     */
    public function bayar(Request $request): JsonResponse
    {
        $request->validate([
            'no_pesanan' => ['required', 'string'],
        ]);

        $pesanan = Pesanan::find($request->no_pesanan);

        if (! $pesanan) {
            return $this->errorResponse('Pesanan tidak ditemukan', 404);
        }

        // 1. Keamanan Akses: Tolak pembuatan invoice jika pesanan dinilai sudah lunas
        if ($pesanan->status_pembayaran === 'lunas') {
            return $this->errorResponse('Pesanan sudah lunas', 422);
        }

        try {
            // 2. Konfigurasi API Key Xendit
            Xendit::setApiKey(config('services.xendit.api_key'));

            // 3. Buat remote invoice link di platform Xendit
            $invoice = Invoice::create([
                'external_id'          => $pesanan->no_pesanan,
                'amount'               => $pesanan->total_harga,
                'payer_email'          => 'konsumen@kohvito.com',
                'description'          => 'Pembayaran #' . $pesanan->no_pesanan,
                'invoice_duration'     => 86400, // Aktif selama 24 jam
                'currency'             => 'IDR',
                'customer'             => ['given_names' => $pesanan->nama_konsumen],
            ]);

            return $this->successResponse([
                'invoice_url' => $invoice['invoice_url'],
            ], 'Invoice berhasil dibuat');
        } catch (\Throwable $e) {
            return $this->errorResponse('Gagal membuat invoice pembayaran: ' . $e->getMessage(), 500);
        }
    }
}
