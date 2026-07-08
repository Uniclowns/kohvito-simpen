<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Traits\CartSessionScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Midtrans\Config;
use Midtrans\Transaction;

/**
 * Class PesananController
 *
 * Controller ini mengatur manajemen histori pemesanan, pelacakan proses pembuatan hidangan,
 * pembatalan pesanan yang belum terkonfirmasi, cetak kuitansi PDF mandiri,
 * serta polling sinkronisasi status transaksi di antarmuka konsumen.
 *
 * Memanfaatkan riwayat nomor transaksi berbasis session array (`riwayat_pesanan`) agar
 * pembeli tetap dapat melacak histori transaksinya meski browser ditutup/sesi belanja di-reset.
 */
class PesananController extends Controller
{
    use CartSessionScope;

    /**
     * Tampilkan seluruh daftar pesanan konsumen dalam sesi aktif saat ini.
     * Jika kosong, sistem menyajikan antarmuka empty-state yang elegan.
     */
    public function index(?string $noMeja = null): View
    {
        // 0. Pulihkan riwayat dari cookie backup (30 hari) bila session hilang/expired.
        $this->restoreRiwayatDariCookie();

        // 1. Ambil koleksi histori pesanan dari session konsumen
        $pesanans = $this->pesananSesi();

        return view('konsumen.pesanan', compact('pesanans', 'noMeja'));
    }

    /**
     * Halaman publik pelacakan pesanan via kode pelacakan (tracking_code).
     * Menampilkan form input kode. Tidak butuh session/cookie — bisa diakses
     * kapan saja oleh siapa pun yang memegang kode pelacakan pesanannya.
     *
     * Mendukung query param ?kode=KV-XXXXX (mis. dari QR di kuitansi):
     * kode valid langsung diproses tanpa submit manual.
     */
    public function lacakForm(Request $request): View|RedirectResponse|Response
    {
        $kode = $request->query('kode');

        if (is_string($kode) && trim($kode) !== '') {
            return $this->prosesKodePelacakan($kode);
        }

        return view('konsumen.lacak-form');
    }

    /**
     * Proses pencarian pesanan berdasarkan tracking_code yang diinput konsumen.
     * Bila ditemukan, sekaligus catat ulang kode ke riwayat session + cookie
     * agar pesanan tersebut kembali muncul di daftar riwayat konsumen.
     */
    public function cari(Request $request): RedirectResponse|Response
    {
        $validated = $request->validate([
            'tracking_code' => ['required', 'string', 'max:20'],
        ]);

        return $this->prosesKodePelacakan($validated['tracking_code']);
    }

    /**
     * Jalur bersama pencarian kode pelacakan (form POST maupun ?kode= dari QR):
     * normalisasi input → gerbang format → lookup DB → auto-merge riwayat →
     * render timeline. Kode salah selalu berujung redirect ramah ke form.
     */
    private function prosesKodePelacakan(string $input): RedirectResponse|Response
    {
        // Normalisasi input: buang spasi, huruf besar, toleran bila user lupa prefix "KV-".
        $kode = strtoupper(trim($input));
        if (! str_starts_with($kode, 'KV-') && ! str_contains($kode, '-')) {
            $kode = 'KV-'.$kode;
        }

        // Gerbang format sebelum menyentuh database: tracking_code adalah
        // satu-satunya kunci publik, jadi input di luar pola KV-XXXXX ditolak dini.
        if (! $this->isValidTrackingCode($kode)) {
            return redirect()->route('konsumen.lacak.form')
                ->withInput()
                ->with('error', 'Pesanan dengan kode tersebut tidak ditemukan. Periksa kembali kode pelacakan Anda.');
        }

        $pesanan = Pesanan::with(['detailPesanan.menu', 'meja'])
            ->where('tracking_code', $kode)
            ->first();

        if (! $pesanan) {
            return redirect()->route('konsumen.lacak.form')
                ->withInput()
                ->with('error', 'Pesanan dengan kode tersebut tidak ditemukan. Periksa kembali kode pelacakan Anda.');
        }

        // Pesanan ketemu → auto-merge ke riwayat browser ini, lalu tampilkan timeline.
        $this->catatRiwayatPesanan($pesanan);

        return response()->view('konsumen.lacak', ['pesanan' => $pesanan, 'noMeja' => null]);
    }

    /**
     * Tampilkan halaman pelacakan status timeline progres dapur untuk pesanan tertentu.
     *
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi
     */
    public function lacak(string $noMeja, string $noPesanan): View
    {
        // 1. Ambil data pesanan beserta relasi item hidangan dan meja
        $pesanan = Pesanan::with(['detailPesanan.menu', 'meja'])->find($noPesanan);

        if (! $pesanan) {
            abort(404);
        }

        // Auto-merge diam-diam: pesanan yang berhasil dilacak ikut tercatat
        // di riwayat browser ini (session + cookie backup).
        $this->catatRiwayatPesanan($pesanan);

        return view('konsumen.lacak', compact('pesanan', 'noMeja'));
    }

    /**
     * Tampilkan halaman pelacakan untuk pesanan terbaru yang aktif.
     * Prioritaskan melacak pesanan berjalan (belum selesai/batal) dari session.
     * Fallback ke pesanan terlama jika tidak ada pesanan aktif berjalan.
     */
    public function lacakLatest(?string $noMeja = null): View
    {
        // 0. Pulihkan riwayat dari cookie backup bila session hilang.
        $this->restoreRiwayatDariCookie();

        // 1. Tarik list pesanan sesi
        $pesanans = $this->pesananSesi();

        // 2. Prioritaskan mencari pesanan yang belum berstatus 'selesai' atau 'dibatalkan'
        $pesanan = $pesanans->first(function ($p) {
            return ! in_array($p->status_pesanan, ['selesai', 'dibatalkan'], true);
        }) ?? $pesanans->first(); // Fallback ke transaksi terakhir jika tidak ada yang berjalan

        return view('konsumen.lacak', compact('pesanan', 'noMeja'));
    }

