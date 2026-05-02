<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HistoriPesananController extends Controller
{
    public function index(Request $request)
    {
        $today  = Carbon::today();
        $search = $request->input('search');

        $query = Pesanan::with(['meja'])
            ->where('status_pesanan', 'selesai')
            ->whereDate('tgl_pembayaran', $today);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_pesanan', 'like', "%{$search}%")
                  ->orWhere('nama_konsumen', 'like', "%{$search}%");
            });
        }

        $pesanans   = $query->orderBy('tgl_pembayaran', 'desc')->get();
        $totalOmzet = $pesanans->sum('total_harga');

        return view('kasir.histori-pesanan', compact('pesanans', 'search', 'totalOmzet'));
    }

    public function detail(string $noPesanan)
    {
        $pesanan = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('no_pesanan', $noPesanan)
            ->where('status_pesanan', 'selesai')
            ->firstOrFail();

        return view('kasir.histori-pesanan-detail', compact('pesanan'));
    }

    public function cetakHistoriPesanan(string $noPesanan)
    {
        $pesanan = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('no_pesanan', $noPesanan)
            ->firstOrFail();

        return Pdf::loadView('kasir.cetak-pesanan-pdf', compact('pesanan'))
            ->stream("histori-{$noPesanan}.pdf");
    }

    public function cetakSemuaHistoriPesanan(Request $request)
    {
        $today = Carbon::today();

        $pesanans = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('status_pesanan', 'selesai')
            ->whereDate('tgl_pembayaran', $today)
            ->orderBy('tgl_pembayaran', 'asc')
            ->get();

        $totalOmzet = $pesanans->sum('total_harga');

        return Pdf::loadView('kasir.cetak-histori-pdf', compact('pesanans', 'totalOmzet', 'today'))
            ->stream("rekap-histori-{$today->format('Y-m-d')}.pdf");
    }
}
