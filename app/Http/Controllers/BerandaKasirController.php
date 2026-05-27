<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Class BerandaKasirController
 * 
 * Controller ini melayani beranda panel kendali untuk Kasir (Staff Dapur/Kasir).
 * Menyajikan statistik waktu-nyata mengenai antrean pesanan hari ini yang dikelompokkan
 * berdasarkan status pengerjaan (menunggu konfirmasi, diproses, selesai), metrik rata-rata belanja,
 * data terlaris harian untuk makanan dan minuman, serta diagram kepopuleran jam sibuk pemesanan.
 *
 * @package App\Http\Controllers
 */
class BerandaKasirController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard Kasir dengan agregasi metrik antrean harian.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $today = Carbon::today();

        // 1. Menghitung jumlah pesanan hari ini dengan status 'menunggu konfirmasi'
        $menunggu = Pesanan::where('status_pesanan', 'menunggu konfirmasi')
            ->whereDate('tgl_pembayaran', $today)
            ->count();

        // 2. Menghitung jumlah pesanan hari ini dengan status 'diproses' di dapur
        $diproses = Pesanan::where('status_pesanan', 'diproses')
            ->whereDate('tgl_pembayaran', $today)
            ->count();

        // 3. Menghitung jumlah pesanan hari ini yang telah 'selesai' disajikan
        $selesai = Pesanan::where('status_pesanan', 'selesai')
            ->whereDate('tgl_pembayaran', $today)
            ->count();

        // 4. Agregasi porsi menu selesai terjual hari ini, dikelompokkan berdasarkan jenis (Makanan/Minuman)
        $selesaiSplit = DB::table('detail_pesanan')
            ->join('pesanan', 'detail_pesanan.no_pesanan', '=', 'pesanan.no_pesanan')
            ->join('menu', 'detail_pesanan.id_menu', '=', 'menu.id_menu')
            ->where('pesanan.status_pesanan', 'selesai')
            ->whereDate('pesanan.tgl_pembayaran', $today)
            ->select('menu.jenis_menu', DB::raw('SUM(detail_pesanan.jumlah) as total'))
            ->groupBy('menu.jenis_menu')
            ->pluck('total', 'jenis_menu');

        // Ekstraksi nilai jumlah porsi makanan & minuman terpisah
        $selesaiMinuman = (int) ($selesaiSplit['Minuman'] ?? 0);
        $selesaiMakanan = (int) ($selesaiSplit['Makanan'] ?? 0);

        // 5. Total antrean pesanan yang masih harus dikerjakan saat ini
        $pesananAktif = $menunggu + $diproses;

        // 6. Total transaksi lunas dan total omzet kotor sepanjang masa untuk perhitungan rata-rata belanja
        $totalTransaksi = Pesanan::where('status_pembayaran', 'lunas')->count();
        $omzetTotal     = (int) Pesanan::where('status_pembayaran', 'lunas')->sum('total_harga');
        
        // Perhitungan rata-rata nilai pembelian (basket size) per transaksi (menghindari division by zero)
        $rataPembelian  = $totalTransaksi > 0 ? (int) round($omzetTotal / $totalTransaksi) : 0;

        // 7. Agregasi Makanan terlaris yang dipesan khusus hari ini
        $makananTerlaris = DB::table('detail_pesanan')
            ->join('pesanan', 'detail_pesanan.no_pesanan', '=', 'pesanan.no_pesanan')
            ->join('menu', 'detail_pesanan.id_menu', '=', 'menu.id_menu')
            ->select('menu.nama_menu', 'menu.gambar_menu', DB::raw('SUM(detail_pesanan.jumlah) as total_terjual'))
            ->where('menu.jenis_menu', 'Makanan')
            ->whereDate('pesanan.tgl_pembayaran', $today)
            ->groupBy('menu.id_menu', 'menu.nama_menu', 'menu.gambar_menu')
            ->orderByDesc('total_terjual')
            ->first();

        // 8. Agregasi Minuman terlaris yang dipesan khusus hari ini
        $minumanTerlaris = DB::table('detail_pesanan')
            ->join('pesanan', 'detail_pesanan.no_pesanan', '=', 'pesanan.no_pesanan')
            ->join('menu', 'detail_pesanan.id_menu', '=', 'menu.id_menu')
            ->select('menu.nama_menu', 'menu.gambar_menu', DB::raw('SUM(detail_pesanan.jumlah) as total_terjual'))
            ->where('menu.jenis_menu', 'Minuman')
            ->whereDate('pesanan.tgl_pembayaran', $today)
            ->groupBy('menu.id_menu', 'menu.nama_menu', 'menu.gambar_menu')
            ->orderByDesc('total_terjual')
            ->first();

        // 9. Chart Analitik A: Kepadatan Pesanan Masuk per Jam hari ini (Fokus jam operasional 08:00 - 17:00)
        $pesananPerJam = DB::table('pesanan')
            ->select(DB::raw('HOUR(tgl_pembayaran) as jam'), DB::raw('COUNT(*) as total'))
            ->whereDate('tgl_pembayaran', $today)
            ->groupBy(DB::raw('HOUR(tgl_pembayaran)'))
            ->get()
            ->keyBy('jam');

        $jamLabels = [];
        $jamData   = [];
        for ($h = 8; $h <= 17; $h++) {
            $jamLabels[] = sprintf('%02d:00', $h);
            $jamData[]   = (int) ($pesananPerJam[$h]->total ?? 0);
        }

        // 10. Chart Analitik B: Pendapatan Harian Minggu Ini (Senin s.d. Minggu)
        $startOfWeek   = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $pendapatanRaw = DB::table('pesanan')
            ->select(DB::raw('DATE(tgl_pembayaran) as tanggal'), DB::raw('SUM(total_harga) as total'))
            ->where('status_pembayaran', 'lunas')
            ->whereBetween('tgl_pembayaran', [$startOfWeek->toDateString(), Carbon::now()->endOfDay()])
            ->groupBy(DB::raw('DATE(tgl_pembayaran)'))
            ->get()
            ->keyBy('tanggal');

        $hariNama       = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $hariLabels     = [];
        $pendapatanData = [];
        for ($i = 0; $i < 7; $i++) {
            $date             = $startOfWeek->copy()->addDays($i);
            $hariLabels[]     = $hariNama[$i];
            $pendapatanData[] = (int) ($pendapatanRaw[$date->format('Y-m-d')]->total ?? 0);
        }

        // 11. Mengembalikan view utama kasir dengan menyertakan seluruh variabel analitik
        return view('kasir.beranda', compact(
            'menunggu', 'diproses', 'selesai',
            'selesaiMakanan', 'selesaiMinuman',
            'pesananAktif', 'totalTransaksi', 'omzetTotal', 'rataPembelian',
            'makananTerlaris', 'minumanTerlaris',
            'jamLabels', 'jamData',
            'hariLabels', 'pendapatanData'
        ));
    }
}
