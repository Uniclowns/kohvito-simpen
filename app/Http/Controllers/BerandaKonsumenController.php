<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BerandaKonsumenController extends Controller
{
    /**
     * Tampilkan halaman katalog menu konsumen (landing page setelah scan QR).
     */
    public function index(string $noMeja)
    {
        return view('konsumen.beranda');
    }

    /**
     * Endpoint JSON: data menu untuk katalog (filter, pagination).
     */
    public function getData(Request $request)
    {
        //
    }

    /**
     * Tampilkan detail satu item menu.
     */
    public function detail(string $id)
    {
        //
    }
}
