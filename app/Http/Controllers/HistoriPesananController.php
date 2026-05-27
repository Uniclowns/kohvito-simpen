<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\View\View;

/**
 * Class HistoriPesananController
 * 
 * Controller ini melayani panel riwayat (histori) transaksi kasir.
 * Menyediakan fitur pelacakan pesanan yang berstatus 'selesai' hari ini, pencarian tingkat lanjut
 * berbasis penutupan logika (Closure) untuk multi-kolom kata kunci, penarikan detil nota transaksi kasir,
 * pencetakan kuitansi tunggal PDF, hingga pencetakan rekap seluruh transaksi harian.
 *
 * @package App\Http\Controllers
 */
class HistoriPesananController extends Controller
{
    /**
     * Tampilkan daftar seluruh histori transaksi pesanan yang telah selesai khusus hari ini.
     * Mendukung pencarian dinamis (kata kunci no_pesanan / nama_konsumen).
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request pembawa parameter kata kunci pencarian
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $today  = Carbon::today();
        $search = $request->input('search');

        // 1. Inisialisasi query dasar: pesanan berstatus selesai dan diselesaikan khusus tanggal hari ini
        $query = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('status_pesanan', 'selesai')
            ->whereDate('tgl_pembayaran', $today);

        // 2. Teknik Pencarian Tingkat Lanjut Berbasis Closure (Fungsi Anonim)
        //    Pencarian dibungkus di dalam query bersarang AND (no_pesanan LIKE ? OR nama_konsumen LIKE ?)
        //    agar operator OR tidak merusak / membatalkan filter penentu utama di luar tanda kurung SQL.
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_pesanan', 'like', "%{$search}%")
                  ->orWhere('nama_konsumen', 'like', "%{$search}%");
            });
        }

        // 3. Ambil data dengan urutan waktu pelunasan terbaru di paling atas
        $pesanans = $query->orderBy('tgl_pembayaran', 'desc')->get();
        
        // 4. Kalkulasi omzet tagihan bersih hasil penjualan yang terkumpul hari ini
        $totalOmzet = $pesanans->sum('total_harga');

        // 5. Kembalikan view riwayat pesanan dengan membawa parameter hasil
        return view('kasir.histori-pesanan', compact('pesanans', 'search', 'totalOmzet'));
    }

    /**
     * Tampilkan detail rincian item pesanan dari histori yang telah selesai.
     *
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi
     * @return \Illuminate\View\View
     */
    public function detail(string $noPesanan): View
    {
        // 1. Mencari spesifik pesanan beserta relasi meja dan item terikat yang berstatus selesai
        $pesanan = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('no_pesanan', $noPesanan)
            ->where('status_pesanan', 'selesai')
            ->firstOrFail();

        return view('kasir.histori-pesanan-detail', compact('pesanan'));
    }

    /**
     * Cetak kuitansi nota pembayaran PDF tunggal untuk pesanan tertentu.
     * Disajikan dalam bentuk stream (preview) PDF di browser.
     *
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi
     * @return \Illuminate\Http\Response  Objek file stream PDF kuitansi tunggal
     */
    public function cetakHistoriPesanan(string $noPesanan): HttpResponse
    {
        // 1. Mengambil data transaksi beserta relasi detail item
        $pesanan = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('no_pesanan', $noPesanan)
            ->firstOrFail();

        // 2. Load template Blade cetak nota dan konversikan ke file PDF secara server-side
        return Pdf::loadView('kasir.cetak-pesanan-pdf', compact('pesanan'))
            ->stream("histori-{$noPesanan}.pdf");
    }

    /**
     * Cetak rekapitulasi seluruh laporan transaksi yang selesai pada hari ini dalam bentuk PDF.
     * Disajikan dalam bentuk stream (preview) PDF di browser.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request aktif
     * @return \Illuminate\Http\Response  Objek file stream PDF rekapitulasi transaksi harian
     */
    public function cetakSemuaHistoriPesanan(Request $request): HttpResponse
    {
        $today = Carbon::today();

        // 1. Ambil seluruh daftar pesanan selesai terbayar hari ini dengan urutan waktu menaik
        $pesanans = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('status_pesanan', 'selesai')
            ->whereDate('tgl_pembayaran', $today)
            ->orderBy('tgl_pembayaran', 'asc')
            ->get();

        // 2. Jumlah total omzet bersih yang diraih
        $totalOmzet = $pesanans->sum('total_harga');

        // 3. Load template Blade cetak rekap dan alirkan file PDF ke browser
        return Pdf::loadView('kasir.cetak-histori-pdf', compact('pesanans', 'totalOmzet', 'today'))
            ->stream("rekap-histori-{$today->format('Y-m-d')}.pdf");
    }
}
