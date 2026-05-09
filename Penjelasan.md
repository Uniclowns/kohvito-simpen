# 📖 Buku Pintar Kode SIMPEN (Kohvito)

> **Dokumen ini dibuat khusus untuk keperluan presentasi.** Berisi penjelasan mendalam (logika dan baris kode) dari setiap fitur yang telah dikembangkan di aplikasi SIMPEN (Sistem Informasi Pemesanan Menu). Dokumen ini akan terus diperbarui seiring berjalannya pengembangan (Issue yang sudah *Closed*).

---

## 📑 Daftar Isi
1. [Milestone 1: Database & Migrations](#1-milestone-1-database--migrations-issue-3---9)
2. [Milestone 2: MVC Skeleton & Middleware](#2-milestone-2-mvc-skeleton--middleware-issue-10---12)
3. [Milestone 4: Backend Admin & Autentikasi](#3-milestone-4-backend-admin--autentikasi-issue-14---26)
4. [Milestone 5: Backend Kasir & Alur Konsumen](#4-milestone-5-backend-kasir--alur-konsumen-issue-28---34)
5. [Milestone 6: Modul Konsumen & Payment](#5-milestone-6-modul-konsumen--payment-issue-38---41)
6. [Dependencies & Library Eksternal](#6-dependencies--library-eksternal)

---

## 1. Milestone 1: Database & Migrations (Issue #3 - #9)

*Migration* adalah versi kontrol untuk database. Daripada membuat tabel secara manual di phpMyAdmin, kita menuliskannya di kode agar struktur database bisa direplikasi di mana saja.

### A. Contoh Logika Migration (Tabel Pesanan)
Tabel ini merupakan urat nadi transaksi. Terdapat beberapa hal unik pada pembuatannya:

```php
Schema::create('pesanan', function (Blueprint $table) {
    // 1. Kustomisasi Primary Key: Kita tidak menggunakan ID auto-increment biasa,
    // melainkan 'no_pesanan' yang bertipe string (VARCHAR) sebagai kode unik transaksi.
    $table->string('no_pesanan', 50)->primary();

    // 2. Foreign Keys (Relasi ke tabel lain):
    // unsignedBigInteger digunakan agar cocok dengan id di tabel users dan meja.
    // nullable() pada id_user karena saat pesanan pertama kali dibuat oleh konsumen, kasir belum mengonfirmasi.
    $table->unsignedBigInteger('id_user')->nullable();
    $table->unsignedBigInteger('id_meja');

    // 3. Tipe Data ENUM:
    // ENUM membatasi nilai yang boleh masuk ke database agar tidak ada salah ketik.
    $table->enum('status_pembayaran', ['belum dibayar', 'lunas', 'gagal'])->default('belum dibayar');
    $table->enum('status_pesanan', ['menunggu konfirmasi', 'diproses', 'selesai', 'dibatalkan'])->default('menunggu konfirmasi');

    // 4. Aturan Relasi (Constraints):
    // Jika data di tabel 'meja' dihapus, apa yang terjadi pada pesanan? 'restrict' artinya
    // database akan menolak penghapusan meja jika meja tersebut masih memiliki riwayat pesanan.
    $table->foreign('id_user')->references('id_users')->on('users')->onDelete('set null');
    $table->foreign('id_meja')->references('id_meja')->on('meja')->onDelete('restrict');
});
```

---

## 2. Milestone 2: MVC Skeleton & Middleware (Issue #10 - #12)

Bagian ini adalah fondasi aplikasi Laravel kita. Kita menghubungkan tabel database ke model (Object-Relational Mapping/ORM), membuat jalur akses (*routes*), dan pintu penjaga (*middleware*).

### A. Model Eloquent & Relasi (Issue #10)
Model `User` adalah representasi dari tabel `users`. Ini cara kita mendefinisikan agar aplikasi paham relasi antar data.

```php
class User extends Authenticatable
{
    // protected $primaryKey memberi tahu Laravel bahwa primary key kita BUKAN 'id', melainkan 'id_users'
    protected $primaryKey = 'id_users';

    // public $timestamps = false; digunakan karena kita tidak memakai field 'created_at' dan 'updated_at' bawaan
    public $timestamps = false;

    // Relasi BelongsTo: "Satu User memiliki SATU Role"
    // Parameter: (Model Tujuan, Foreign Key di tabel ini, Primary Key di tabel tujuan)
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }
}
```

### B. Middleware CheckRole (Issue #12)
Bagaimana cara kita memisahkan halaman Admin dan Kasir? Kita membuat satpam pengecekan bernama Middleware.

```php
public function handle(Request $request, Closure $next, string $role): Response
{
    $user = $request->user(); // Mengambil data user yang sedang login

    // Jika user belum login atau tidak punya role, tendang (Keluarkan Error 403 Forbidden)
    if (! $user || ! $user->role) {
        abort(403, 'Akses ditolak.');
    }

    // Perbandingan Role: strtolower mengubah teks jadi huruf kecil (misal 'Admin' jadi 'admin').
    // Jika role user tidak sama dengan role yang diizinkan route, maka tendang.
    if (strtolower($user->role->nama_role) !== strtolower($role)) {
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }

    // Jika lolos pengecekan, persilakan user melanjutkan perjalanannya (akses halaman)
    return $next($request);
}
```

### C. Routing Terproteksi (Issue #12)
Pengaplikasian Middleware tadi ke dalam *Routes* (`routes/web.php`):

```php
// Route::prefix('admin') -> semua rute di dalam grup ini diawali dengan /admin/ (misal: /admin/beranda)
// ->middleware(['auth', 'role:admin']) -> Terapkan satpam 'auth' (harus login) DAN satpam 'role' (hanya admin)
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/', [BerandaAdminController::class, 'index'])->name('beranda');
    Route::get('/menu', [KelolaMenuController::class, 'index'])->name('menu.index');
});
```

---

## 3. Milestone 4: Backend Admin & Autentikasi (Issue #14 - #26)

Tahapan di mana kita benar-benar menulis logika pemrograman (CRUD, Filter, Upload File, Autentikasi).

### A. Autentikasi Login (Issue #14)
File: `AuthController.php`

```php
public function store(Request $request)
{
    // 1. Auth::attempt memeriksa ke tabel 'users' apakah username & password cocok (Otomatis nge-cek hash/enkripsi)
    if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
        
        // 2. Jika sukses, regenerasi session id untuk mencegah serangan Session Fixation (Hacker mencuri token)
        $request->session()->regenerate();

        // 3. Ambil nama role dari user yang baru login
        $role = strtolower(Auth::user()->role->nama_role);

        // 4. Lemparkan user ke dashboard yang sesuai (Direct routing berbasis peran)
        if ($role === 'admin') {
            return redirect()->route('admin.beranda');
        }

        if ($role === 'kasir') {
            return redirect()->route('kasir.beranda');
        }
    }

    // 5. Jika gagal (password/username salah), kembalikan ke halaman login dengan pesan error
    return back()->withErrors(['loginError' => 'Username atau password salah.']);
}
```

### B. Upload & Update Gambar Menu (Issue #18)
File: `KelolaMenuController.php`

```php
public function updateMenu(Request $request, string $id)
{
    $menu = Menu::findOrFail($id); // Cari menu, kalau tidak ketemu otomatis Error 404

    // 1. Simpan nama gambar lama
    $gambarFilename = $menu->gambar_menu;

    // 2. Cek apakah ada file gambar baru yang di-upload?
    if ($request->hasFile('gambar_menu')) {
        // 3. Jika dulu menu ini punya gambar, HAPUS gambar lama dari hard disk server agar storage tidak penuh
        if ($gambarFilename) {
            Storage::disk('public')->delete('menu-images/' . $gambarFilename);
        }
        
        // 4. Simpan gambar baru ke folder 'storage/app/public/menu-images'
        $stored = $request->file('gambar_menu')->store('menu-images', 'public');
        
        // 5. Ambil hanya nama file-nya (basename) untuk disimpan ke database
        $gambarFilename = basename($stored);
    }

    // Lanjutkan simpan ke database...
    $menu->gambar_menu = $gambarFilename;
    $menu->save();
}
```

### C. Integrasi Generate QR Code Meja (Issue #24)
File: `KelolaMejaController.php`

Fitur ini otomatis menggambar QR Code setiap admin menambahkan Meja Baru. Menggunakan library `simplesoftwareio/simple-qrcode`.

```php
public function storeMeja(Request $request)
{
    // 1. Simpan data meja ke DB (QR Code di-set null dulu karena butuh nomor meja)
    $meja = Meja::create(['no_meja' => $request->no_meja, 'qr_code' => null]);

    // 2. Buat URL link yang akan di-embed ke dalam gambar QR Code (misal: https://kohvito.com/M-01)
    $qrUrl = url('/' . $meja->no_meja);
    
    // 3. Nama file QR Code yang akan disimpan (misal: qrcodes/M-01.png)
    $qrFilename = 'qrcodes/' . $meja->no_meja . '.png';
    
    // 4. Proses melukis QR Code (Format PNG, Ukuran 300x300 pixel)
    $qrContent = QrCode::format('png')->size(300)->generate($qrUrl);
    
    // 5. Simpan gambar hasil lukisan tadi ke dalam server
    Storage::disk('public')->put($qrFilename, $qrContent);

    // 6. Update kolom qr_code di database dengan lokasi file
    $meja->update(['qr_code' => $qrFilename]);
}
```

### D. Laporan Keuangan (Filter Tanggal & Aggregasi) (Issue #26)
File: `LaporanKeuanganController.php`

Fitur ini menggunakan teknik **Query Builder Aggregation** tingkat lanjut untuk mencari Menu Terlaris dan merangkum omzet.

```php
public function index(Request $request)
{
    // 1. Tentukan batasan waktu (Start Of Day -> Pukul 00:00:00)
    $tanggalMulai = $request->input('tanggal_mulai')
        ? Carbon::parse($request->input('tanggal_mulai'))->startOfDay()
        : Carbon::today()->startOfDay();

    // End Of Day -> Pukul 23:59:59
    $tanggalSelesai = $request->input('tanggal_selesai')
        ? Carbon::parse($request->input('tanggal_selesai'))->endOfDay()
        : Carbon::today()->endOfDay();

    // 2. Tarik semua pesanan beserta relasi meja dan detail menu-nya
    $pesanans = Pesanan::with(['meja', 'detailPesanan.menu'])
        ->where('status_pembayaran', 'lunas')
        ->whereBetween('tgl_pembayaran', [$tanggalMulai, $tanggalSelesai])
        ->get();

    // 3. Agregasi Koleksi (Collection Methods Laravel): Hitung jumlah total uang secara instan dari koleksi tadi
    $totalOmzet = $pesanans->sum('total_harga');

    // 4. Query Menu Terlaris: 
    // whereHas: "Ambil detail pesanan YANG MANA pesanannya itu berstatus lunas di tanggal tersebut"
    $menuTerlaris = DetailPesanan::with('menu')
        ->whereHas('pesanan', function ($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->where('status_pembayaran', 'lunas')
              ->whereBetween('tgl_pembayaran', [$tanggalMulai, $tanggalSelesai]);
        })
        ->selectRaw('id_menu, SUM(jumlah) as total_terjual') // Hitung total unit yang terjual
        ->groupBy('id_menu')                                 // Kelompokkan berdasarkan menu
        ->orderByDesc('total_terjual')                       // Urutkan dari yang terbanyak
        ->limit(5)                                           // Ambil Top 5 saja
        ->get();

    // ... Passing ke view untuk di render
}
```

---

## 4. Milestone 5: Backend Kasir & Alur Konsumen (Issue #28 - #34)

Tahapan di mana kita melengkapi alur manajemen toko (buka/tutup) dan pemrosesan pesanan oleh Kasir.

### A. Fitur Buka/Tutup Pesanan (Issue #28)
File: `CheckOrderStatus.php` (Middleware) & `BerandaAdminController.php`

Fitur ini menggunakan sistem **Caching** bawaan Laravel untuk menyimpan status apakah toko sedang buka atau tutup secara global, tanpa harus membuat tabel baru di database. Karena hanya berisi nilai tunggal (`buka` / `tutup`), menyimpan di memori/cache jauh lebih cepat.

```php
// Di dalam CheckOrderStatus.php (Middleware)
public function handle(Request $request, Closure $next): Response
{
    // Cek di memori Cache, apakah kunci 'order_status' bernilai 'tutup'?
    // Jika tidak ada kunci tersebut, anggap nilainya 'buka' (default).
    if (Cache::get('order_status', 'buka') === 'tutup') {
        // Jika tutup, alihkan konsumen ke halaman pemberitahuan
        return redirect()->route('konsumen.order-tutup');
    }

    return $next($request);
}

// Di dalam BerandaAdminController.php
public function toggleOrderStatus()
{
    $current = Cache::get('order_status', 'buka');
    $new = $current === 'buka' ? 'tutup' : 'buka';
    
    // Cache::forever menyimpan nilai ini selamanya (sampai diubah lagi)
    Cache::forever('order_status', $new);

    return redirect()->route('admin.beranda')->with('success', 'Status diubah.');
}
```

### B. Kelola Pesanan Aktif & State Machine (Issue #32)
File: `KelolaPesananController.php`

Fitur ini menggunakan logika *State Machine* (Mesin Kondisi) sederhana menggunakan Array. Alur pesanan wajib berurutan: `menunggu konfirmasi` -> `diproses` -> `selesai`. Hal ini mencegah Kasir mengubah pesanan yang baru masuk langsung menjadi selesai tanpa diproses.

```php
public function updateStatus(Request $request, string $noPesanan)
{
    $pesanan = Pesanan::where('no_pesanan', $noPesanan)->firstOrFail();

    // Kamus transisi (State Machine)
    // Jika statusnya A, maka harus berubah menjadi B.
    $transitions = [
        'menunggu konfirmasi' => 'diproses',
        'diproses'            => 'selesai',
    ];

    // Ambil status selanjutnya berdasarkan status saat ini.
    // Jika tidak ada di kamus, kembalikan null.
    $nextStatus = $transitions[$pesanan->status_pesanan] ?? null;

    if (! $nextStatus) {
        return back()->with('error', 'Status pesanan tidak dapat diubah.');
    }

    // Lakukan pembaruan status
    $pesanan->status_pesanan = $nextStatus;
    $pesanan->save();
}
```

### C. Pencarian Berbasis Closure (Issue #34)
File: `HistoriPesananController.php`

Pada halaman Histori Pesanan, Kasir dapat mencari nama konsumen atau nomor pesanan. Laravel Query Builder menggunakan *Closure* (Fungsi Anonim) agar pencarian di-grouping di dalam kurung `(...)` pada eksekusi SQL query, menghindari bentrok dengan kondisi `WHERE` utamanya.

```php
public function index(Request $request)
{
    // ... inisialisasi $today & $search keyword
    $query = Pesanan::with(['meja'])
        ->where('status_pesanan', 'selesai')
        ->whereDate('tgl_pembayaran', $today);

    // Jika ada keyword pencarian, jalankan closure pembungkus ini
    if ($search) {
        $query->where(function ($q) use ($search) {
            // "use ($search)" mengizinkan fungsi anonim ini untuk membaca variabel $search
            // Menghasilkan SQL: AND (no_pesanan LIKE '%X%' OR nama_konsumen LIKE '%X%')
            $q->where('no_pesanan', 'like', "%{$search}%")
              ->orWhere('nama_konsumen', 'like', "%{$search}%");
        });
    }

    // Eksekusi query
    $pesanans = $query->orderBy('tgl_pembayaran', 'desc')->get();
}
```

---

## 5. Milestone 6: Modul Konsumen & Payment (Issue #38 - #41)

Tahapan di mana kita berfokus pada pengalaman Konsumen, mulai dari menambahkan menu ke keranjang hingga proses pembayaran otomatis menggunakan *Payment Gateway*.

### A. Keranjang Belanja Berbasis Session (Issue #39)
File: `KeranjangKonsumenController.php`

Keranjang belanja disimpan sementara di dalam *Session* browser konsumen, sehingga tidak membebani database sebelum konsumen benar-benar *checkout*.

```php
public function storeTambahKeranjang(Request $request)
{
    // ... validasi menu ...
    $keranjang = session('keranjang', []);
    $idMenu    = (int) $request->id_menu;

    // Jika menu sudah ada di keranjang, tambahkan jumlahnya. Jika belum, buat item baru.
    if (isset($keranjang[$idMenu])) {
        $keranjang[$idMenu]['jumlah']   += $request->jumlah;
        $keranjang[$idMenu]['subtotal']  = $keranjang[$idMenu]['harga'] * $keranjang[$idMenu]['jumlah'];
    } else {
        $keranjang[$idMenu] = [
            // ... assign atribut menu ...
            'jumlah'    => $request->jumlah,
            'subtotal'  => $menu->harga * $request->jumlah,
        ];
    }

    // Simpan kembali array keranjang ke dalam session
    session(['keranjang' => $keranjang]);
}
```

### B. Integrasi Payment Gateway Xendit (Issue #41)
File: `BayarController.php`

Pembayaran tidak lagi dikonfirmasi manual oleh Kasir, melainkan otomatis oleh Xendit menggunakan *Webhook* / Callback.

```php
// 1. Membuat Invoice (Konsumen diarahkan ke halaman pembayaran Xendit)
public function bayar(Request $request)
{
    // ... inisialisasi API key ...
    Xendit::setApiKey(config('services.xendit.api_key'));

    $invoice = Invoice::create([
        'external_id'          => $pesanan->no_pesanan,
        'amount'               => $pesanan->total_harga,
        'description'          => 'Pembayaran pesanan ' . $pesanan->no_pesanan,
        'invoice_duration'     => 86400, // Aktif 24 jam
        // ... (konfigurasi redirect url)
    ]);

    // Arahkan konsumen ke link invoice Xendit
    return redirect($invoice['invoice_url']);
}

// 2. Menerima Callback/Webhook dari Xendit secara background
public function callback(Request $request)
{
    $payload = $request->all();

    // Jika status dari Xendit adalah "PAID" (Sudah Dibayar)
    if (isset($payload['status']) && $payload['status'] === 'PAID') {
        $pesanan = Pesanan::find($payload['external_id']);
        
        // Ubah status pesanan secara otomatis tanpa campur tangan kasir
        $pesanan->update([
            'status_pembayaran' => 'lunas',
            'status_pesanan'    => 'menunggu konfirmasi',
            'tgl_pembayaran'    => now(),
        ]);
    }
    
    return response()->json(['status' => 'ok']);
}
```

---

## 6. Dependencies & Library Eksternal

Aplikasi ini menggunakan beberapa alat bantu (library) tambahan yang diinstal melalui `Composer` (untuk PHP/Laravel) dan `NPM` (untuk frontend). Berikut adalah daftar dependensi beserta kegunaannya:

### A. Dependensi PHP / Backend (composer.json)
- **`barryvdh/laravel-dompdf`**: Digunakan untuk mengubah tampilan HTML (view) menjadi file PDF. Berguna pada fitur **Export PDF** di Laporan Keuangan agar data yang tampil di layar bisa diunduh dalam format dokumen resmi.
- **`rap2hpoutre/fast-excel`**: Digunakan untuk fitur **Export Excel** pada Laporan Keuangan. Library ini dipilih karena performanya yang sangat cepat dan ramah memori (tidak memakan banyak RAM) saat melakukan export data laporan yang jumlahnya ratusan hingga ribuan baris.
- **`simplesoftwareio/simple-qrcode`**: Digunakan untuk menghasilkan/melukis **QR Code** secara otomatis saat Admin membuat Meja baru. QR Code ini nantinya digunakan oleh pelanggan untuk melakukan *scan* dan memesan menu.
- **`xendit/xendit-php`**: Digunakan sebagai SDK (Software Development Kit) resmi untuk mengintegrasikan layanan **Payment Gateway Xendit**. Memudahkan pembuatan *Invoice* otomatis saat konsumen *checkout* sehingga mereka dapat membayar melalui Virtual Account (VA), E-Wallet, atau QRIS dengan verifikasi instan.
- **`fakerphp/faker`**: (Dependensi Dev) Digunakan saat melakukan *Database Seeding* untuk membuat data palsu/dummy yang realistis (seperti nama orang, alamat, nomor telepon) secara acak, guna keperluan *testing* dan demonstrasi.
- **`laravel/framework`** & **`laravel/tinker`**: Core framework Laravel beserta fitur Tinker untuk berinteraksi dengan database melalui command line (CLI) PHP.
- **`phpunit/phpunit`**, **`mockery/mockery`**, **`nunomaduro/collision`**: (Dependensi Dev) Library yang digunakan Laravel untuk keperluan Testing (Unit/Feature Test) dan pelaporan error.

### B. Dependensi Frontend (package.json)
- **`tailwindcss`** & **`@tailwindcss/vite`**: Framework CSS yang digunakan untuk mendesain antarmuka/UI aplikasi secara cepat menggunakan *utility classes*. Plugin Vite digunakan untuk mengintegrasikan Tailwind versi 4 ke dalam proses *build* Vite.
- **`vite`** & **`laravel-vite-plugin`**: Vite adalah modul *bundler* yang sangat cepat untuk memproses file CSS dan JavaScript. Plugin Laravel digunakan agar aset-aset frontend terhubung secara otomatis (Hot Module Replacement) dengan backend Laravel.
- **`autoprefixer`** & **`postcss`**: Alat bantu CSS. Digunakan bersama TailwindCSS agar *styling* yang ditulis bisa kompatibel dengan berbagai macam jenis browser (Chrome, Safari, Firefox) dengan menambahkan *prefix* vendor secara otomatis.
- **`concurrently`**: Digunakan di skrip `npm run dev` agar developer bisa menjalankan beberapa *command* sekaligus dalam satu terminal (seperti menjalankan `php artisan serve`, Vite, dan Queue Worker secara berbarengan).

> **Catatan Presentasi Tambahan:** Library eksternal sangat penting dicatat karena jika aplikasi di-deploy (diunggah) ke server *hosting* atau *VPS*, pengguna/developer lain harus menjalankan perintah `composer install` dan `npm install` agar library-library ini terunduh kembali dan aplikasi bisa berjalan sempurna.

---
*(Dokumen ini akan terus di-update seiring berjalannya proses pengembangan (seperti integrasi Frontend & Konsumen)).*
