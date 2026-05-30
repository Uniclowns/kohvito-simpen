<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BayarController;
use App\Http\Controllers\BerandaAdminController;
use App\Http\Controllers\BerandaKasirController;
use App\Http\Controllers\BerandaKonsumenController;
use App\Http\Controllers\HistoriPesananController;
use App\Http\Controllers\ImageCompressController;
use App\Http\Controllers\KelolaKategoriMenuController;
use App\Http\Controllers\KelolaMejaController;
use App\Http\Controllers\KelolaMenuController;
use App\Http\Controllers\KelolaPenggunaKasirController;
use App\Http\Controllers\KelolaPesananController;
use App\Http\Controllers\KeranjangKonsumenController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\SuperadminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes — Autentikasi (Login/Logout)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'authenticated'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Image Compression — Serves compressed/resized menu images on-the-fly.
| GET /img/{food|drink}/{filename}?w=600&q=78
|--------------------------------------------------------------------------
*/
Route::get('/img/{type}/{file}', ImageCompressController::class)
    ->where(['type' => 'food|drink', 'file' => '[A-Za-z0-9_\-]+\.(png|jpe?g|webp)'])
    ->name('img.serve');

/*
|--------------------------------------------------------------------------
| Admin Routes — Middleware: auth + role:admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    // Dashboard Admin
    Route::get('/', [BerandaAdminController::class, 'index'])->name('beranda');
    Route::get('/data', [BerandaAdminController::class, 'getData'])->name('beranda.data');
    Route::get('/laporan/cetak', [BerandaAdminController::class, 'cetakLaporanKasir'])->name('laporan.cetak');
    Route::post('/toggle-order-status', [BerandaAdminController::class, 'toggleOrderStatus'])->name('toggle-order-status');

    // Kelola Menu
    Route::get('/menu', [KelolaMenuController::class, 'index'])->name('menu.index');
    Route::post('/menu', [KelolaMenuController::class, 'storeMenu'])->name('menu.store');
    Route::put('/menu/{id}', [KelolaMenuController::class, 'updateMenu'])->name('menu.update');
    Route::delete('/menu/{id}', [KelolaMenuController::class, 'destroyMenu'])->name('menu.destroy');

    // Kelola Kategori Menu
    Route::get('/kategori', [KelolaKategoriMenuController::class, 'index'])->name('kategori.index');
    Route::post('/kategori', [KelolaKategoriMenuController::class, 'storeKategoriMenu'])->name('kategori.store');
    Route::put('/kategori/{id}', [KelolaKategoriMenuController::class, 'updateKategoriMenu'])->name('kategori.update');
    Route::delete('/kategori/{id}', [KelolaKategoriMenuController::class, 'destroyKategoriMenu'])->name('kategori.destroy');

    // Kelola Pengguna Kasir
    Route::get('/pengguna-kasir', [KelolaPenggunaKasirController::class, 'index'])->name('pengguna-kasir.index');
    Route::post('/pengguna-kasir', [KelolaPenggunaKasirController::class, 'storePenggunaKasir'])->name('pengguna-kasir.store');
    Route::put('/pengguna-kasir/{id}', [KelolaPenggunaKasirController::class, 'updatePenggunaKasir'])->name('pengguna-kasir.update');
    Route::delete('/pengguna-kasir/{id}', [KelolaPenggunaKasirController::class, 'destroyPenggunaKasir'])->name('pengguna-kasir.destroy');

});

/*
|--------------------------------------------------------------------------
| Super Admin Routes — Middleware: auth + role:superadmin
|--------------------------------------------------------------------------
| Khusus untuk akun Super Admin (god mode). Saat ini hanya menampung fitur
| Kelola Meja & QR Code agar manajemen meja fisik terpisah dari operasional
| admin sehari-hari. Super Admin juga dapat mengakses semua route admin/kasir
| karena CheckRole middleware menerapkan bypass khusus untuk role ini.
*/
Route::prefix('superadmin')->middleware(['auth', 'role:superadmin'])->name('superadmin.')->group(function () {
    // Dashboard Hub Super Admin — launchpad ke semua panel
    Route::get('/', [SuperadminController::class, 'beranda'])->name('beranda');

    // Kelola Admin — CRUD akun Administrator (id_role = 1)
    Route::get('/kelola-admin', [SuperadminController::class, 'indexAdmin'])->name('admin.index');
    Route::post('/kelola-admin', [SuperadminController::class, 'storeAdmin'])->name('admin.store');
    Route::put('/kelola-admin/{id}', [SuperadminController::class, 'updateAdmin'])->name('admin.update');
    Route::delete('/kelola-admin/{id}', [SuperadminController::class, 'destroyAdmin'])->name('admin.destroy');

    // Kelola Meja & QR Code
    // /cetak harus didefinisikan SEBELUM /{id} agar tidak ditangkap sebagai id=cetak
    Route::get('/meja', [KelolaMejaController::class, 'index'])->name('meja.index');
    Route::get('/meja/cetak', [KelolaMejaController::class, 'cetakQr'])->name('meja.cetak');
    Route::post('/meja', [KelolaMejaController::class, 'storeMeja'])->name('meja.store');
    Route::put('/meja/{id}', [KelolaMejaController::class, 'updateMeja'])->name('meja.update');
    Route::delete('/meja/{id}', [KelolaMejaController::class, 'destroyMeja'])->name('meja.destroy');
});

