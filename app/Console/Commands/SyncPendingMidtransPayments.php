<?php

namespace App\Console\Commands;

use App\Models\Pesanan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Failsafe untuk webhook Midtrans yang missed. Dijadwalkan tiap menit:
 * tarik status remote dari Midtrans untuk semua pesanan yang masih
 * `menunggu` & punya midtrans_transaction_id, lalu mirror ke DB lokal.
 *
 * Dengan command ini, status `lunas` di Midtrans selalu sampai ke aplikasi
 * dalam waktu max 1 menit walau webhook hilang & tidak ada user yang
 * membuka halaman pembayaran/pesanan.
 */
class SyncPendingMidtransPayments extends Command
{
    protected $signature = 'payments:sync-pending {--max=50 : Batas pesanan yang diproses per run}';

    protected $description = 'Sync status pembayaran pesanan Midtrans yang masih menunggu ke status remote terbaru.';

    public function handle(): int
    {
        if (config('services.bayar.driver') !== 'midtrans') {
            $this->info('Driver bukan midtrans — skip.');
            return self::SUCCESS;
        }

        if (! class_exists(\Midtrans\Transaction::class)) {
            $this->error('Midtrans SDK belum terinstall. Jalankan: composer require midtrans/midtrans-php');
            return self::FAILURE;
        }

        \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production');

        $pending = Pesanan::where('status_pembayaran', 'menunggu')
            ->whereNotNull('midtrans_transaction_id')
            ->orderByDesc('no_pesanan')
            ->limit((int) $this->option('max'))
            ->get();

        if ($pending->isEmpty()) {
            $this->info('Tidak ada pesanan menunggu untuk di-sync.');
            return self::SUCCESS;
        }

        $synced = 0;
        $errors = 0;

        foreach ($pending as $pesanan) {
            try {
                $remote = \Midtrans\Transaction::status($pesanan->no_pesanan);
                $remoteStatus = is_object($remote)
                    ? ($remote->transaction_status ?? null)
                    : ($remote['transaction_status'] ?? null);

                if (in_array($remoteStatus, ['capture', 'settlement'], true)) {
                    $pesanan->update([
                        'status_pembayaran' => 'lunas',
                        'tgl_pembayaran'    => now(),
                    ]);
                    $synced++;
                    $this->line("  [lunas]    {$pesanan->no_pesanan}");
                } elseif (in_array($remoteStatus, ['expire', 'cancel', 'deny', 'failure'], true)) {
                    $pesanan->update(['status_pembayaran' => 'gagal']);
                    $this->line("  [gagal]    {$pesanan->no_pesanan} ({$remoteStatus})");
                } else {
                    $this->line("  [pending]  {$pesanan->no_pesanan} ({$remoteStatus})");
                }
            } catch (\Throwable $e) {
                $errors++;
                Log::warning('payments:sync-pending failed for pesanan', [
                    'pesanan' => $pesanan->no_pesanan,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        $this->info("Selesai: total={$pending->count()} synced={$synced} errors={$errors}");

        return self::SUCCESS;
    }
}
