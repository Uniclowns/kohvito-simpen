<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BerandaAdminController extends Controller
{
    /**
     * Tampilkan halaman dashboard admin dengan ringkasan omzet.
     */
    public function index()
    {
        return view('admin.beranda');
    }

    /**
     * Endpoint JSON: data grafik omzet untuk chart.
     */
    public function getData(Request $request)
    {
        //
    }

    /**
     * Generate dan download laporan kasir (PDF/Excel).
     */
    public function cetakLaporanKasir(Request $request)
    {
        //
    }
}
