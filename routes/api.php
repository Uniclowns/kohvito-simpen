<?php

use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\KasirApiController;
use App\Http\Controllers\Api\KonsumenApiController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────────
// Auth
// ─────────────────────────────────────────────────────────────────────────────
Route::post('/auth/login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthApiController::class, 'logout']);
    Route::get('/auth/me', [AuthApiController::class, 'me']);
});

// ─────────────────────────────────────────────────────────────────────────────
// Admin routes
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    // Dashboard & order status
    Route::get('/dashboard', [AdminApiController::class, 'dashboard']);
    Route::get('/order-status', [AdminApiController::class, 'getOrderStatus']);
    Route::post('/order-status/toggle', [AdminApiController::class, 'toggleOrderStatus']);

    // Menu
    Route::get('/menu', [AdminApiController::class, 'indexMenu']);
    Route::post('/menu', [AdminApiController::class, 'storeMenu']);
    Route::put('/menu/{id}', [AdminApiController::class, 'updateMenu']);
    Route::delete('/menu/{id}', [AdminApiController::class, 'destroyMenu']);

    // Kategori menu
    Route::get('/kategori', [AdminApiController::class, 'indexKategori']);
    Route::post('/kategori', [AdminApiController::class, 'storeKategori']);
    Route::delete('/kategori/{id}', [AdminApiController::class, 'destroyKategori']);

    // Pengguna kasir
    Route::get('/pengguna-kasir', [AdminApiController::class, 'indexKasir']);
    Route::post('/pengguna-kasir', [AdminApiController::class, 'storeKasir']);
    Route::delete('/pengguna-kasir/{id}', [AdminApiController::class, 'destroyKasir']);

    // Laporan keuangan
    Route::get('/laporan', [AdminApiController::class, 'indexLaporan']);
});

// ─────────────────────────────────────────────────────────────────────────────
// Kasir routes
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'role:kasir'])->prefix('kasir')->group(function () {
    Route::get('/dashboard', [KasirApiController::class, 'dashboard']);
    Route::get('/pesanan', [KasirApiController::class, 'indexPesanan']);
    Route::get('/pesanan/{noPesanan}', [KasirApiController::class, 'detailPesanan']);
    Route::put('/pesanan/{noPesanan}/status', [KasirApiController::class, 'updateStatusPesanan']);
    Route::get('/histori', [KasirApiController::class, 'indexHistori']);
    Route::get('/histori/{noPesanan}', [KasirApiController::class, 'detailHistori']);
});

// ─────────────────────────────────────────────────────────────────────────────
// Konsumen routes (public — no authentication required)
// ─────────────────────────────────────────────────────────────────────────────
Route::prefix('konsumen')->group(function () {
    Route::get('/menu', [KonsumenApiController::class, 'getMenu']);
    Route::get('/menu/{id}/detail', [KonsumenApiController::class, 'detailMenu']);
    Route::get('/keranjang', [KonsumenApiController::class, 'keranjang']);
    Route::post('/keranjang/tambah', [KonsumenApiController::class, 'tambahKeranjang']);
    Route::put('/keranjang/update', [KonsumenApiController::class, 'updateKeranjang']);
    Route::put('/keranjang/notes', [KonsumenApiController::class, 'updateNotes']);
    Route::post('/pesanan', [KonsumenApiController::class, 'storePesan']);
    Route::get('/pesanan/{noPesanan}/status', [KonsumenApiController::class, 'statusPesanan']);
    Route::post('/bayar', [KonsumenApiController::class, 'bayar']);
    // Beranda (QR scan landing) — must be last to avoid catching other routes
    Route::get('/{noMeja}', [KonsumenApiController::class, 'beranda']);
});
