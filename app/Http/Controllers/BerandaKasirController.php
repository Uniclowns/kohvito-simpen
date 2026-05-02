<?php

namespace App\Http\Controllers;

class BerandaKasirController extends Controller
{
    /**
     * Tampilkan halaman dashboard kasir dengan ringkasan pesanan hari ini.
     */
    public function index()
    {
        return view('kasir.beranda');
    }
}
