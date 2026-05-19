<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KelolaPesananController extends Controller
{
    public function index()
    {
        $pesanans = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->whereIn('status_pesanan', ['menunggu konfirmasi', 'diproses'])
            ->orderBy('tgl_pembayaran', 'asc')
            ->get();

        return view('kasir.kelola-pesanan', compact('pesanans'));
    }

    public function detail(string $noPesanan)
    {
        $pesanan = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('no_pesanan', $noPesanan)
            ->whereIn('status_pesanan', ['menunggu konfirmasi', 'diproses'])
            ->firstOrFail();

        return view('kasir.kelola-pesanan-detail', compact('pesanan'));
    }

    public function updateStatus(Request $request, string $noPesanan)
    {
        $pesanan = Pesanan::where('no_pesanan', $noPesanan)->firstOrFail();

        $transitions = [
            'menunggu konfirmasi' => 'diproses',
            'diproses' => 'selesai',
        ];

        $nextStatus = $transitions[$pesanan->status_pesanan] ?? null;

        if (! $nextStatus) {
            return back()->with('error', 'Status pesanan tidak dapat diubah.');
        }

        $pesanan->status_pesanan = $nextStatus;
        $pesanan->save();

        $isAccepted = $nextStatus === 'diproses';
        $message = $isAccepted
            ? 'Pesanan diterima dan sedang diproses.'
            : 'Pesanan telah selesai.';

        return redirect()
            ->route('kasir.pesanan.index')
            ->with('success', $message)
            ->with('order_action', $isAccepted ? 'accepted' : 'completed');
    }

    public function cetakPesanan(string $noPesanan)
    {
        $pesanan = Pesanan::with(['meja', 'detailPesanan.menu'])
            ->where('no_pesanan', $noPesanan)
            ->firstOrFail();

        return Pdf::loadView('kasir.cetak-pesanan-pdf', compact('pesanan'))
            ->stream("struk-{$noPesanan}.pdf");
    }
}
