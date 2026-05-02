<?php

namespace App\Http\Controllers;

class PesananController extends Controller
{
    /**
     * Tampilkan halaman tracking pesanan dan struk digital konsumen.
     */
    public function index(string $noPesanan)
    {
        return view('konsumen.pesanan');
    }
}
