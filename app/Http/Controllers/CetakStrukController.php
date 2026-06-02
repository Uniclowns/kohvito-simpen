<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Services\ThermalReceiptPrinter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class CetakStrukController
 *
 * Menangani pencetakan struk pesanan secara langsung ke printer termal jaringan
 * (ESC/POS) dari panel Kasir. Berbeda dengan cetak PDF lama yang membuka tab
 * browser, endpoint ini dipanggil via AJAX (POST) sehingga kasir tetap berada
 * di halaman dan struk langsung keluar dari printer.
 *
 * Satu endpoint melayani kedua layar (antrean aktif & histori) karena proses
 * pencetakannya identik — keduanya hanya butuh nomor pesanan.
 *
 * @package App\Http\Controllers
 */
class CetakStrukController extends Controller
{
    /**
     * Cetak struk satu pesanan langsung ke printer termal.
     *
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi.
     * @param  \App\Services\ThermalReceiptPrinter  $printer
     * @return \Illuminate\Http\JsonResponse
     */
    public function cetak(string $noPesanan, ThermalReceiptPrinter $printer): JsonResponse
    {
        // 1. Ambil pesanan beserta relasi yang dibutuhkan struk
        $pesanan = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('no_pesanan', $noPesanan)
            ->firstOrFail();

        // 2. Kirim ke printer; tangani kegagalan jaringan/printer dengan rapi
        try {
            $printer->print($pesanan);
        } catch (Throwable $e) {
            Log::error('Cetak struk termal gagal', [
                'no_pesanan' => $noPesanan,
                'error'      => $e->getMessage(),
            ]);

            return response()->json([
                'ok'      => false,
                'message' => 'Printer tidak terhubung. Periksa printer & jaringan, lalu coba lagi.',
            ], 503);
        }

        // 3. Sukses — beri umpan balik untuk toast di sisi klien
        return response()->json([
            'ok'      => true,
            'message' => 'Struk berhasil dicetak.',
        ]);
    }
}
