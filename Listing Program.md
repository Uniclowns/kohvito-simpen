# Listing Program — Sistem Pemesanan Kohvito

Seluruh potongan kode di bawah diambil langsung dari source code proyek (bukan contoh fiktif).
Setiap listing sudah diringkas ke bagian inti agar pas untuk lampiran laporan; path file asli
dicantumkan bila ingin melihat versi lengkapnya.

---

## 2. Implementasi Scan QR Code

**Sumber:** `app/Http/Controllers/BerandaKonsumenController.php` — method `index()`
**Route:** `GET /{noMeja}` (publik, tanpa login)

```php
/**
 * Tampilkan halaman utama katalog menu konsumen (Landing Page scan QR).
 * URL yang dipindai dari QR Code memuat parameter nomor meja, misal: /M12
 */
public function index(string $noMeja, Request $request)
{
    // 1. Validasi identitas meja hasil scan QR terhadap database
    $meja = Meja::firstWhere('no_meja', $noMeja);

    if (! $meja) {
        abort(404);
    }

    // 2. Inisialisasi session nomor meja sementara milik konsumen
    session([
        'id_meja'    => $meja->id_meja,
        'id_meja_no' => $meja->no_meja,
    ]);

    // 3. Muat data kategori dan seluruh menu berstatus 'Tersedia' dari database
    $kategoris = KategoriMenu::orderBy('id_kategori')->get();
    $menus = Menu::with('kategoris')
        ->where('status_ketersediaan', 'Tersedia')
        ->orderBy('id_menu')
        ->get();

    // 4. Arahkan konsumen ke halaman katalog menu (Blade template)
    return view('konsumen.beranda', compact('meja', 'kategoris', 'menus'));
}
```

---

## 3. Implementasi Pemesanan

**Sumber:** `app/Http/Controllers/KeranjangKonsumenController.php` — method `storePesan()`
**Route:** `POST /{noMeja}/checkout`

```php
/**
 * Memproses checkout: menulis data pesanan & detail pesanan ke database.
 * Menggunakan DB::transaction agar penyimpanan bersifat atomik.
 */
public function storePesan(CheckoutCartRequest $request, string $noMeja): RedirectResponse
{
    // 1. Ambil seluruh item keranjang belanja konsumen dari session
    $keranjang = $this->getKeranjang();

    if (empty($keranjang)) {
        return redirect()->route('konsumen.keranjang', ['noMeja' => $noMeja])
            ->with('error', 'Keranjang kosong. Tambahkan menu terlebih dahulu.');
    }

    // 2. Bangkitkan nomor pesanan unik dan hitung total tagihan (subtotal + PPN 11%)
    $noPesanan = 'PS-'.date('YmdHis').'-'.strtoupper(Str::random(4));
    $subtotalHarga = array_sum(array_column($keranjang, 'subtotal'));
    $ppnAmount  = (int) round($subtotalHarga * 0.11);
    $totalHarga = $subtotalHarga + $ppnAmount;

    // 3. Simpan pesanan dan rinciannya dalam satu transaksi database
    DB::transaction(function () use ($noPesanan, $totalHarga, $keranjang, $request) {
        // A. Insert baris header ke tabel pesanan
        Pesanan::create([
            'no_pesanan'        => $noPesanan,
            'id_meja'           => $this->resolveIdMeja(),
            'nama_konsumen'     => $request->nama_konsumen,
            'total_harga'       => $totalHarga,
            'status_pembayaran' => 'menunggu',
            'status_pesanan'    => 'menunggu konfirmasi',
        ]);

        // B. Insert setiap item keranjang ke tabel detail_pesanan
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

    // 4. Kosongkan keranjang lalu arahkan konsumen ke halaman pembayaran
    $this->forgetKeranjang();
    session(['no_pesanan_baru' => $noPesanan]);

    return redirect()->route('konsumen.pembayaran', $noPesanan);
}
```

---

## 4. Implementasi Pembayaran

**Sumber:** `app/Http/Controllers/BayarController.php` — method `generateMidtransQris()` dan `callbackMidtrans()`
**Route:** `GET /pembayaran/{noPesanan}` dan `POST /{noMeja}/bayar/callback`

### a. Memanggil API payment gateway (Midtrans) untuk membangkitkan QRIS

```php
/**
 * Menghubungi Midtrans Core API untuk membangkitkan QRIS dinamis.
 * URL QR hasil charge disimpan ke tabel pesanan.
 */
private function generateMidtransQris(Pesanan $pesanan): void
{
    // 1. Set konfigurasi kredensial SDK Midtrans
    Config::$serverKey    = config('services.midtrans.server_key');
    Config::$isProduction = (bool) config('services.midtrans.is_production');

    // 2. Siapkan parameter charge transaksi QRIS
    $params = [
        'payment_type' => 'qris',
        'transaction_details' => [
            'order_id'     => $pesanan->no_pesanan,
            'gross_amount' => (int) $pesanan->total_harga,
        ],
        'qris' => ['acquirer' => 'gopay'],
        'customer_details' => ['first_name' => $pesanan->nama_konsumen],
    ];

    // 3. Kirim request charge ke API Midtrans
    $response = CoreApi::charge($params);

    // 4. Simpan URL QR Code dan ID transaksi ke database lokal
    $pesanan->update([
        'midtrans_transaction_id' => $response->transaction_id ?? null,
        'qr_url' => $this->extractQrUrl($response),
    ]);
}
```

