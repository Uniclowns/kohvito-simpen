<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BerandaAdminController extends Controller
{
    /**
     * Tampilkan halaman dashboard admin dengan ringkasan omzet.
     */
    public function index()
    {
        $today = Carbon::today();

        $omzetHariIni = Pesanan::where('status_pembayaran', 'lunas')
            ->whereDate('tgl_pembayaran', $today)
            ->sum('total_harga');

        $omzetBulanIni = Pesanan::where('status_pembayaran', 'lunas')
            ->whereYear('tgl_pembayaran', $today->year)
            ->whereMonth('tgl_pembayaran', $today->month)
            ->sum('total_harga');

        $totalPesananHariIni = Pesanan::whereDate('tgl_pembayaran', $today)
            ->count();

        return view('admin.beranda', compact('omzetHariIni', 'omzetBulanIni', 'totalPesananHariIni'));
    }

    /**
     * Endpoint JSON: data grafik omzet per hari untuk 30 hari terakhir.
     */
    public function getData(Request $request)
    {
        $data = DB::table('pesanan')
            ->select(DB::raw('DATE(tgl_pembayaran) as tanggal'), DB::raw('SUM(total_harga) as total'))
            ->where('status_pembayaran', 'lunas')
            ->where('tgl_pembayaran', '>=', DB::raw('NOW() - INTERVAL 30 DAY'))
            ->groupBy(DB::raw('DATE(tgl_pembayaran)'))
            ->orderBy('tanggal', 'asc')
            ->get();

        return response()->json($data);
    }

    /**
     * Generate dan download laporan kasir (PDF).
     */
    public function cetakLaporanKasir(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : Carbon::today()->startOfDay();

        $tanggalSelesai = $request->tanggal_selesai
            ? Carbon::parse($request->tanggal_selesai)->endOfDay()
            : Carbon::today()->endOfDay();

        $pesanan = Pesanan::with(['meja', 'user'])
            ->where('status_pembayaran', 'lunas')
            ->whereBetween('tgl_pembayaran', [$tanggalMulai, $tanggalSelesai])
            ->get();

        return Pdf::loadView('admin.laporan-kasir-pdf', compact('pesanan', 'tanggalMulai', 'tanggalSelesai'))
            ->download('laporan-kasir.pdf');
    }
}
