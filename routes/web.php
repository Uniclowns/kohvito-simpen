<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BayarController;
use App\Http\Controllers\BerandaAdminController;
use App\Http\Controllers\BerandaKasirController;
use App\Http\Controllers\BerandaKonsumenController;
use App\Http\Controllers\HistoriPesananController;
use App\Http\Controllers\KelolaKategoriMenuController;
use App\Http\Controllers\KelolaMenuController;
use App\Http\Controllers\KelolaPenggunaKasirController;
use App\Http\Controllers\KelolaPesananController;
use App\Http\Controllers\KeranjangKonsumenController;
use App\Http\Controllers\PesananController;
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
| Admin Routes — Middleware: auth + role:admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    // Dashboard Admin
    Route::get('/', [BerandaAdminController::class, 'index'])->name('beranda');
    Route::get('/data', [BerandaAdminController::class, 'getData'])->name('beranda.data');
    Route::get('/laporan/cetak', [BerandaAdminController::class, 'cetakLaporanKasir'])->name('laporan.cetak');

    // Kelola Menu
    Route::get('/menu', [KelolaMenuController::class, 'index'])->name('menu.index');
    Route::post('/menu', [KelolaMenuController::class, 'storeMenu'])->name('menu.store');
    Route::put('/menu/{id}', [KelolaMenuController::class, 'updateMenu'])->name('menu.update');
    Route::delete('/menu/{id}', [KelolaMenuController::class, 'destroyMenu'])->name('menu.destroy');

    // Kelola Kategori Menu
    Route::get('/kategori', [KelolaKategoriMenuController::class, 'index'])->name('kategori.index');
    Route::post('/kategori', [KelolaKategoriMenuController::class, 'storeKategoriMenu'])->name('kategori.store');
    Route::delete('/kategori/{id}', [KelolaKategoriMenuController::class, 'destroyKategoriMenu'])->name('kategori.destroy');

    // Kelola Pengguna Kasir
    Route::get('/pengguna-kasir', [KelolaPenggunaKasirController::class, 'index'])->name('pengguna-kasir.index');
    Route::post('/pengguna-kasir', [KelolaPenggunaKasirController::class, 'storePenggunaKasir'])->name('pengguna-kasir.store');
    Route::delete('/pengguna-kasir/{id}', [KelolaPenggunaKasirController::class, 'destroyPenggunaKasir'])->name('pengguna-kasir.destroy');
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

// Tracking pesanan & kuitansi
Route::get('/pesanan/{noPesanan}', [PesananController::class, 'index'])->name('konsumen.pesanan');

// Keranjang
Route::get('/keranjang', [KeranjangKonsumenController::class, 'index'])->name('konsumen.keranjang');
Route::post('/keranjang/tambah', [KeranjangKonsumenController::class, 'storeTambahKeranjang'])->name('konsumen.keranjang.tambah');
Route::put('/keranjang/notes', [KeranjangKonsumenController::class, 'updateNotesPesanan'])->name('konsumen.keranjang.notes');
Route::put('/keranjang/update', [KeranjangKonsumenController::class, 'updatePesanan'])->name('konsumen.keranjang.update');
Route::post('/keranjang/pesan', [KeranjangKonsumenController::class, 'storePesan'])->name('konsumen.keranjang.pesan');

// Pembayaran
Route::post('/bayar', [BayarController::class, 'bayar'])->name('konsumen.bayar');
Route::post('/bayar/callback', [BayarController::class, 'callback'])->name('konsumen.bayar.callback');

// Beranda & Katalog Menu (scan QR → /{noMeja})
Route::get('/menu/data', [BerandaKonsumenController::class, 'getData'])->name('konsumen.menu.data');
Route::get('/menu/{id}/detail', [BerandaKonsumenController::class, 'detail'])->name('konsumen.menu.detail');
Route::get('/{noMeja}', [BerandaKonsumenController::class, 'index'])->name('konsumen.beranda');
