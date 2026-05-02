<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Carbon\Carbon;

class BerandaKasirController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $menunggu = Pesanan::where('status_pesanan', 'menunggu konfirmasi')
            ->whereDate('tgl_pembayaran', $today)
            ->count();

        $diproses = Pesanan::where('status_pesanan', 'diproses')
            ->whereDate('tgl_pembayaran', $today)
            ->count();

        $selesai = Pesanan::where('status_pesanan', 'selesai')
            ->whereDate('tgl_pembayaran', $today)
            ->count();

        return view('kasir.beranda', compact('menunggu', 'diproses', 'selesai'));
    }
}
