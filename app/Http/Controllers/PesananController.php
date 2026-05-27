<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Class PesananController
 * 
 * Controller ini mengatur manajemen histori pemesanan, pelacakan proses pembuatan hidangan,
 * pembatalan pesanan yang belum terkonfirmasi, cetak kuitansi PDF mandiri,
 * serta polling sinkronisasi status transaksi di antarmuka konsumen.
 *
 * Memanfaatkan riwayat nomor transaksi berbasis session array (`riwayat_pesanan`) agar
 * pembeli tetap dapat melacak histori transaksinya meski browser ditutup/sesi belanja di-reset.
 *
 * @package App\Http\Controllers
 */
class PesananController extends Controller
{
    /**
     * Tampilkan seluruh daftar pesanan konsumen dalam sesi aktif saat ini.
     * Jika kosong, sistem menyajikan antarmuka empty-state yang elegan.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // 1. Ambil koleksi histori pesanan dari session konsumen
        $pesanans = $this->pesananSesi();

        return view('konsumen.pesanan', compact('pesanans'));
    }

    /**
     * Tampilkan halaman pelacakan status timeline progres dapur untuk pesanan tertentu.
     *
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi
     * @return \Illuminate\View\View
     */
    public function lacak(string $noPesanan): View
    {
        // 1. Ambil data pesanan beserta relasi item hidangan dan meja
        $pesanan = Pesanan::with(['detailPesanan.menu', 'meja'])->find($noPesanan);

        if (! $pesanan) {
            abort(404);
        }

        return view('konsumen.lacak', compact('pesanan'));
    }

    /**
     * Tampilkan halaman pelacakan untuk pesanan terbaru yang aktif.
     * Prioritaskan melacak pesanan berjalan (belum selesai/batal) dari session.
     * Fallback ke pesanan terlama jika tidak ada pesanan aktif berjalan.
     *
     * @return \Illuminate\View\View
     */
    public function lacakLatest(): View
    {
        // 1. Tarik list pesanan sesi
        $pesanans = $this->pesananSesi();

        // 2. Prioritaskan mencari pesanan yang belum berstatus 'selesai' atau 'dibatalkan'
        $pesanan = $pesanans->first(function ($p) {
            return ! in_array($p->status_pesanan, ['selesai', 'dibatalkan'], true);
        }) ?? $pesanans->first(); // Fallback ke transaksi terakhir jika tidak ada yang berjalan

        return view('konsumen.lacak', compact('pesanan'));
    }

    /**
     * Memperoleh seluruh data pesanan milik konsumen dari session browser.
     * Menjamin urutan data kronologis terbalik (transaksi terbaru diposisikan paling atas).
     *
     * @return \Illuminate\Support\Collection  Koleksi data model Pesanan
     */
    private function pesananSesi(): Collection
    {
        // 1. Dapatkan daftar ID pesanan unik dari session riwayat pesanan pembeli
        $ids = array_values(array_unique(session('riwayat_pesanan', [])));

        // 2. Dukungan backward-compatibility (fallback) jika sesi lama menggunakan model single string
        if (empty($ids) && session('no_pesanan_baru')) {
            $ids = [session('no_pesanan_baru')];
        }

        if (empty($ids)) {
            return collect();
        }

        // 3. Tarik seluruh baris pesanan yang tercocokkan dengan ID sesi dari database
        $map = Pesanan::with(['detailPesanan.menu', 'meja'])
            ->whereIn('no_pesanan', $ids)
            ->get()
            ->keyBy('no_pesanan');

        // 4. Susun ulang koleksi agar tetap mempertahankan urutan kronologis terbaru di atas (reverse order)
        return collect(array_reverse($ids))
            ->map(function ($id) use ($map) {
                return $map->get($id);
            })
            ->filter() // Bersihkan jika ada nilai kosong/pesanan terhapus
            ->values();
    }

