<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BerandaAdminController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // Date-range filter dari Filter modal (default: hari ini)
        $filterMulai = $request->filled('tanggal_mulai')
            ? Carbon::parse($request->query('tanggal_mulai'))->startOfDay()
            : $today->copy()->startOfDay();

        $filterSelesai = $request->filled('tanggal_selesai')
            ? Carbon::parse($request->query('tanggal_selesai'))->endOfDay()
            : $today->copy()->endOfDay();

        $omzetHariIni = Pesanan::where('status_pembayaran', 'lunas')
            ->whereBetween('tgl_pembayaran', [$filterMulai, $filterSelesai])
            ->sum('total_harga');

        $omzetBulanIni = Pesanan::where('status_pembayaran', 'lunas')
            ->whereYear('tgl_pembayaran', $today->year)
            ->whereMonth('tgl_pembayaran', $today->month)
            ->sum('total_harga');

        $totalMenu     = Menu::count();
        $totalKasir    = User::where('id_role', 2)->count();
        $totalTransaksi = Pesanan::where('status_pembayaran', 'lunas')->count();
        $pesananDiproses = Pesanan::whereIn('status_pesanan', ['menunggu konfirmasi', 'diproses'])->count();
        $orderStatus   = Cache::get('order_status', 'buka');

        $makananTerlaris = DB::table('detail_pesanan')
            ->join('menu', 'detail_pesanan.id_menu', '=', 'menu.id_menu')
            ->select('menu.nama_menu', 'menu.gambar_menu', DB::raw('SUM(detail_pesanan.jumlah) as total_terjual'))
            ->where('menu.jenis_menu', 'Makanan')
            ->groupBy('menu.id_menu', 'menu.nama_menu', 'menu.gambar_menu')
            ->orderByDesc('total_terjual')
            ->first();

        $minumanTerlaris = DB::table('detail_pesanan')
            ->join('menu', 'detail_pesanan.id_menu', '=', 'menu.id_menu')
            ->select('menu.nama_menu', 'menu.gambar_menu', DB::raw('SUM(detail_pesanan.jumlah) as total_terjual'))
            ->where('menu.jenis_menu', 'Minuman')
            ->groupBy('menu.id_menu', 'menu.nama_menu', 'menu.gambar_menu')
            ->orderByDesc('total_terjual')
            ->first();

        $pesananHariIni = Pesanan::with(['meja', 'user', 'detailPesanan.menu'])
            ->whereBetween('tgl_pembayaran', [$filterMulai, $filterSelesai])
            ->orderByDesc('tgl_pembayaran')
            ->get();

        // Chart: pesanan per jam hari ini (08:00–22:00)
        $pesananPerJam = DB::table('pesanan')
            ->select(DB::raw('HOUR(tgl_pembayaran) as jam'), DB::raw('COUNT(*) as total'))
            ->whereDate('tgl_pembayaran', $today)
            ->groupBy(DB::raw('HOUR(tgl_pembayaran)'))
            ->get()->keyBy('jam');

        $jamLabels = [];
        $jamData   = [];
        for ($h = 8; $h <= 22; $h++) {
            $jamLabels[] = sprintf('%02d:00', $h);
            $jamData[]   = (int) ($pesananPerJam[$h]->total ?? 0);
        }

        // Chart: pendapatan per hari minggu ini
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $pendapatanRaw = DB::table('pesanan')
            ->select(DB::raw('DATE(tgl_pembayaran) as tanggal'), DB::raw('SUM(total_harga) as total'))
            ->where('status_pembayaran', 'lunas')
            ->whereBetween('tgl_pembayaran', [$startOfWeek->toDateString(), Carbon::now()->endOfDay()])
            ->groupBy(DB::raw('DATE(tgl_pembayaran)'))
            ->get()->keyBy('tanggal');

        $hariNama      = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $hariLabels    = [];
        $pendapatanData = [];
        for ($i = 0; $i < 7; $i++) {
            $date            = $startOfWeek->copy()->addDays($i);
            $hariLabels[]    = $hariNama[$i];
            $pendapatanData[] = (int) ($pendapatanRaw[$date->format('Y-m-d')]->total ?? 0);
        }

        return view('admin.beranda', compact(
            'omzetHariIni', 'omzetBulanIni',
            'totalMenu', 'totalKasir', 'totalTransaksi', 'pesananDiproses', 'orderStatus',
            'makananTerlaris', 'minumanTerlaris',
            'pesananHariIni',
            'jamLabels', 'jamData',
            'hariLabels', 'pendapatanData'
        ));
    }

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

    public function toggleOrderStatus()
    {
        $current = Cache::get('order_status', 'buka');
        $new     = $current === 'buka' ? 'tutup' : 'buka';
        Cache::forever('order_status', $new);

        $message = $new === 'tutup' ? 'Pemesanan berhasil ditutup.' : 'Pemesanan berhasil dibuka.';

        return redirect()->route('admin.beranda')
            ->with('success', $message)
            ->with('store_status_changed_to', $new);
    }

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
