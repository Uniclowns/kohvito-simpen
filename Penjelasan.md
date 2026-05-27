# 📖 Buku Pintar Kode SIMPEN (Kohvito)

> **Dokumen ini dibuat khusus untuk keperluan presentasi & dokumentasi developer.** Berisi penjelasan mendalam mengenai arsitektur, pola logika, dan baris kode terbaik dari setiap fitur yang telah dikembangkan di aplikasi SIMPEN (Sistem Informasi Pemesanan Menu) Kohvito Café. Dokumen ini menggambarkan evolusi sistem dari database monolitik klasik hingga arsitektur modern yang dilengkapi RESTful API (Sanctum), kompresi gambar otomatis, visualisasi mobile-first premium, dan integrasi payment gateway.

---

## 📑 Daftar Isi
1. [Milestone 1: Database & Evolvement Migrations](#1-milestone-1-database--evolvement-migrations)
2. [Milestone 2: MVC & API Architecture (Sanctum)](#2-milestone-2-mvc--api-architecture-sanctum)
3. [Milestone 3: On-The-Fly Image Compression & WebP Optimization](#3-milestone-3-on-the-fly-image-compression--webp-optimization)
4. [Milestone 4: Backend Admin & Autentikasi Kompleks](#4-milestone-4-backend-admin--autentikasi-kompleks)
5. [Milestone 5: Backend Kasir, State Machine & Pencarian Closure](#5-milestone-5-backend-kasir-state-machine--pencarian-closure)
6. [Milestone 6: Modul Konsumen Mobile-First & Xendit Payment Gateway](#6-milestone-6-modul-konsumen-mobile-first--xendit-payment-gateway)
7. [Milestone 7: Desain Responsif & Arsitektur Grid Konsumen Widescreen](#7-milestone-7-desain-responsif--arsitektur-grid-konsumen-widescreen)
8. [Dependencies & Library Eksternal Lengkap](#8-dependencies--library-eksternal-lengkap)

---

## 1. Milestone 1: Database & Evolvement Migrations

*Migration* di Laravel berfungsi sebagai sistem kontrol versi untuk database. Seiring berjalannya pengembangan aplikasi Kohvito, struktur tabel telah berevolusi dari skema relasional linear sederhana menjadi struktur relasional dinamis dengan normalisasi data tingkat tinggi demi performa yang optimal.

### A. Contoh Logika Migration Terkini (Tabel Pesanan)
Tabel ini merupakan urat nadi transaksi. Terdapat beberapa hal unik pada pembuatannya:

```php
Schema::create('pesanan', function (Blueprint $table) {
    // 1. Kustomisasi Primary Key String:
    // Kita tidak menggunakan ID auto-increment integer biasa untuk melacak pesanan,
    // melainkan string unik 'no_pesanan' (VARCHAR) sebagai kode unik transaksi global.
    $table->string('no_pesanan', 50)->primary();

    // 2. Foreign Keys & Nullability:
    // id_user di-set nullable agar pesanan dari konsumen dapat masuk terlebih dahulu
    // sebelum nantinya diasosiasikan dengan id_users kasir yang melakukan konfirmasi.
    $table->unsignedBigInteger('id_user')->nullable();
    $table->unsignedBigInteger('id_meja');
    $table->string('nama_konsumen');
    $table->integer('total_harga');

    // 3. Tipe Data ENUM & Status Penjaga Integritas Data:
    // ENUM membatasi nilai input agar tidak ada inkonsistensi teks pada database.
    $table->enum('status_pembayaran', ['belum bayar', 'menunggu', 'lunas', 'gagal'])->default('belum bayar');
    $table->enum('status_pesanan', ['menunggu konfirmasi', 'diproses', 'selesai', 'dibatalkan'])->default('menunggu konfirmasi');
    
    $table->string('catatan_pesanan')->nullable();
    $table->timestamp('tgl_pembayaran')->nullable();

    // 4. Foreign Key Constraints dengan Kebijakan Referensi:
    // Jika user/kasir dihapus, pesanan tetap ada dengan id_user bernilai null (set null) demi data transaksi historis.
    // Jika meja dihapus, database akan menolak (restrict) apabila meja tersebut memiliki catatan pesanan aktif.
    $table->foreign('id_user')->references('id_users')->on('users')->onDelete('set null');
    $table->foreign('id_meja')->references('id_meja')->on('meja')->onDelete('restrict');
});
```

### B. Normalisasi Banyak-ke-Banyak (Pivot Table `menu_kategori`)
Untuk mendukung arsitektur menu modern di mana satu menu dapat tergabung ke dalam beberapa kategori sekaligus (misal: "Espresso" adalah kategori "Kopi" dan juga "Best Seller"), aplikasi beralih dari kolom `id_kategori` linear pada tabel `menu` ke tabel penghubung (*pivot table*) `menu_kategori`.

```php
Schema::create('menu_kategori', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('id_menu');
    $table->unsignedBigInteger('id_kategori');

    // Cascade Delete: Jika menu atau kategori dihapus, relasi pivot otomatis terhapus
    $table->foreign('id_menu')->references('id_menu')->on('menu')->onDelete('cascade');
    $table->foreign('id_kategori')->references('id_kategori')->on('kategori_menu')->onDelete('cascade');
});
```

---

## 2. Milestone 2: MVC & API Architecture (Sanctum)

Aplikasi SIMPEN didesain menggunakan **Hybrid Architecture**. Selain melayani render halaman server-side tradisional menggunakan Blade (*Model-View-Controller*), aplikasi ini juga dilengkapi dengan arsitektur **RESTful API terproteksi** menggunakan **Laravel Sanctum** untuk mendukung integrasi aplikasi mobile konsumen atau perangkat kasir pintar di masa depan.

```
                  ┌─────────────────────────────────────────┐
                  │              HTTP REQUEST               │
                  └────────────────────┬────────────────────┘
                                       │
                    ┌──────────────────┴──────────────────┐
                    ▼                                     ▼
          [ routes/web.php ]                     [ routes/api.php ]
         (Web MVC Controllers)                (Sanctum API Controllers)
                    │                                     │
           ┌────────┴────────┐                   ┌────────┴────────┐
           ▼                 ▼                   ▼                 ▼
     [ Blade Views ]   [ HTML Output ]     [ Auth Middleware ]  [ JSON Response ]
```

### A. Model Eloquent & Relasi Banyak-ke-Banyak (Many-to-Many)
Model `Menu` merepresentasikan tabel `menu` dan mendefinisikan relasi Eloquent yang fleksibel ke model `KategoriMenu` melalui relasi `belongsToMany`:

```php
class Menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'id_menu';
    public $timestamps = false;

    protected $fillable = [
        'nama_menu', 'deskripsi', 'harga', 'stock',
        'komposisi', 'gambar_menu', 'status_ketersediaan',
        'jenis_menu', 'kategori_makanan', 'tipe_minuman',
    ];

    // Relasi Banyak-ke-Banyak ke KategoriMenu
    public function kategoris(): BelongsToMany
    {
        return $this->belongsToMany(
            KategoriMenu::class,
            'menu_kategori', // Nama tabel pivot
            'id_menu',       // FK model asal
            'id_kategori'    // FK model tujuan
        );
    }
}
```

### B. Middleware CheckRole (Multi-User Protection)
Pintu gerbang keamanan yang memastikan bahwa hanya akun berwenang yang dapat mengakses dashboard tertentu berdasarkan relasi tabel `roles`.

```php
public function handle(Request $request, Closure $next, string $role): Response
{
    $user = $request->user();

    // Pastikan user terautentikasi dan memiliki relasi role
    if (!$user || !$user->role) {
        abort(403, 'Akses ditolak.');
    }

    // Standardisasi pengecekan case-insensitive
    if (strtolower($user->role->nama_role) !== strtolower($role)) {
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }

    return $next($request);
}
```

### C. RESTful API Routing (Laravel Sanctum)
File `routes/api.php` mengimplementasikan RESTful API dengan struktur rute yang rapi:

```php
// Rute Publik Auth & Konsumen
Route::post('/auth/login', [AuthApiController::class, 'login']);
Route::prefix('konsumen')->group(function () {
    Route::get('/menu', [KonsumenApiController::class, 'getMenu']);
    Route::get('/menu/{id}/detail', [KonsumenApiController::class, 'detailMenu']);
    Route::post('/pesanan', [KonsumenApiController::class, 'storePesan']);
    Route::post('/bayar', [KonsumenApiController::class, 'bayar']);
});

// Rute Terproteksi Token (Sanctum) & Hak Akses (Role)
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminApiController::class, 'dashboard']);
    Route::post('/menu', [AdminApiController::class, 'storeMenu']);
});
```

---

## 3. Milestone 3: On-The-Fly Image Compression & WebP Optimization

Salah satu fitur premium pada aplikasi SIMPEN adalah **`ImageCompressController`**. Fitur ini bertugas memproses, mengubah ukuran, melakukan kompresi, dan menyajikan gambar menu secara dinamis ketika di-request oleh browser konsumen.

### Keunggulan Rekayasa Sistem Gambar:
1. **On-the-fly Transformation**: Browser melakukan request dengan parameter kustom, contoh: `/img/food/nasi-goreng.png?w=400&q=75`.
2. **WebP Converter**: Apapun format aslinya (PNG/JPEG), output akan dikonversi ke format **WebP** yang memiliki ukuran file hingga 70% lebih kecil dengan kualitas visual yang sama.
3. **Recursive File Caching**: Hasil kompresi pertama disimpan di folder `public/images/cache/`. Request berikutnya langsung dilayani dari file cache tanpa proses kalkulasi ulang.
4. **Fallback Mechanism**: Jika ukuran file hasil kompresi ternyata lebih besar dari file asli, sistem secara otomatis akan menyajikan file asli.

```php
class ImageCompressController extends Controller
{
    private const ALLOWED_TYPES = ['food', 'drink'];
    private const DEFAULT_WIDTH = 600;
    private const DEFAULT_QUALITY = 78;
    private const CACHE_TTL_SECONDS = 31536000; // 1 Tahun Cache Browser

    public function __invoke(Request $request, string $type, string $file): BinaryFileResponse|Response
    {
        if (!in_array($type, self::ALLOWED_TYPES, true)) abort(404);
        if (!preg_match('/^[A-Za-z0-9_\-]+\.(png|jpe?g|webp)$/i', $file)) abort(404);

        $width = max(100, min(1600, (int) $request->query('w', self::DEFAULT_WIDTH)));
        $quality = max(40, min(95, (int) $request->query('q', self::DEFAULT_QUALITY)));

        $srcPath = public_path("images/{$type}/{$file}");
        if (!is_file($srcPath)) abort(404);

        $basename = pathinfo($file, PATHINFO_FILENAME);
        $cacheDir = public_path("images/cache/{$type}/{$width}x{$quality}");
        $cachePath = "{$cacheDir}/{$basename}.webp";

        // Generate cache jika belum ada atau file asli di-update
        if (!is_file($cachePath) || filemtime($cachePath) < filemtime($srcPath)) {
            $this->compressImage($srcPath, $cachePath, $width, $quality);
        }

        $servePath = (filesize($cachePath) < filesize($srcPath)) ? $cachePath : $srcPath;

        return response()->file($servePath, [
            'Cache-Control'   => 'public, max-age=' . self::CACHE_TTL_SECONDS . ', immutable',
            'X-Compressed-By' => 'kohvito-image-compress',
        ]);
    }

    private function compressImage(string $srcPath, string $cachePath, int $maxWidth, int $quality): void
    {
        if (!is_dir(dirname($cachePath))) @mkdir(dirname($cachePath), 0755, true);

        [$srcW, $srcH, $type] = getimagesize($srcPath);
        $src = match ($type) {
            IMAGETYPE_PNG  => imagecreatefrompng($srcPath),
            IMAGETYPE_JPEG => imagecreatefromjpeg($srcPath),
            IMAGETYPE_WEBP => imagecreatefromwebp($srcPath),
            default        => abort(500),
        };

        // Hitung aspek rasio agar gambar tidak gepeng (asymmetric scaling)
        $ratio = $srcW > $maxWidth ? $maxWidth / $srcW : 1.0;
        $dstW = (int) round($srcW * $ratio);
        $dstH = (int) round($srcH * $ratio);

        // Pertahankan Alpha Channel (Transparansi gambar PNG/WebP)
        $dst = imagecreatetruecolor($dstW, $dstH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefilledrectangle($dst, 0, 0, $dstW, $dstH, $transparent);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
        imagewebp($dst, $cachePath, $quality); // Simpan WebP dengan kompresi kualitas dinamis

        imagedestroy($src);
        imagedestroy($dst);
    }
}
```

---

## 4. Milestone 4: Backend Admin & Autentikasi Kompleks

Bagian ini berfokus pada panel kendali utama Admin untuk mengelola data master, otorisasi, dan laporan kinerja finansial café.

### A. Autentikasi Login Aman & Penanganan Hijack Session
Sistem menerapkan regenerasi token session sesaat setelah login berhasil untuk menangkal serangan cyber berupa *Session Fixation* dan merutekan pengguna berdasarkan perannya:

```php
public function store(Request $request)
{
    $credentials = $request->validate([
        'username' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    if (Auth::attempt($credentials)) {
        // Regenerasi ID Session setelah login sukses
        $request->session()->regenerate();

        $role = strtolower(Auth::user()->role->nama_role);
        if ($role === 'admin') {
            return redirect()->route('admin.beranda');
        } elseif ($role === 'kasir') {
            return redirect()->route('kasir.beranda');
        }
    }

    return back()->withErrors(['loginError' => 'Username atau password tidak cocok.']);
}
```

### B. Filter & Agregasi Laporan Keuangan Tingkat Lanjut
Sistem menggunakan teknik **Sub-Query Filter** menggunakan Eloquent untuk menghitung omzet secara akurat dan melacak menu best-seller dalam rentang waktu terfilter.

```php
public function index(Request $request)
{
    // Filter rentang hari (00:00:00 s.d. 23:59:59)
    $tanggalMulai = $request->filled('tanggal_mulai')
        ? Carbon::parse($request->input('tanggal_mulai'))->startOfDay()
        : Carbon::today()->startOfDay();

    $tanggalSelesai = $request->filled('tanggal_selesai')
        ? Carbon::parse($request->input('tanggal_selesai'))->endOfDay()
        : Carbon::today()->endOfDay();

    // Query agregasi pesanan lunas
    $pesanans = Pesanan::with(['meja', 'detailPesanan.menu'])
        ->where('status_pembayaran', 'lunas')
        ->whereBetween('tgl_pembayaran', [$tanggalMulai, $tanggalSelesai])
        ->get();

    $totalOmzet = $pesanans->sum('total_harga');

    // Menghitung Top 5 Menu Paling Banyak Dipesan (Agregasi Query Builder)
    $menuTerlaris = DetailPesanan::with('menu')
        ->whereHas('pesanan', function ($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->where('status_pembayaran', 'lunas')
              ->whereBetween('tgl_pembayaran', [$tanggalMulai, $tanggalSelesai]);
        })
        ->selectRaw('id_menu, SUM(jumlah) as total_terjual')
        ->groupBy('id_menu')
        ->orderByDesc('total_terjual')
        ->limit(5)
        ->get();

    return view('admin.laporan', compact('pesanans', 'totalOmzet', 'menuTerlaris'));
}
```

---

## 5. Milestone 5: Backend Kasir, State Machine & Pencarian Closure

Modul Kasir bertanggung jawab dalam memproses pesanan dapur dan mengelola transaksi aktif.

### A. Fitur Buka/Tutup Toko Global Berbasis Cache Memori
Untuk mengontrol penerimaan pesanan dari konsumen tanpa membebani server database melalui query I/O terus-menerus, sistem menyimpan state toko aktif di dalam **Laravel Cache** global.

```php
// 1. Pintu Penjaga Akses Pemesanan (Middleware: CheckOrderStatus)
public function handle(Request $request, Closure $next): Response
{
    if (Cache::get('order_status', 'buka') === 'tutup') {
        return redirect()->route('konsumen.order-tutup');
    }
    return $next($request);
}

// 2. Toggle Status Toko (Oleh Admin/Kasir)
public function toggleOrderStatus()
{
    $current = Cache::get('order_status', 'buka');
    $newStatus = $current === 'buka' ? 'tutup' : 'buka';
    Cache::forever('order_status', $newStatus);

    return redirect()->back()->with('success', "Status pesanan berhasil diubah menjadi {$newStatus}");
}
```

### B. Proteksi Transisi Status Pesanan (State Machine)
Sistem membatasi transisi status pesanan kasir menggunakan aturan berurutan (*state machine*). Status tidak boleh lompat (misal: dari "menunggu konfirmasi" langsung ke "selesai" tanpa melewati proses "diproses").

```php
public function updateStatus(Request $request, string $noPesanan)
{
    $pesanan = Pesanan::where('no_pesanan', $noPesanan)->firstOrFail();

    // Kamus Aturan Transisi Status
    $transitions = [
        'menunggu konfirmasi' => 'diproses',
        'diproses'            => 'selesai',
    ];

    $nextStatus = $transitions[$pesanan->status_pesanan] ?? null;

    if (!$nextStatus) {
        return redirect()->back()->with('error', 'Transisi status tidak valid.');
    }

    $pesanan->update(['status_pesanan' => $nextStatus]);
    return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
}
```

### C. Teknik Pencarian Tingkat Lanjut Berbasis Closure (Fungsi Anonim)
Dalam menu histori, pencarian kata kunci multi-kolom di-group di dalam tanda kurung SQL untuk mencegah terjadinya pembatalan kondisi filter utama `WHERE status_pesanan = 'selesai'`.

```php
$query = Pesanan::with('meja')->where('status_pesanan', 'selesai');

if ($searchKeyword) {
    // Penggunaan Closure untuk mengisolasi query: AND (no_pesanan LIKE ? OR nama_konsumen LIKE ?)
    $query->where(function ($q) use ($searchKeyword) {
        $q->where('no_pesanan', 'like', "%{$searchKeyword}%")
          ->orWhere('nama_konsumen', 'like', "%{$searchKeyword}%");
    });
}
$pesanans = $query->orderBy('tgl_pembayaran', 'desc')->get();
```

---

## 6. Milestone 6: Modul Konsumen Mobile-First & Xendit Payment Gateway

Antarmuka konsumen dirancang dengan konsep **Mobile-First Premium** yang diadaptasi dari pixel Figma mockups. Visualisasi interaktif, performa responsif, dan alur pembayaran instan menjadi fokus utama.

```
       ┌────────────────────────┐
       │   Scan QR Code Meja    │
       └───────────┬────────────┘
                   │
                   ▼
       ┌────────────────────────┐
       │ Splash Overlay (White) │ (Dot expansion animation)
       └───────────┬────────────┘
                   │
                   ▼
       ┌────────────────────────┐
       │  Katalog & Filter Menu │ (Pills sticky on scroll)
       └───────────┬────────────┘
                   │
                   ▼
       ┌────────────────────────┐
       │ Keranjang Belanja (Sess)│ (Custom notes & qty control)
       └───────────┬────────────┘
                   │
                   ▼
       ┌────────────────────────┐
       │ Xendit Payment Gateway │ (Automatic Invoice & Webhook Callback)
       └────────────────────────┘
```

### A. Fitur Interaktif Frontend Premium (Blade & Tailwind)
View konsumen (`konsumen/beranda.blade.php`) menyajikan pengalaman interaktif tanpa lag:
1. **Interactive Splash Dot Expansion**: Titik merah khas Kohvito melebar menutupi layar putih, memunculkan maskot dan salam hangat sebelum menyajikan katalog menu.
2. **Color-Dodge Blend Effect**: Efek teks tumpang tindih (*overlay typography*) premium pada gambar banner.
3. **Sticky Category on Scroll**: Kategori menu akan menempel di bagian atas layar ketika discroll melewati threshold.
4. **Glassmorphism Bottom Navigation**: Bar navigasi melayang (*floating navigation*) dengan efek kaca blur (`backdrop-blur-md`) di bagian bawah layar.

### B. Manajemen Keranjang Belanja Sementara (Session-Based)
Agar tidak membebani database dengan data sampah dari transaksi yang batal checkout, item keranjang beserta kustomisasi catatan (*notes*) disimpan di session server konsumen:

```php
public function storeTambahKeranjang(Request $request)
{
    $request->validate([
        'id_menu' => ['required', 'integer', 'exists:menu,id_menu'],
        'jumlah'  => ['required', 'integer', 'min:1'],
        'catatan' => ['nullable', 'string', 'max:255']
    ]);

    $menu = Menu::findOrFail($request->id_menu);
    $keranjang = session('keranjang', []);
    $idMenu = (int)$request->id_menu;

    if (isset($keranjang[$idMenu])) {
        $keranjang[$idMenu]['jumlah'] += $request->jumlah;
        $keranjang[$idMenu]['subtotal'] = $keranjang[$idMenu]['harga'] * $keranjang[$idMenu]['jumlah'];
        if ($request->catatan) $keranjang[$idMenu]['catatan'] = $request->catatan;
    } else {
        $keranjang[$idMenu] = [
            'id_menu'   => $idMenu,
            'nama_menu' => $menu->nama_menu,
            'harga'     => $menu->harga,
            'jumlah'    => $request->jumlah,
            'catatan'   => $request->catatan,
            'subtotal'  => $menu->harga * $request->jumlah,
        ];
    }

    session(['keranjang' => $keranjang]);
    return redirect()->back()->with('success', 'Item dimasukkan ke keranjang.');
}
```

### C. Integrasi Xendit Payment Gateway & Webhook Callback Otomatis
Setelah checkout, konsumen diarahkan ke halaman invoice instan Xendit. Validasi pelunasan dikonfirmasi secara aman melalui mekanisme **Webhook Callback** background server-to-server:

```php
// 1. Pembuatan Invoice Xendit Dinamis
public function bayar(Request $request)
{
    $pesanan = Pesanan::findOrFail($request->no_pesanan);

    Xendit::setApiKey(config('services.xendit.api_key'));

    $invoice = Invoice::create([
        'external_id'          => $pesanan->no_pesanan,
        'amount'               => $pesanan->total_harga,
        'payer_email'          => 'konsumen@kohvito.com',
        'description'          => 'Pembayaran pesanan ' . $pesanan->no_pesanan,
        'invoice_duration'     => 86400, // Durasi aktif 24 jam
        'currency'             => 'IDR',
        'customer'             => ['given_names' => $pesanan->nama_konsumen],
    ]);

    return response()->json(['invoice_url' => $invoice['invoice_url']]);
}

// 2. Webhook Handler dari API Xendit (Callback)
public function callback(Request $request)
{
    $payload = $request->all();

    // Verifikasi status pembayaran dari payloader Xendit
    if (isset($payload['status']) && $payload['status'] === 'PAID') {
        $pesanan = Pesanan::find($payload['external_id']);
        
        if ($pesanan) {
            // State pembayaran diubah otomatis ke lunas secara background
            $pesanan->update([
                'status_pembayaran' => 'lunas',
                'status_pesanan'    => 'menunggu konfirmasi',
                'tgl_pembayaran'    => now(),
            ]);
        }
    }
    
    return response()->json(['status' => 'success']);
}
```

---

## 7. Milestone 7: Desain Responsif & Arsitektur Grid Konsumen Widescreen

Untuk menghadirkan pengalaman berbelanja premium di segala ukuran layar—mulai dari smartphone layar kecil, tablet, hingga monitor desktop ultra-wide—tata letak antarmuka konsumen pada aplikasi SIMPEN telah sepenuhnya dirombak dari batasan *mobile-only* yang kaku menjadi desain **fluid-responsive** modern berbasis arsitektur grid Tailwind CSS.

### A. Rekayasa Viewport & Penghapusan Hambatan Lebar Statis (Fluid Container)
Sebelumnya, antarmuka konsumen dikunci pada lebar statis `max-width: 440px` dengan latar belakang wallpaper simulasi telepon seluler pada resolusi tablet/desktop. Batasan ini dilepas dengan merekayasa ulang `app.css` agar kontainer utama dapat membesar secara fleksibel mengikuti lebar *viewport* fisik layar perangkat, dengan batas proporsional aman (`max-width: 1280px` atau `1400px` pada layar lebar desktop) agar konten tidak terlihat terlalu renggang.

```css
/* app.css - Pelepasan Lock Width & Centered Container */
@media (min-width: 768px) {
    .app-container {
        max-width: 1280px; /* Batas maksimal layar desktop agar tetap proporsional */
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        margin-left: auto;
        margin-right: auto;
    }
}
```

### B. Transisi Grid Katalog Menu Adaptif (Responsive Menu Grid)
Pada halaman utama (`beranda.blade.php`), daftar menu didesain menggunakan skema grid dinamis dengan transisi kolom sebagai berikut:
- **Mobile (<768px)**: `grid-cols-2` (2 kolom gambar) untuk akses jempol yang cepat.
- **Tablet (768px - 1023px)**: `md:grid-cols-3` (3 kolom) untuk memaksimalkan ruang layar sedang.
- **Laptop (1024px - 1279px)**: `lg:grid-cols-4` (4 kolom) menyajikan proporsi seimbang.
- **Widescreen Desktop (≥1280px)**: `xl:grid-cols-6` (6 kolom) menghadirkan layout e-commerce kelas dunia yang memukau.

Selain itu, ditambahkan pula pembungkusan baris kategori menu (`flex-wrap`) agar tombol pil filter tidak terpotong ke samping di layar lebar desktop, serta pembatasan lebar kolom input pencarian (`max-w-md mx-auto`) agar elemen kontrol pencarian tetap simetris di tengah.

```html
<!-- Grid Katalog Menu yang Adaptif -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 md:gap-6 mt-6">
    @foreach($menus as $menu)
        @include('konsumen.partials.menu-card', ['menu' => $menu])
    @endforeach
</div>
```

### C. Harmonisasi Tipografi Ganda (Dual-Typography Editorial Design)
Untuk memperkuat kesan kafe berkelas, kami menerapkan paduan tipografi editorial premium:
- **Font Sans-serif (`Aileron`)**: Digunakan untuk elemen-elemen kontrol fungsional, angka harga, nama opsi, status ketersediaan, serta tombol aksi demi keterbacaan tingkat tinggi (*readability*).
- **Font Serif Klasik (`Georgia`)**: Digunakan khusus untuk judul/headline besar dan frasa dekoratif dengan gaya miring (*italic*). Contohnya pada banner selamat datang: `"Pesan Menu"` (sans-serif bold berukuran besar) bersanding anggun dengan *sub-headline* italic serif `"Anti Ribet."`

Gaya visual ini memecah kejenuhan desain digital modern dengan memberikan kesan artistik layaknya menu fisik majalah kuliner kelas atas.

### D. Rekayasa Modal Drawer Menjadi Centered Dialog Box
Pada perangkat mobile, detail menu ditampilkan via slide-up drawer yang muncul dari tepi bawah layar (`translate-y-full` ke `translate-y-0`). Pada perangkat desktop/tablet, drawer bawah ini terasa aneh dan tidak proporsional. 

Oleh karena itu, sistem CSS merekayasa transisi pada `@media (min-width: 768px)` untuk menyulap drawer tersebut menjadi **modal dialog box terpusat** dengan transisi skala yang halus (`scale-95` ke `scale-100`) dan penempatan di pusat layar (`items-center justify-center`).

```css
@media (min-width: 768px) {
    #menu-sheet {
        top: 0 !important;
        display: flex !important;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 300ms ease;
    }
    #menu-sheet-panel {
        position: relative !important;
        transform: scale(0.9) !important;
        max-width: 580px !important;
        width: 90% !important;
        max-height: 85vh !important;
        border-radius: 18px !important;
    }
}
```

### E. Split-Layout Konten Berdampingan (Two-Column Split System)
Untuk layar lebar tablet/desktop, antarmuka linear yang memanjang ke bawah dipecah secara cerdas menjadi tata letak berdampingan menggunakan sistem grid Tailwind:

1. **Detail Produk Terpisah (`detail-menu-content.blade.php`)**:
   - Gambar produk diletakkan secara eksklusif di kolom kiri dengan efek bingkai melengkung premium.
   - Kolom kanan memuat deskripsi menu, komposisi bahan, kelompok pilihan tambahan (*extra options*), catatan pesanan, dan tombol aksi pemesanan.
   - Tombol melayang di bawah layar (*floating sticky footer*) dinonaktifkan sepenuhnya di desktop, diubah menjadi tombol inline statis yang elegan di dalam kolom kanan (`dm-fixed-footer` dinonaktifkan di media query).
   
2. **E-Commerce Checkout & Keranjang Belanja (`keranjang.blade.php`)**:
   - Halaman dipecah menjadi **7-kolom kiri** untuk mendaftarkan item pesanan dengan kontrol kuantitas interaktif, dan **5-kolom kanan** sebagai *sticky sidebar summary card* untuk input nama, nomor meja, ringkasan harga, dan tombol checkout terintegrasi.

3. **Layar Pelacakan Pesanan & Status Bayar (`lacak.blade.php` & `pembayaran.blade.php`)**:
   - **Pelacakan**: Sisi kiri menyajikan informasi ringkasan meja, status pembayaran, dan total tagihan, sedangkan sisi kanan menyajikan bagan garis vertikal *timeline progress status* pesanan real-time dari dapur.
   - **Pembayaran**: Sisi kiri berfokus menampilkan kartu QRIS dinamis yang siap dipindai, sementara sisi kanan mengelompokkan panduan langkah demi langkah instruksi pembayaran secara rapi demi menghindari kebingungan konsumen.

```html
<!-- Contoh Struktur Split Layout 2 Kolom di Keranjang Belanja -->
<form action="{{ route('konsumen.checkout') }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
    <!-- Kolom Kiri: Item Belanja (Col Span 7) -->
    <div class="md:col-span-7 space-y-4">
        <!-- Loop item-item keranjang -->
    </div>
    
    <!-- Kolom Kanan: Ringkasan & Form Meja (Col Span 5 - Sticky) -->
    <div class="md:col-span-5 md:sticky md:top-24">
        <div class="bg-[#FFF5F5] border border-[#681F1F]/10 rounded-2xl p-6">
            <!-- Ringkasan Harga & Input Checkout -->
        </div>
    </div>
</form>
```

---

## 8. Dependencies & Library Eksternal Lengkap

SIMPEN memanfaatkan ekosistem library modern untuk menangani fitur-fitur kompleks dengan efisiensi tinggi.

### A. Dependensi Backend / PHP (`composer.json`)
* **`laravel/sanctum`**: Menyediakan otentikasi API berbasis token yang aman namun super ringan untuk SPA maupun aplikasi mobile.
* **`xendit/xendit-php`**: SDK resmi Xendit untuk mempermudah integrasi pembuatan invoice, pelacakan payment channel, dan handling webhook.
* **`simplesoftwareio/simple-qrcode`**: Generator QR Code berkecepatan tinggi yang dikonfigurasi untuk melukis QR Code unik per nomor meja dalam format PNG berkualitas tinggi.
* **`barryvdh/laravel-dompdf`**: Mengubah representasi visual Blade HTML menjadi dokumen PDF resmi secara server-side pada menu pencetakan nota/laporan.
* **`rap2hpoutre/fast-excel`**: Mempercepat ekspor data laporan yang berjumlah ribuan baris ke format Excel (.xlsx) dengan alokasi RAM yang minimal.

### B. Dependensi Frontend / Javascript (`package.json`)
* **`tailwindcss`** & **`@tailwindcss/vite`**: Framework CSS utilitas super modern versi terbaru yang diintegrasikan langsung dengan compiler super cepat Vite untuk menyusun antarmuka visual Kohvito yang memikat.
* **`vite`** & **`laravel-vite-plugin`**: Compiler aset frontend yang menyajikan Hot Module Replacement (HMR) berkecepatan tinggi saat proses development dan bundler optimal saat production.
* **`concurrently`**: Memungkinkan proses development server Laravel (`php artisan serve`) dan aset compiler Vite (`npm run dev`) dijalankan bersamaan dalam satu jendela terminal konsol tunggal.
* **`autoprefixer`** & **`postcss`**: Secara cerdas menambahkan prefiks CSS vendor secara otomatis agar layout tampilan tetap konsisten dan rapi di semua browser web modern (Safari, Chrome, Firefox, dll).

---
*(Dokumen ini akan terus diperbarui secara dinamis seiring penambahan fitur terobosan baru lainnya di SIMPEN).*