/*
|--------------------------------------------------------------------------
| Kasir Routes — Middleware: auth + role:kasir
|--------------------------------------------------------------------------
*/
Route::prefix('kasir')->middleware(['auth', 'role:kasir'])->name('kasir.')->group(function () {
    // Dashboard Kasir
    Route::get('/', [BerandaKasirController::class, 'index'])->name('beranda');

    // Kelola Pesanan
    Route::get('/pesanan', [KelolaPesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{noPesanan}', [KelolaPesananController::class, 'detail'])->name('pesanan.detail');
    Route::put('/pesanan/{noPesanan}/status', [KelolaPesananController::class, 'updateStatus'])->name('pesanan.update-status');
    Route::get('/pesanan/{noPesanan}/cetak', [KelolaPesananController::class, 'cetakPesanan'])->name('pesanan.cetak');

    // Histori Pesanan
    Route::get('/histori', [HistoriPesananController::class, 'index'])->name('histori.index');
    Route::get('/histori/{noPesanan}', [HistoriPesananController::class, 'detail'])->name('histori.detail');
    Route::get('/histori/{noPesanan}/cetak', [HistoriPesananController::class, 'cetakHistoriPesanan'])->name('histori.cetak');
    Route::get('/histori-cetak-semua', [HistoriPesananController::class, 'cetakSemuaHistoriPesanan'])->name('histori.cetak-semua');
});

/*
|--------------------------------------------------------------------------
| Konsumen Routes — Tanpa Autentikasi (Public)
|--------------------------------------------------------------------------
*/

// Info halaman order ditutup
Route::get('/order-tutup', fn () => view('konsumen.order-tutup'))->name('konsumen.order-tutup');

// Tracking pesanan & kuitansi (tidak diblokir saat tutup)
Route::get('/pembayaran/{noPesanan}', [BayarController::class, 'qris'])->name('konsumen.pembayaran');
Route::get('/pembayaran/{noPesanan}/qr', [BayarController::class, 'downloadQr'])->name('konsumen.bayar.qr');
Route::get('/pembayaran/{noPesanan}/sync', [BayarController::class, 'syncStatus'])->name('konsumen.bayar.sync');

// Daftar Pesanan — semua pesanan dalam sesi konsumen (Figma 1432-23620 / empty 1465-23298)
Route::get('/pesanan', [PesananController::class, 'index'])->name('konsumen.pesanan');

// Lacak Pesanan — timeline progress per pesanan (Figma 1465-22886 / empty 1465-24095)
Route::get('/lacak', [PesananController::class, 'lacakLatest'])->name('konsumen.lacak');
Route::get('/lacak/{noPesanan}', [PesananController::class, 'lacak'])->name('konsumen.lacak.detail');

// Aksi per-pesanan (polling status, kuitansi, pembatalan)
Route::get('/pesanan/{noPesanan}/status', [PesananController::class, 'status'])->name('konsumen.pesanan.status');
Route::get('/pesanan/{noPesanan}/kuitansi', [PesananController::class, 'kuitansi'])->name('konsumen.pesanan.kuitansi');
Route::delete('/pesanan/{noPesanan}/batal', [PesananController::class, 'batal'])->name('konsumen.pesanan.batal');

// Keranjang & pemesanan (diblokir saat order ditutup)
Route::middleware('order.status')->group(function () {
    Route::get('/keranjang', [KeranjangKonsumenController::class, 'index'])->name('konsumen.keranjang');
    Route::post('/keranjang/tambah', [KeranjangKonsumenController::class, 'storeTambahKeranjang'])->name('konsumen.keranjang.tambah');
    Route::put('/keranjang/notes', [KeranjangKonsumenController::class, 'updateNotesPesanan'])->name('konsumen.keranjang.notes');
    Route::put('/keranjang/update', [KeranjangKonsumenController::class, 'updatePesanan'])->name('konsumen.keranjang.update');
    Route::post('/keranjang/pesan', [KeranjangKonsumenController::class, 'storePesan'])->name('konsumen.keranjang.pesan');

    // Pembayaran
    Route::post('/bayar', [BayarController::class, 'bayar'])->name('konsumen.bayar');
    Route::post('/bayar/callback', [BayarController::class, 'callback'])->name('konsumen.bayar.callback');
    Route::get('/bayar/simulator/{noPesanan}', [BayarController::class, 'simulator'])->name('konsumen.bayar.simulator');
    Route::post('/bayar/simulator/{noPesanan}/callback', [BayarController::class, 'simulatorCallback'])->name('konsumen.bayar.simulator.callback');

    // Beranda & Katalog Menu (scan QR → /{noMeja})
    Route::get('/menu/data', [BerandaKonsumenController::class, 'getData'])->name('konsumen.menu.data');
    Route::get('/menu/{id}/detail', [BerandaKonsumenController::class, 'detail'])->name('konsumen.menu.detail');
    Route::get('/{noMeja}', [BerandaKonsumenController::class, 'index'])->name('konsumen.beranda');
});
