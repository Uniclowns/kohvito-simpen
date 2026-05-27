<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Class BerandaAdminController
 * 
 * Controller ini mengatur dashboard kontrol pusat bagi Administrator (Admin).
 * Bertugas menyediakan data analitik penjualan, rekapitulasi data master (menu, kasir),
 * status penerimaan pesanan global (buka/tutup toko), performa grafik penjualan berkala,
 * pencarian menu terlaris mingguan, hingga penarikan cetak laporan kasir berbasis PDF.
 *
 * @package App\Http\Controllers
 */
class BerandaAdminController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard Admin beserta visualisasi analitiknya.
     * Mendukung pemfilteran berbasis rentang tanggal (default: hari ini).
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP Request pembawa parameter filter tanggal
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $today = Carbon::today();

        // 1. Parsing filter tanggal mulai dan selesai dari form pencarian dashboard.
        //    Jika tidak diisi, kembalikan ke default rentang waktu hari ini (00:00:00 s/d 23:59:59).
        $filterMulai = $request->filled('tanggal_mulai')
            ? Carbon::parse($request->query('tanggal_mulai'))->startOfDay()
            : $today->copy()->startOfDay();

        $filterSelesai = $request->filled('tanggal_selesai')
            ? Carbon::parse($request->query('tanggal_selesai'))->endOfDay()
            : $today->copy()->endOfDay();

        // 2. Akumulasi omzet penjualan lunas dalam rentang tanggal terfilter
        $omzetHariIni = Pesanan::where('status_pembayaran', 'lunas')
            ->whereBetween('tgl_pembayaran', [$filterMulai, $filterSelesai])
            ->sum('total_harga');

        // 3. Akumulasi omzet penjualan lunas khusus bulan berjalan (Mulai 1 s/d hari terakhir bulan ini)
        $omzetBulanIni = Pesanan::where('status_pembayaran', 'lunas')
            ->whereYear('tgl_pembayaran', $today->year)
            ->whereMonth('tgl_pembayaran', $today->month)
            ->sum('total_harga');

        // 4. Perhitungan statistik data master dan antrean aktif
        $totalMenu       = Menu::count();
        $totalKasir      = User::where('id_role', 2)->count(); // ID Role 2 mewakili peran Kasir
        $totalTransaksi  = Pesanan::where('status_pembayaran', 'lunas')->count();
        $pesananDiproses = Pesanan::whereIn('status_pesanan', ['menunggu konfirmasi', 'diproses'])->count();
        
        // 5. Cek status operasional pemesanan global dari cache sistem
        $orderStatus     = Cache::get('order_status', 'buka');

        // 6. Mencari menu kategori Makanan yang paling banyak dibeli (Best Seller Makanan)
        $makananTerlaris = DB::table('detail_pesanan')
            ->join('menu', 'detail_pesanan.id_menu', '=', 'menu.id_menu')
            ->select('menu.nama_menu', 'menu.gambar_menu', DB::raw('SUM(detail_pesanan.jumlah) as total_terjual'))
            ->where('menu.jenis_menu', 'Makanan')
            ->groupBy('menu.id_menu', 'menu.nama_menu', 'menu.gambar_menu')
            ->orderByDesc('total_terjual')
            ->first();

        // 7. Mencari menu kategori Minuman yang paling banyak dibeli (Best Seller Minuman)
        $minumanTerlaris = DB::table('detail_pesanan')
            ->join('menu', 'detail_pesanan.id_menu', '=', 'menu.id_menu')
            ->select('menu.nama_menu', 'menu.gambar_menu', DB::raw('SUM(detail_pesanan.jumlah) as total_terjual'))
            ->where('menu.jenis_menu', 'Minuman')
            ->groupBy('menu.id_menu', 'menu.nama_menu', 'menu.gambar_menu')
            ->orderByDesc('total_terjual')
            ->first();

        // 8. Menarik daftar pesanan yang lunas dalam rentang tanggal filter saat ini
        $pesananHariIni = Pesanan::with(['meja', 'user', 'detailPesanan.menu'])
            ->whereBetween('tgl_pembayaran', [$filterMulai, $filterSelesai])
            ->orderByDesc('tgl_pembayaran')
            ->get();

        // 9. Chart Analitik A: Pola Kepadatan Pemesanan per Jam Hari Ini (08:00 - 22:00)
        $pesananPerJam = DB::table('pesanan')
            ->select(DB::raw('HOUR(tgl_pembayaran) as jam'), DB::raw('COUNT(*) as total'))
            ->whereDate('tgl_pembayaran', $today)
            ->groupBy(DB::raw('HOUR(tgl_pembayaran)'))
            ->get()
            ->keyBy('jam');

        $jamLabels = [];
        $jamData   = [];
        for ($h = 8; $h <= 22; $h++) {
            $jamLabels[] = sprintf('%02d:00', $h);
            $jamData[]   = (int) ($pesananPerJam[$h]->total ?? 0);
        }

        // 10. Chart Analitik B: Grafik Pendapatan Mingguan Berjalan (Senin - Minggu)
        $startOfWeek   = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $pendapatanRaw = DB::table('pesanan')
            ->select(DB::raw('DATE(tgl_pembayaran) as tanggal'), DB::raw('SUM(total_harga) as total'))
            ->where('status_pembayaran', 'lunas')
            ->whereBetween('tgl_pembayaran', [$startOfWeek->toDateString(), Carbon::now()->endOfDay()])
            ->groupBy(DB::raw('DATE(tgl_pembayaran)'))
            ->get()
            ->keyBy('tanggal');

        $hariNama       = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $hariLabels     = [];
        $pendapatanData = [];
        for ($i = 0; $i < 7; $i++) {
            $date             = $startOfWeek->copy()->addDays($i);
            $hariLabels[]     = $hariNama[$i];
            $pendapatanData[] = (int) ($pendapatanRaw[$date->format('Y-m-d')]->total ?? 0);
        }

        // 11. Mengembalikan view dengan melemparkan compact data analisis penjualan
        return view('admin.beranda', compact(
            'omzetHariIni', 'omzetBulanIni',
            'totalMenu', 'totalKasir', 'totalTransaksi', 'pesananDiproses', 'orderStatus',
            'makananTerlaris', 'minumanTerlaris',
            'pesananHariIni',
            'jamLabels', 'jamData',
            'hariLabels', 'pendapatanData'
        ));
    }

    /**
     * Endpoint API JSON: Menyediakan data histori tren omzet omzet penjualan selama 30 hari terakhir.
     * Berguna untuk rendering diagram/chart interaktif eksternal.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request): JsonResponse
    {
        // 1. Agregasi total tagihan per tanggal untuk transaksi yang lunas selama 30 hari ke belakang
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
     * Mengubah status pemesanan global (Buka/Tutup Toko) secara instan.
     * Menggunakan caching permanen agar langsung terbaca oleh seluruh middleware konsumen.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleOrderStatus(): RedirectResponse
    {
        // 1. Membaca status toko saat ini, fallback ke default 'buka'
        $current = Cache::get('order_status', 'buka');
        // 2. Membalikkan state status
        $new     = $current === 'buka' ? 'tutup' : 'buka';
        
        // 3. Simpan state baru secara permanen di cache server
        Cache::forever('order_status', $new);

        $message = $new === 'tutup' ? 'Pemesanan berhasil ditutup.' : 'Pemesanan berhasil dibuka.';

        // 4. Kembali ke halaman sebelumnya dengan flash data notifikasi sukses
        return redirect()->route('admin.beranda')
            ->with('success', $message)
            ->with('store_status_changed_to', $new);
    }

    /**
     * Membuat dokumen cetak laporan penutupan kasir dalam format PDF.
     *
     * @param  \Illuminate\Http\Request  $request  Objek data form filter cetak laporan
     * @return \Symfony\Component\HttpFoundation\Response  Pengunduhan berkas PDF laporan kasir
     */
    public function cetakLaporanKasir(Request $request): HttpResponse
    {
        // 1. Mengidentifikasi rentang waktu laporan kasir yang diinginkan
        $tanggalMulai = $request->tanggal_mulai
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : Carbon::today()->startOfDay();

        $tanggalSelesai = $request->tanggal_selesai
            ? Carbon::parse($request->tanggal_selesai)->endOfDay()
            : Carbon::today()->endOfDay();

        // 2. Query data transaksi lunas pada tanggal terpilih beserta relasi meja dan kasir
        $pesanan = Pesanan::with(['meja', 'user'])
            ->where('status_pembayaran', 'lunas')
            ->whereBetween('tgl_pembayaran', [$tanggalMulai, $tanggalSelesai])
            ->get();

        // 3. Memanfaatkan DomPDF untuk merender template Blade menjadi file PDF secara instan untuk diunduh
        return Pdf::loadView('admin.laporan-kasir-pdf', compact('pesanan', 'tanggalMulai', 'tanggalSelesai'))
            ->download('laporan-kasir.pdf');
    }
}
