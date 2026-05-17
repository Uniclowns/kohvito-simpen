<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Xendit\Invoice;
use Xendit\Xendit;

class BayarController extends Controller
{
    /**
     * Buat transaksi pembayaran ke Payment Gateway (Xendit).
     */
    public function bayar(Request $request)
    {
        $noPesanan = session('no_pesanan_baru') ?? $request->input('no_pesanan');

        $pesanan = Pesanan::find($noPesanan);

        if (! $pesanan) {
            abort(404);
        }

        if ($pesanan->status_pembayaran === 'lunas') {
            return redirect()->route('konsumen.pesanan', $pesanan->no_pesanan);
        }

        try {
            Xendit::setApiKey(config('services.xendit.api_key'));

            $invoice = Invoice::create([
                'external_id'          => $pesanan->no_pesanan,
                'amount'               => $pesanan->total_harga,
                'description'          => 'Pembayaran pesanan ' . $pesanan->no_pesanan . ' — ' . $pesanan->nama_konsumen,
                'invoice_duration'     => 86400,
                'success_redirect_url' => route('konsumen.pesanan', $pesanan->no_pesanan),
                'failure_redirect_url' => route('konsumen.pesanan', $pesanan->no_pesanan),
                'currency'             => 'IDR',
                'customer'             => ['given_names' => $pesanan->nama_konsumen],
            ]);

            return redirect($invoice['invoice_url']);
        } catch (\Throwable $e) {
            return back()->withErrors(['bayar' => 'Gagal membuat invoice pembayaran: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle callback/webhook dari Payment Gateway setelah pembayaran.
     */
    public function callback(Request $request)
    {
        $payload = $request->all();

        if (! isset($payload['status']) || $payload['status'] !== 'PAID') {
            return response()->json(['status' => 'ignored'], 200);
        }

        if (! isset($payload['external_id'])) {
            return response()->json(['status' => 'ignored'], 200);
        }

        $pesanan = Pesanan::find($payload['external_id']);

        if (! $pesanan) {
            return response()->json(['status' => 'ignored'], 200);
        }

        $pesanan->update([
            'status_pembayaran' => 'lunas',
            'status_pesanan'    => 'menunggu konfirmasi',
            'tgl_pembayaran'    => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }
}
