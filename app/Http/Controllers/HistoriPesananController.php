<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HistoriPesananController extends Controller
{
    /**
     * Tampilkan daftar pesanan selesai/dibatalkan hari ini.
     */
    public function index()
    {
        return view('kasir.histori-pesanan');
    }

    /**
     * Tampilkan detail histori pesanan.
     */
    public function detail(string $noPesanan)
    {
        //
    }

    /**
     * Cetak satu struk histori pesanan.
     */
    public function cetakHistoriPesanan(string $noPesanan)
    {
        //
    }

    /**
     * Cetak rekap seluruh histori pesanan harian.
     */
    public function cetakSemuaHistoriPesanan(Request $request)
    {
        //
    }
}
