<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Xendit\Invoice;
use Xendit\Xendit;

/**
 * Class BayarController
 * 
 * Controller ini mengatur seluruh siklus hidup proses pembayaran di aplikasi Kohvito.
 * Menyediakan dukungan bagi berbagai metode pembayaran (*multichannel drivers*):
 * 1. **Midtrans**: Menggunakan Core API untuk membangkitkan QRIS dinamis di halaman web.
 * 2. **Xendit**: Menyajikan invoice pengalihan (redirect invoice page) eksternal.
 * 3. **Mock/Simulator**: Halaman lokal simulasi pembayaran untuk keperluan development luring.
 * 
 * Controller ini juga mengatur polling status pesanan, pengunduhan file QR,
 * serta verifikasi webhook callback yang aman dari payment gateway.
 *
 * @package App\Http\Controllers
 */
class BayarController extends Controller
{
    /**
     * Tampilkan halaman QRIS pembayaran.
     * Jika driver diatur ke 'midtrans' dan pesanan belum lunas serta belum memiliki URL QR,
     * controller ini otomatis melakukan charge transaksi ke Midtrans untuk mendapatkan URL QR Code.
     *
     * @param  string  $noPesanan  Nomor transaksi referensi
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function qris(string $noPesanan)
    {
        // 1. Mencari pesanan beserta relasi detil pesanan dan meja terkait berdasarkan nomor unik
        $pesanan = Pesanan::with(['detailPesanan', 'meja'])->find($noPesanan);

        if (! $pesanan) {
            abort(404);
        }

        // 2. Simpan nomor pesanan aktif saat ini ke session pembeli
        session(['no_pesanan_baru' => $pesanan->no_pesanan]);

        // 3. Membaca driver pembayaran aktif dari konfigurasi layanan (.env)
        $driver = config('services.bayar.driver', 'mock');

        // 4. Jika menggunakan Midtrans, transaksi belum lunas, dan qr_url belum dibangkitkan
        if ($driver === 'midtrans'
            && $pesanan->status_pembayaran !== 'lunas'
            && empty($pesanan->qr_url)
        ) {
            // 5. Bangkitkan QRIS baru dari Midtrans Core API secara background
            $this->generateMidtransQris($pesanan);
            // 6. Refresh data model untuk memuat data qr_url terupdate yang baru disimpan
            $pesanan->refresh();
        }

        // 7. Mengembalikan tampilan pembayaran konsumen
        return view('konsumen.pembayaran', compact('pesanan'));
    }

    /**
     * Endpoint POST /bayar
     * Menjembatani proses awal checkout konsumen untuk dialihkan menuju halaman bayar yang sesuai.
     *
     * @param  \Illuminate\Http\Request  $request  Data request checkout
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bayar(Request $request): RedirectResponse
    {
        // 1. Mendapatkan nomor pesanan dari session checkout, fallback ke parameter input
        $noPesanan = session('no_pesanan_baru') ?? $request->input('no_pesanan');

        $pesanan = Pesanan::find($noPesanan);

        if (! $pesanan) {
            abort(404);
        }

        // 2. Apabila status pesanan sudah lunas, langsung arahkan ke pelacakan status
        if ($pesanan->status_pembayaran === 'lunas') {
            return redirect()->route('konsumen.lacak.detail', $pesanan->no_pesanan);
        }

        // 3. Mengambil jenis driver pembayaran aktif saat ini
        $driver = config('services.bayar.driver', 'mock');

        // 4. Pengalihan rute pembayaran berdasarkan jenis driver terpilih
        return match ($driver) {
            'xendit'   => $this->bayarXendit($pesanan),
            'midtrans' => redirect()->route('konsumen.pembayaran', $pesanan->no_pesanan),
            default    => redirect()->route('konsumen.bayar.simulator', $pesanan->no_pesanan),
        };
    }

    /**
     * Polling Endpoint (Pull-based status mirror)
     * Dipanggil berkala oleh JavaScript di halaman frontend untuk menyelaraskan status bayar lokal DB.
     * Sebagai penanganan fallback jika transmisi callback dari Midtrans terhambat/hilang.
     *
     * @param  string  $noPesanan  Nomor transaksi referensi
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncStatus(string $noPesanan): JsonResponse
    {
        $pesanan = Pesanan::find($noPesanan);

        if (! $pesanan) {
            return response()->json(['error' => 'not_found'], 404);
        }

        // 1. Sinkronisasi paksa jika transaksi belum lunas, driver Midtrans aktif, dan kelas SDK tersedia
        if ($pesanan->status_pembayaran !== 'lunas'
            && config('services.bayar.driver') === 'midtrans'
            && class_exists(\Midtrans\Transaction::class)
        ) {
            try {
                // 2. Set parameter konfigurasi SDK Midtrans
                \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
                \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production');

                // 3. Tarik status pembayaran terbaru langsung dari remote server Midtrans
                $remote = \Midtrans\Transaction::status($pesanan->no_pesanan);
                $remoteStatus = is_object($remote) ? ($remote->transaction_status ?? null) : ($remote['transaction_status'] ?? null);

                // 4. Jika status bernilai 'capture' atau 'settlement' (berhasil lunas)
                if (in_array($remoteStatus, ['capture', 'settlement'], true)) {
                    // 5. Perbarui database lokal ke status lunas secara atomik
                    $pesanan->update([
                        'status_pembayaran' => 'lunas',
                        'status_pesanan'    => 'menunggu konfirmasi',
                        'tgl_pembayaran'    => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                // 6. Catat log kegagalan penarikan status agar sistem tidak terhenti
                Log::warning('Midtrans status sync failed', [
                    'pesanan' => $pesanan->no_pesanan,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        // 7. Mengembalikan respon data status pembayaran terbaru ke client
        return response()->json([
            'no_pesanan'        => $pesanan->no_pesanan,
            'status_pembayaran' => $pesanan->status_pembayaran,
            'tgl_pembayaran'    => optional($pesanan->tgl_pembayaran)->toIso8601String(),
            'redirect'          => $pesanan->status_pembayaran === 'lunas'
                ? route('konsumen.lacak.detail', $pesanan->no_pesanan)
                : null,
        ]);
    }

    /**
     * Mengalirkan (stream) gambar QR Code dari URL gateway eksternal ke browser konsumen.
     * Menggunakan Content-Disposition: attachment agar memicu popup "Simpan File" di browser,
     * menggantikan keterbatasan atribut HTML `download` akibat pembatasan CORS Cross-Origin.
     *
     * @param  string  $noPesanan  Nomor transaksi referensi
     * @return \Illuminate\Http\Response
     */
    public function downloadQr(string $noPesanan)
    {
        $pesanan = Pesanan::find($noPesanan);

        if (! $pesanan || empty($pesanan->qr_url)) {
            abort(404);
        }

        try {
            // 1. Melakukan HTTP GET Request ke URL penyimpanan gambar QR gateway dengan batas timeout 15 detik
            $response = Http::timeout(15)->get($pesanan->qr_url);
        } catch (\Throwable $e) {
            Log::error('QR download failed', ['error' => $e->getMessage(), 'pesanan' => $pesanan->no_pesanan]);
            abort(502, 'Gagal mengunduh QR Code dari payment gateway.');
        }

        if (! $response->ok()) {
            abort(502, 'Payment gateway menolak permintaan QR Code.');
        }

        // 2. Menganalisis content-type file gambar hasil download
        $contentType = $response->header('Content-Type') ?: 'image/png';
        $extension   = str_contains($contentType, 'jpeg') ? 'jpg' : 'png';
        $filename    = 'qris-kohvito-' . $pesanan->no_pesanan . '.' . $extension;

        // 3. Kirim streaming byte gambar dengan header penanganan download browser
        return response($response->body(), 200, [
            'Content-Type'        => $contentType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * MOCK Driver — Membuka halaman simulator pembayaran lokal.
     * Digunakan untuk pengujian sistem checkout tanpa memerlukan koneksi internet aktif ke payment gateway asli.
     *
     * @param  string  $noPesanan  Nomor transaksi referensi
     * @return \Illuminate\View\View
     */
    public function simulator(string $noPesanan): View
    {
        $pesanan = Pesanan::find($noPesanan);

        if (! $pesanan) {
            abort(404);
        }

        return view('konsumen.bayar-simulator', compact('pesanan'));
    }

    /**
     * MOCK Driver — Simulasi pengiriman data callback lunas/gagal dari simulator lokal.
     * Memperbarui status pesanan lokal sesuai dengan aksi tombol yang ditekan penguji.
     *
     * @param  \Illuminate\Http\Request  $request  Data form hasil simulasi
     * @param  string  $noPesanan  Nomor transaksi referensi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function simulatorCallback(Request $request, string $noPesanan): RedirectResponse
    {
        // 1. Cegah akses simulator bila driver aktif bukan 'mock' demi alasan keamanan produksi
        if (config('services.bayar.driver') !== 'mock') {
            abort(404);
        }

        $pesanan = Pesanan::find($noPesanan);

        if (! $pesanan) {
            abort(404);
        }

        $hasil = $request->input('hasil', 'lunas');

        // 2. Jika disimulasikan lunas
        if ($hasil === 'lunas') {
            $pesanan->update([
                'status_pembayaran' => 'lunas',
                'status_pesanan'    => 'menunggu konfirmasi',
                'tgl_pembayaran'    => now(),
            ]);

            // Kembali ke halaman tunggu bayar agar script polling di frontend menangkap perubahan lunas
            return redirect()->route('konsumen.pembayaran', $pesanan->no_pesanan);
        }

        // 3. Jika disimulasikan gagal
        return redirect()->route('konsumen.pembayaran', [$pesanan->no_pesanan, 'result' => 'gagal']);
    }

    /**
     * Membuat halaman Invoice Xendit dinamis dan mengalihkan pengguna ke tautan pembayaran.
     *
     * @param  \App\Models\Pesanan  $pesanan  Model pesanan aktif
     * @return \Illuminate\Http\RedirectResponse
     */
    private function bayarXendit(Pesanan $pesanan): RedirectResponse
    {
        try {
            // 1. Mengatur API Key Xendit dari konfigurasi kredensial
            Xendit::setApiKey(config('services.xendit.api_key'));

            // 2. Buat invoice baru di platform Xendit
            $invoice = Invoice::create([
                'external_id'          => $pesanan->no_pesanan,
                'amount'               => $pesanan->total_harga,
                'description'          => 'Pembayaran pesanan ' . $pesanan->no_pesanan . ' - ' . $pesanan->nama_konsumen,
                'invoice_duration'     => 86400, // Aktif selama 24 jam
                'success_redirect_url' => route('konsumen.lacak.detail', $pesanan->no_pesanan),
                'failure_redirect_url' => route('konsumen.lacak.detail', $pesanan->no_pesanan),
                'currency'             => 'IDR',
                'customer'             => ['given_names' => $pesanan->nama_konsumen],
            ]);

            // 3. Alihkan konsumen secara langsung menuju halaman pembayaran eksternal Xendit
            return redirect($invoice['invoice_url']);
        } catch (\Throwable $e) {
            Log::error('Xendit invoice creation failed', ['error' => $e->getMessage(), 'pesanan' => $pesanan->no_pesanan]);
            return back()->withErrors(['bayar' => 'Gagal membuat invoice pembayaran: ' . $e->getMessage()]);
        }
    }

    /**
     * Menghubungi Midtrans Core API secara background untuk membangkitkan QRIS dinamis (payment_type=qris).
     * Menyimpan URL QR dan ID Transaksi dari Midtrans langsung ke baris tabel pesanan.
     * Bersifat idempotent (tidak akan membangkitkan ulang jika sudah pernah dibuat sebelumnya).
     *
     * @param  \App\Models\Pesanan  $pesanan  Model pesanan aktif
     * @return void
     */
    private function generateMidtransQris(Pesanan $pesanan): void
    {
        // 1. Pastikan class library Midtrans terpasang di sistem
        if (! class_exists(\Midtrans\CoreApi::class)) {
            Log::warning('Midtrans SDK belum terinstall — jalankan: composer require midtrans/midtrans-php');
            return;
        }

        try {
            // 2. Set parameter konfigurasi SDK Midtrans
            \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production');
            \Midtrans\Config::$isSanitized  = true;
            \Midtrans\Config::$is3ds        = true;

            // 3. Siapkan parameter charge untuk skema pembayaran QRIS GoPay
            $params = [
                'payment_type' => 'qris',
                'transaction_details' => [
                    'order_id'     => $pesanan->no_pesanan,
                    'gross_amount' => (int) $pesanan->total_harga,
                ],
                'qris' => [
                    'acquirer' => 'gopay',
                ],
                'customer_details' => [
                    'first_name' => $pesanan->nama_konsumen,
                ],
            ];

            // 4. Kirim request charge ke API server Midtrans
            $response = \Midtrans\CoreApi::charge($params);

            // 5. Ekstrak URL gambar QR Code dari array actions respon
            $qrUrl = $this->extractQrUrl($response);

            if (! $qrUrl) {
                Log::warning('Midtrans QRIS response tidak mengandung generate-qr-code URL', [
                    'pesanan'  => $pesanan->no_pesanan,
                    'response' => $response,
                ]);
                return;
            }

            // 6. Simpan URL QR dan ID transaksi dari Midtrans ke database lokal
            $pesanan->update([
                'midtrans_transaction_id' => $response->transaction_id ?? null,
                'qr_url'                  => $qrUrl,
            ]);
        } catch (\Throwable $e) {
            Log::error('Midtrans QRIS charge failed', [
                'error'   => $e->getMessage(),
                'pesanan' => $pesanan->no_pesanan,
            ]);
        }
    }

    /**
     * Ekstrak URL QR Code dari struktur respon Midtrans Core API secara aman.
     *
     * @param  mixed  $response  Struktur objek/array respon Midtrans
     * @return string|null  Mengembalikan tautan URL QR Code jika sukses, atau null jika gagal
     */
    private function extractQrUrl(mixed $response): ?string
    {
        $actions = is_object($response) ? ($response->actions ?? []) : ($response['actions'] ?? []);

        // Menelusuri daftar actions untuk menemukan nama aksi 'generate-qr-code'
        foreach ($actions as $action) {
            $name = is_object($action) ? ($action->name ?? '') : ($action['name'] ?? '');
            if ($name === 'generate-qr-code') {
                return is_object($action) ? ($action->url ?? null) : ($action['url'] ?? null);
            }
        }

        return null;
    }

    /**
     * Webhook Callback Gateway Handler
     * Endpoint tunggal terpadu untuk mendeteksi secara otomatis dan memproses callback
     * konfirmasi pembayaran baik dari Xendit maupun Midtrans secara background.
     *
     * @param  \Illuminate\Http\Request  $request  Payload callback dari gateway
     * @return \Illuminate\Http\JsonResponse
     */
    public function callback(Request $request): JsonResponse
    {
        $payload = $request->all();

        // 1. Deteksi payload Xendit — umumnya membawa parameter 'external_id' dan 'status'
        if (isset($payload['external_id'], $payload['status'])) {
            return $this->callbackXendit($request, $payload);
        }

        // 2. Deteksi payload Midtrans — umumnya membawa parameter 'order_id' dan 'transaction_status'
        if (isset($payload['order_id'], $payload['transaction_status'])) {
            return $this->callbackMidtrans($request, $payload);
        }

        // Abaikan request jika tipe payload tidak dikenali
        return response()->json(['status' => 'ignored'], 200);
    }

    /**
     * Memproses callback khusus dari platform Xendit.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request untuk validasi header
     * @param  array  $payload  Payload array kiriman webhook
     * @return \Illuminate\Http\JsonResponse
     */
    private function callbackXendit(Request $request, array $payload): JsonResponse
    {
        $expectedToken = config('services.xendit.callback_token');
        $receivedToken = $request->header('X-Callback-Token');

        // 1. Pengamanan keamanan: validasi kesesuaian callback token untuk menangkal manipulasi request palsu
        if ($expectedToken && $expectedToken !== $receivedToken) {
            Log::warning('Xendit callback rejected - token mismatch', ['external_id' => $payload['external_id'] ?? null]);
            return response()->json(['status' => 'rejected'], 401);
        }

        // 2. Abaikan callback jika status transaksi bukan 'PAID' (misal: EXPIRED / PENDING)
        if ($payload['status'] !== 'PAID') {
            return response()->json(['status' => 'ignored'], 200);
        }

        $pesanan = Pesanan::find($payload['external_id']);

        if (! $pesanan) {
            return response()->json(['status' => 'ignored'], 200);
        }

        // 3. Ubah status transaksi lokal menjadi Lunas secara instan
        $pesanan->update([
            'status_pembayaran' => 'lunas',
            'status_pesanan'    => 'menunggu konfirmasi',
            'tgl_pembayaran'    => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Memproses callback khusus dari platform Midtrans.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request
     * @param  array  $payload  Payload array kiriman webhook
     * @return \Illuminate\Http\JsonResponse
     */
    private function callbackMidtrans(Request $request, array $payload): JsonResponse
    {
        $serverKey = config('services.midtrans.server_key');
        // 1. Pengamanan keamanan: verifikasi signature key dari Midtrans menggunakan sha512
        //    Menjamin payload benar-benar berasal dari server Midtrans yang sah.
        $signature = hash('sha512',
            $payload['order_id']
            . ($payload['status_code'] ?? '')
            . ($payload['gross_amount'] ?? '')
            . $serverKey
        );

        if ($serverKey && isset($payload['signature_key']) && $signature !== $payload['signature_key']) {
            Log::warning('Midtrans callback rejected - signature mismatch', ['order_id' => $payload['order_id']]);
            return response()->json(['status' => 'rejected'], 401);
        }

        $status = $payload['transaction_status'] ?? '';

        // 2. Pastikan status transaksi Midtrans bernilai lunas ('capture' / 'settlement')
        if (! in_array($status, ['capture', 'settlement'], true)) {
            return response()->json(['status' => 'ignored'], 200);
        }

        $pesanan = Pesanan::find($payload['order_id']);

        if (! $pesanan) {
            return response()->json(['status' => 'ignored'], 200);
        }

        // 3. Tandai pesanan lunas di sistem internal
        $pesanan->update([
            'status_pembayaran' => 'lunas',
            'status_pesanan'    => 'menunggu konfirmasi',
            'tgl_pembayaran'    => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }
}