    /**
     * Polling Endpoint JSON: Mendapatkan status pembaruan transaksi (pembayaran & progres dapur).
     * Melakukan sinkronisasi pull-based status ke Midtrans jika transaksi dinilai belum lunas.
     *
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(string $noPesanan): JsonResponse
    {
        $pesanan = Pesanan::find($noPesanan);

        if (! $pesanan) {
            return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
        }

        // 1. Sinkronisasi preventif: Tarik paksa status Midtrans secara realtime jika webhook delay/miss
        if ($pesanan->status_pembayaran !== 'lunas'
            && config('services.bayar.driver') === 'midtrans'
            && $pesanan->midtrans_transaction_id
            && class_exists(\Midtrans\Transaction::class)
        ) {
            try {
                // Set konfigurasi server key Midtrans
                \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
                \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production');

                $remote = \Midtrans\Transaction::status($pesanan->no_pesanan);
                $remoteStatus = is_object($remote) ? ($remote->transaction_status ?? null) : ($remote['transaction_status'] ?? null);

                // Ubah lunas jika remote lunas
                if (in_array($remoteStatus, ['capture', 'settlement'], true)) {
                    $pesanan->update([
                        'status_pembayaran' => 'lunas',
                        'tgl_pembayaran'    => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Midtrans status sync failed (pesanan view)', [
                    'pesanan' => $pesanan->no_pesanan,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        // 2. Kembalikan detail status terupdate ke JavaScript client
        return response()->json([
            'no_pesanan'        => $pesanan->no_pesanan,
            'status_pesanan'    => $pesanan->status_pesanan,
            'status_pembayaran' => $pesanan->status_pembayaran,
            'tgl_pembayaran'    => optional($pesanan->tgl_pembayaran)->toIso8601String(),
        ]);
    }

    /**
     * Membuat kuitansi nota pembayaran PDF mandiri untuk diunduh konsumen.
     * Hanya diijinkan setelah status pembayaran dinyatakan LUNAS.
     *
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi
     * @return \Illuminate\Http\Response  File unduhan PDF kuitansi (Format A5 portrait)
     */
    public function kuitansi(string $noPesanan): HttpResponse
    {
        $pesanan = Pesanan::with(['detailPesanan.menu', 'meja'])->find($noPesanan);

        if (! $pesanan) {
            abort(404);
        }

        // 1. Keamanan Akses: Tolak pembuatan kuitansi jika pesanan belum dibayar lunas
        if ($pesanan->status_pembayaran !== 'lunas') {
            abort(403, 'Kuitansi hanya tersedia setelah pembayaran lunas.');
        }

        // 2. Render view kuitansi dengan pengaturan kertas khusus A5
        $pdf = Pdf::loadView('konsumen.kuitansi', compact('pesanan'))
            ->setPaper('a5', 'portrait');

        return $pdf->download('kuitansi-' . $pesanan->no_pesanan . '.pdf');
    }

    /**
     * Membatalkan transaksi pemesanan secara mandiri oleh konsumen sebelum disetujui dapur.
     * Hanya diizinkan jika pesanan belum lunas DAN belum terkonfirmasi/sedang diproses dapur.
     *
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function batal(string $noPesanan): RedirectResponse
    {
        $pesanan = Pesanan::with('detailPesanan')->find($noPesanan);

        if (! $pesanan) {
            abort(404);
        }

        // 1. Kebijakan Keamanan: Tolak pembatalan sepihak jika pesanan sudah dibayar lunas atau sudah mulai dikerjakan dapur
        if ($pesanan->status_pembayaran === 'lunas' || $pesanan->status_pesanan !== 'menunggu konfirmasi') {
            return redirect()
                ->route('konsumen.lacak.detail', $pesanan->no_pesanan)
                ->withErrors(['batal' => 'Pesanan tidak dapat dibatalkan karena sudah dibayar atau sedang diproses.']);
        }

        // 2. Hapus detail dan data utama pesanan dalam transaksi database tunggal
        DB::transaction(function () use ($pesanan) {
            $pesanan->detailPesanan()->delete(); // Hapus item detail terlebih dahulu (foreign key restrict safety)
            $pesanan->delete(); // Hapus data pesanan utama
        });

        // 3. Bersihkan pencatatan nomor transaksi aktif di session jika cocok
        if (session('no_pesanan_baru') === $noPesanan) {
            session()->forget('no_pesanan_baru');
        }

        // 4. Arahkan kembali konsumen menuju keranjang belanja awal
        return redirect()->route('konsumen.keranjang')->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