### b. Menangani callback (webhook) konfirmasi pembayaran dari payment gateway

```php
/**
 * Memproses callback dari Midtrans: verifikasi signature,
 * lalu perbarui status pembayaran menjadi lunas jika settlement.
 */
private function callbackMidtrans(Request $request, array $payload): JsonResponse
{
    $serverKey = config('services.midtrans.server_key');

    // 1. Verifikasi signature key (sha512) — memastikan payload asli dari Midtrans
    $signature = hash('sha512',
        $payload['order_id']
        .($payload['status_code'] ?? '')
        .($payload['gross_amount'] ?? '')
        .$serverKey
    );

    if ($serverKey && isset($payload['signature_key']) && $signature !== $payload['signature_key']) {
        return response()->json(['status' => 'rejected'], 401);
    }

    // 2. Proses hanya jika status transaksi lunas ('capture' / 'settlement')
    $status = $payload['transaction_status'] ?? '';
    if (! in_array($status, ['capture', 'settlement'], true)) {
        return response()->json(['status' => 'ignored'], 200);
    }

    $pesanan = Pesanan::find($payload['order_id']);

    if (! $pesanan) {
        return response()->json(['status' => 'ignored'], 200);
    }

    // 3. Perbarui status pembayaran pesanan menjadi lunas secara otomatis
    $pesanan->markAsPaid();

    return response()->json(['status' => 'ok']);
}
```

---

## 5. Implementasi Tracking Pesanan

**Sumber:** `app/Http/Controllers/PesananController.php` — method `lacak()` dan `status()`
**Route:** `GET /{noMeja}/lacak/{noPesanan}` dan `GET /pesanan/{noPesanan}/status`

### a. Menampilkan halaman timeline pelacakan pesanan

```php
/**
 * Tampilkan halaman pelacakan status timeline progres dapur untuk pesanan tertentu.
 */
public function lacak(string $noMeja, string $noPesanan): View
{
    // 1. Query data pesanan beserta relasi item hidangan dan meja berdasarkan nomor pesanan
    $pesanan = Pesanan::with(['detailPesanan.menu', 'meja'])->find($noPesanan);

    if (! $pesanan) {
        abort(404);
    }

    // 2. Kirim data pesanan ke view Blade untuk dirender sebagai indikator
    //    pelacakan visual (Menunggu Konfirmasi / Diproses / Selesai)
    return view('konsumen.lacak', compact('pesanan', 'noMeja'));
}
```

### b. Endpoint polling status (dipanggil berkala oleh JavaScript agar tampilan real-time)

```php
/**
 * Polling Endpoint JSON: status pembayaran & progres dapur terkini.
 */
public function status(string $noPesanan): JsonResponse
{
    $pesanan = Pesanan::find($noPesanan);

    if (! $pesanan) {
        return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
    }

    // Kembalikan status terbaru ke JavaScript client untuk memperbarui indikator tracking
    return response()->json([
        'no_pesanan'        => $pesanan->no_pesanan,
        'status_pesanan'    => $pesanan->status_pesanan,
        'status_pembayaran' => $pesanan->status_pembayaran,
    ]);
}
```

---

## 6. Implementasi Terima Pesanan

**Sumber:** `app/Http/Controllers/KelolaPesananController.php` — method `updateStatus()`
**Route:** `PUT /kasir/pesanan/{noPesanan}/status` (middleware: auth + role kasir)

```php
/**
 * Mengubah status pengerjaan pesanan menggunakan pola State Machine.
 * Saat kasir menekan tombol Terima Pesanan, status berubah menjadi 'diproses'.
 */
public function updateStatus(Request $request, string $noPesanan): RedirectResponse
{
    $pesanan = Pesanan::where('no_pesanan', $noPesanan)->firstOrFail();

    // 1. Alur pay-first: pesanan yang belum lunas tidak boleh diproses dapur
    if ($pesanan->status_pembayaran !== 'lunas') {
        return back()->with('error', 'Pesanan belum dibayar — tidak dapat diproses.');
    }

    // 2. Kamus aturan transisi status — mencegah lompatan status
    $transitions = [
        'menunggu konfirmasi' => 'diproses',
        'diproses'            => 'selesai',
    ];

    $nextStatus = $transitions[$pesanan->status_pesanan] ?? null;

    if (! $nextStatus) {
        return back()->with('error', 'Status pesanan tidak dapat diubah.');
    }

    // 3. Update status pesanan di database — perubahan ini otomatis
    //    tersinkronisasi ke tampilan tracking pesanan milik konsumen
    $pesanan->status_pesanan = $nextStatus;
    $pesanan->save();

    $message = $nextStatus === 'diproses'
        ? 'Pesanan diterima dan sedang diproses.'
        : 'Pesanan telah selesai.';

    return redirect()->route('kasir.pesanan.index')->with('success', $message);
}
```

