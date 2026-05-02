<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KelolaPesananController extends Controller
{
    /**
     * Tampilkan daftar pesanan masuk (status: menunggu konfirmasi).
     */
    public function index()
    {
        return view('kasir.kelola-pesanan');
    }

    /**
     * Tampilkan detail pesanan (items, notes, meja).
     */
    public function detail(string $noPesanan)
    {
        //
    }

    /**
     * Update status pesanan: menunggu konfirmasi → diproses → selesai.
     */
    public function updateStatus(Request $request, string $noPesanan)
    {
        //
    }

    /**
     * Cetak struk pesanan (untuk dapur).
     */
    public function cetakPesanan(string $noPesanan)
    {
        //
    }
}