    /**
     * Memperoleh seluruh data pesanan milik konsumen dari session browser.
     * Menjamin urutan data kronologis terbalik (transaksi terbaru diposisikan paling atas).
     *
     * Riwayat kini disimpan sebagai daftar tracking_code (device-independent).
     * Untuk backward-compatibility, nilai lama yang berupa no_pesanan tetap dicocokkan.
     *
     * @return Collection Koleksi data model Pesanan
     */
    private function pesananSesi(): Collection
    {
        // 1. Dapatkan daftar kode unik dari session riwayat pesanan pembeli
        $ids = array_values(array_unique(session('riwayat_pesanan', [])));

        // 2. Dukungan backward-compatibility (fallback) jika sesi lama menggunakan model single string
        if (empty($ids) && session('no_pesanan_baru')) {
            $ids = [session('no_pesanan_baru')];
        }

        if (empty($ids)) {
            return collect();
        }

        // 3. Tarik seluruh baris pesanan yang tercocokkan dengan kode sesi dari database.
        //    Cocokkan baik terhadap tracking_code (format baru) maupun no_pesanan (format lama).
        $rows = Pesanan::with(['detailPesanan.menu', 'meja'])
            ->whereIn('tracking_code', $ids)
            ->orWhereIn('no_pesanan', $ids)
            ->get();

        // 4. Bangun peta lookup berdasar kedua kunci agar urutan bisa dipertahankan.
        $map = collect();
        foreach ($rows as $row) {
            if ($row->tracking_code) {
                $map->put($row->tracking_code, $row);
            }
            $map->put($row->no_pesanan, $row);
        }

        // 5. Susun ulang koleksi agar tetap mempertahankan urutan kronologis terbaru di atas (reverse order)
        return collect(array_reverse($ids))
            ->map(fn ($id) => $map->get($id))
            ->filter()   // Bersihkan jika ada nilai kosong/pesanan terhapus
            ->unique('no_pesanan')
            ->values();
    }

    /**
     * Polling Endpoint JSON: Mendapatkan status pembaruan transaksi (pembayaran & progres dapur).
     * Melakukan sinkronisasi pull-based status ke Midtrans jika transaksi dinilai belum lunas.
     *
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi
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
            && class_exists(Transaction::class)
        ) {
            try {
                // Set konfigurasi server key Midtrans
                Config::$serverKey = config('services.midtrans.server_key');
                Config::$isProduction = (bool) config('services.midtrans.is_production');

                $remote = Transaction::status($pesanan->no_pesanan);
                $remoteStatus = is_object($remote) ? ($remote->transaction_status ?? null) : ($remote['transaction_status'] ?? null);

                // Ubah lunas jika remote lunas
                if (in_array($remoteStatus, ['capture', 'settlement'], true)) {
                    $pesanan->update([
                        'status_pembayaran' => 'lunas',
                        'tgl_pembayaran' => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Midtrans status sync failed (pesanan view)', [
                    'pesanan' => $pesanan->no_pesanan,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // 2. Kembalikan detail status terupdate ke JavaScript client
        return response()->json([
            'no_pesanan' => $pesanan->no_pesanan,
            'status_pesanan' => $pesanan->status_pesanan,
            'status_pembayaran' => $pesanan->status_pembayaran,
            'tgl_pembayaran' => optional($pesanan->tgl_pembayaran)->toIso8601String(),
        ]);
    }

    /**
     * Membuat kuitansi nota pembayaran PDF mandiri untuk diunduh konsumen.
     * Hanya diijinkan setelah status pembayaran dinyatakan LUNAS.
     *
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi
     * @return Response File unduhan PDF kuitansi (Format A5 portrait)
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

        // 1b. Auto-merge diam-diam: membuka kuitansi ikut mencatat pesanan
        //     ke riwayat browser ini (session + cookie backup).
        $this->catatRiwayatPesanan($pesanan);

        // 2. Render view kuitansi dengan pengaturan kertas khusus A5
        $pdf = Pdf::loadView('konsumen.kuitansi', compact('pesanan'))
            ->setPaper('a5', 'portrait');

        return $pdf->download('kuitansi-'.$pesanan->no_pesanan.'.pdf');
    }

    /**
     * Membatalkan transaksi pemesanan secara mandiri oleh konsumen sebelum disetujui dapur.
     * Hanya diizinkan jika pesanan belum lunas DAN belum terkonfirmasi/sedang diproses dapur.
     *
     * @param  string  $noPesanan  Nomor transaksi pesanan referensi
     */
    public function batal(string $noPesanan): RedirectResponse
    {
        $pesanan = Pesanan::with('detailPesanan')->find($noPesanan);

        if (! $pesanan) {
            abort(404);
        }

        // 1. Kebijakan Keamanan: Tolak pembatalan sepihak jika pesanan sudah dibayar lunas atau sudah mulai dikerjakan dapur
        if ($pesanan->status_pembayaran === 'lunas' || $pesanan->status_pesanan !== 'menunggu konfirmasi') {
            return redirect($pesanan->lacakUrl())
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
