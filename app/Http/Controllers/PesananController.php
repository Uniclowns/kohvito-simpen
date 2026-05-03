<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;

class PesananController extends Controller
{
    public function index(string $noPesanan)
    {
        $pesanan = Pesanan::with(['detailPesanan.menu', 'meja'])->find($noPesanan);

        if (! $pesanan) {
            abort(404);
        }

        return view('konsumen.pesanan', compact('pesanan'));
    }

    public function status(string $noPesanan)
    {
        $pesanan = Pesanan::select('no_pesanan', 'status_pesanan', 'status_pembayaran', 'tgl_pembayaran')
            ->find($noPesanan);

        if (! $pesanan) {
            return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
        }

        return response()->json($pesanan);
    }

    public function kuitansi(string $noPesanan)
    {
        $pesanan = Pesanan::with(['detailPesanan.menu', 'meja'])->find($noPesanan);

        if (! $pesanan) {
            abort(404);
        }

        if ($pesanan->status_pembayaran !== 'lunas') {
            abort(403, 'Kuitansi hanya tersedia setelah pembayaran lunas.');
        }

        $pdf = Pdf::loadView('konsumen.kuitansi', compact('pesanan'))
            ->setPaper('a5', 'portrait');

        return $pdf->download('kuitansi-' . $pesanan->no_pesanan . '.pdf');
    }
}