---

## 7. Implementasi Kelola Pengguna Kasir

**Sumber:** `app/Http/Controllers/KelolaPenggunaKasirController.php` — method `storePenggunaKasir()`
**Route:** `POST /admin/pengguna-kasir` (middleware: auth + role admin)

```php
/**
 * Memvalidasi dan menyimpan akun Kasir baru ke database
 * dengan password ter-hashing otomatis.
 */
public function storePenggunaKasir(Request $request): RedirectResponse
{
    // 1. Validasi form isian akun kasir baru beserta pesan kesalahan
    $request->validate([
        'nama_lengkap' => 'required|string|max:255',
        'username'     => 'required|string|min:6|max:255|unique:users,username',
        'password'     => 'required|string|min:9',
    ], [
        'nama_lengkap.required' => 'Nama lengkap pengguna wajib diisi',
        'username.required'     => 'Username pengguna wajib diisi',
        'username.min'          => 'Username minimal 6 karakter',
        'username.unique'       => 'Username sudah digunakan, silakan pilih yang lain',
        'password.required'     => 'Password wajib diisi',
        'password.min'          => 'Password harus lebih dari 8 karakter',
    ]);

    // 2. Simpan kasir baru ke tabel users dengan hak akses (role) kasir.
    //    Password dienkripsi satu arah menggunakan hashing bcrypt Laravel.
    User::create([
        'id_role'      => 2, // ID Role 2 mewakili peran Kasir
        'nama_lengkap' => $request->nama_lengkap,
        'username'     => $request->username,
        'password'     => bcrypt($request->password),
    ]);

    return redirect()->route('admin.pengguna-kasir.index')
        ->with('success', 'Akun kasir berhasil ditambahkan.');
}
```

---

## 8. Implementasi Cetak Pesanan

**Sumber:** `app/Http/Controllers/KelolaPesananController.php` — method `cetakPesanan()`
**Route:** `GET /kasir/pesanan/{noPesanan}/cetak` (middleware: auth + role kasir)

```php
/**
 * Tampilkan struk pesanan sebagai halaman HTML siap-cetak (format 80mm).
 * Halaman memanggil window.print() otomatis sehingga kasir langsung
 * melihat dialog cetak ke printer termal.
 */
public function cetakPesanan(string $noPesanan): View
{
    // 1. Muat rincian pesanan (item, kuantitas, catatan khusus)
    //    beserta relasi meja berdasarkan nomor transaksi
    $pesanan = Pesanan::with(['meja', 'detailPesanan.menu'])
        ->where('no_pesanan', $noPesanan)
        ->firstOrFail();

    // 2. Teruskan data ke view khusus cetak untuk dirender
    //    menjadi format struk kasir siap-print
    return view('kasir.cetak-pesanan-print', compact('pesanan'));
}
```

Pada sisi view `kasir/cetak-pesanan-print.blade.php`, perintah cetak dipicu otomatis:

```html
<script>
    // Panggil dialog cetak browser secara otomatis saat halaman struk termuat
    window.addEventListener('load', function () {
        window.print();
    });
</script>
```

---

## 9. Implementasi Cetak Laporan

**Sumber:** `app/Http/Controllers/BerandaAdminController.php` — method `cetakLaporanKasir()`
**Route:** `GET /admin/laporan/cetak` (middleware: auth + role admin)

```php
/**
 * Membuat dokumen cetak laporan penjualan dalam format PDF
 * berdasarkan filter rentang tanggal yang dipilih admin.
 */
public function cetakLaporanKasir(Request $request): HttpResponse
{
    // 1. Identifikasi rentang waktu laporan (default: hari ini)
    $tanggalMulai = $request->tanggal_mulai
        ? Carbon::parse($request->tanggal_mulai)->startOfDay()
        : Carbon::today()->startOfDay();

    $tanggalSelesai = $request->tanggal_selesai
        ? Carbon::parse($request->tanggal_selesai)->endOfDay()
        : Carbon::today()->endOfDay();

    // 2. Query akumulasi transaksi lunas pada rentang tanggal terpilih
    //    beserta relasi meja dan kasir dari database MySQL
    $pesanan = Pesanan::with(['meja', 'user'])
        ->where('status_pembayaran', 'lunas')
        ->whereBetween('tgl_pembayaran', [$tanggalMulai, $tanggalSelesai])
        ->get();

    // 3. Nama file menyertakan periode agar unduhan tidak tertukar
    $namaFile = 'laporan-kasir-'.$tanggalMulai->format('Y-m-d');
    if (! $tanggalMulai->isSameDay($tanggalSelesai)) {
        $namaFile .= '_sd_'.$tanggalSelesai->format('Y-m-d');
    }

    // 4. Render template Blade menjadi file PDF menggunakan library
    //    DomPDF, siap diunduh oleh admin sebagai bahan evaluasi
    return Pdf::loadView('admin.laporan-kasir-pdf', compact('pesanan', 'tanggalMulai', 'tanggalSelesai'))
        ->download($namaFile.'.pdf');
}
```
