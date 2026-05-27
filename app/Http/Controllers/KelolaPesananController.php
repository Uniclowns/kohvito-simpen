<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\View\View;

/**
 * Class KelolaPesananController
 * 
 * Controller ini mengatur manajemen antrean pesanan aktif di dapur kafe (Panel Kasir).
 * Bertugas menarik antrean berjalan (antrean berstatus 'menunggu konfirmasi' dan 'diproses'),
 * melihat spesifikasi rincian pesanan, mencetak struk pemesanan PDF, serta mengamankan alur transisi
 * status pengerjaan makanan/minuman menggunakan pola State Machine (Mesin State terproteksi).
 *
 * @package App\Http\Controllers
 */
class KelolaPesananController extends Controller
{
    /**
     * Tampilkan antrean pesanan aktif yang harus dikerjakan dapur.
     * Diurutkan dari waktu masuk yang terlama (FIFO - First In First Out) agar adil.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // 1. Ambil pesanan yang berstatus 'menunggu konfirmasi' atau 'diproses' beserta relasi meja dan item
        $pesanans = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->whereIn('status_pesanan', ['menunggu konfirmasi', 'diproses'])
            ->orderBy('tgl_pembayaran', 'asc') // Urutan FIFO berdasarkan waktu pembayaran/masuk
            ->get();

        return view('kasir.kelola-pesanan', compact('pesanans'));
    }

    /**
     * Tampilkan detail pesanan yang sedang aktif di antrean.
     *
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi
     * @return \Illuminate\View\View
     */
    public function detail(string $noPesanan): View
    {
        // 1. Ambil detail data jika pesanan tersebut berstatus aktif
        $pesanan = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('no_pesanan', $noPesanan)
            ->whereIn('status_pesanan', ['menunggu konfirmasi', 'diproses'])
            ->firstOrFail();

        return view('kasir.kelola-pesanan-detail', compact('pesanan'));
    }

    /**
     * Mengubah status pengerjaan pesanan menggunakan pola State Machine (transisi berurutan).
     * Mencegah lompatan status (misal dari menunggu konfirmasi langsung selesai) demi konsistensi.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, string $noPesanan): RedirectResponse
    {
        $pesanan = Pesanan::where('no_pesanan', $noPesanan)->firstOrFail();

        // 1. Kamus Aturan Transisi Status (State Machine Kamus)
        //    Menjamin status hanya boleh melangkah maju satu demi satu tingkatan
        $transitions = [
            'menunggu konfirmasi' => 'diproses',
            'diproses'            => 'selesai',
        ];

        // 2. Tentukan status selanjutnya berdasarkan status pesanan saat ini
        $nextStatus = $transitions[$pesanan->status_pesanan] ?? null;

        // 3. Batalkan aksi jika transisi tidak valid (misal: pesanan sudah dibatalkan atau selesai)
        if (! $nextStatus) {
            return back()->with('error', 'Status pesanan tidak dapat diubah.');
        }

        // 4. Perbarui dan simpan status baru ke database
        $pesanan->status_pesanan = $nextStatus;
        $pesanan->save();

        // 5. Tentukan label pesan notifikasi interaktif untuk dilemparkan ke client
        $isAccepted = $nextStatus === 'diproses';
        $message    = $isAccepted
            ? 'Pesanan diterima dan sedang diproses.'
            : 'Pesanan telah selesai.';

        return redirect()
            ->route('kasir.pesanan.index')
            ->with('success', $message)
            ->with('order_action', $isAccepted ? 'accepted' : 'completed');
    }

    /**
     * Cetak kuitansi struk antrean pesanan dalam format PDF untuk dapur/pelanggan.
     * Disajikan dalam bentuk stream (preview) PDF di browser.
     *
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi
     * @return \Illuminate\Http\Response  Objek file stream PDF kuitansi/struk
     */
    public function cetakPesanan(string $noPesanan): HttpResponse
    {
        // 1. Mengambil data transaksi beserta relasi detail item
        $pesanan = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('no_pesanan', $noPesanan)
            ->firstOrFail();

        // 2. Load template Blade cetak nota dan alirkan file PDF ke browser
        return Pdf::loadView('kasir.cetak-pesanan-pdf', compact('pesanan'))
            ->stream("struk-{$noPesanan}.pdf");
    }
}
