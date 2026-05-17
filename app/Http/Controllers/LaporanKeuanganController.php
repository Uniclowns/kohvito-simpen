<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class LaporanKeuanganController extends Controller
{
    public function index(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai')
            ? Carbon::parse($request->input('tanggal_mulai'))->startOfDay()
            : Carbon::today()->startOfDay();

        $tanggalSelesai = $request->input('tanggal_selesai')
            ? Carbon::parse($request->input('tanggal_selesai'))->endOfDay()
            : Carbon::today()->endOfDay();

        $pesanans = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('status_pembayaran', 'lunas')
            ->whereBetween('tgl_pembayaran', [$tanggalMulai, $tanggalSelesai])
            ->get();

        $totalOmzet = $pesanans->sum('total_harga');
        $totalTransaksi = $pesanans->count();

        // Menu terlaris
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

        return view('admin.laporan-keuangan', compact(
            'pesanans', 'totalOmzet', 'totalTransaksi', 'menuTerlaris',
            'tanggalMulai', 'tanggalSelesai'
        ));
    }

    public function exportPdf(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai')
            ? Carbon::parse($request->input('tanggal_mulai'))->startOfDay()
            : Carbon::today()->startOfDay();

        $tanggalSelesai = $request->input('tanggal_selesai')
            ? Carbon::parse($request->input('tanggal_selesai'))->endOfDay()
            : Carbon::today()->endOfDay();

        $pesanans = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('status_pembayaran', 'lunas')
            ->whereBetween('tgl_pembayaran', [$tanggalMulai, $tanggalSelesai])
            ->get();

        $totalOmzet = $pesanans->sum('total_harga');
        $totalTransaksi = $pesanans->count();

        $pdf = Pdf::loadView('admin.laporan-keuangan-pdf', compact(
            'pesanans', 'totalOmzet', 'totalTransaksi', 'tanggalMulai', 'tanggalSelesai'
        ));

        return $pdf->download('laporan-keuangan.pdf');
    }

    public function exportExcel(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai')
            ? Carbon::parse($request->input('tanggal_mulai'))->startOfDay()
            : Carbon::today()->startOfDay();

        $tanggalSelesai = $request->input('tanggal_selesai')
            ? Carbon::parse($request->input('tanggal_selesai'))->endOfDay()
            : Carbon::today()->endOfDay();

        $pesanans = Pesanan::with(['meja'])
            ->where('status_pembayaran', 'lunas')
            ->whereBetween('tgl_pembayaran', [$tanggalMulai, $tanggalSelesai])
            ->get()
            ->map(fn($p) => [
                'No Pesanan'       => $p->no_pesanan,
                'Nama Konsumen'    => $p->nama_konsumen,
                'No Meja'          => $p->meja?->no_meja ?? '-',
                'Total Harga'      => $p->total_harga,
                'Tgl Pembayaran'   => $p->tgl_pembayaran?->format('Y-m-d H:i'),
            ]);

        return (new FastExcel($pesanans))->download('laporan-keuangan.xlsx');
    }
}
