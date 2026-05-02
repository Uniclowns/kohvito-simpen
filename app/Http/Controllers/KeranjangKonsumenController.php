<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KeranjangKonsumenController extends Controller
{
    /**
     * Tampilkan isi keranjang belanja (session-based).
     */
    public function index()
    {
        return view('konsumen.keranjang');
    }

    /**
     * Tambah item menu ke keranjang (session).
     */
    public function storeTambahKeranjang(Request $request)
    {
        //
    }

    /**
     * Update catatan/notes kustomisasi per item di keranjang.
     */
    public function updateNotesPesanan(Request $request)
    {
        //
    }

    /**
     * Update jumlah porsi atau hapus item di keranjang.
     */
    public function updatePesanan(Request $request)
    {
        //
    }

    /**
     * Finalisasi pesanan: input nama konsumen, generate no_pesanan, simpan ke DB.
     */
    public function storePesan(Request $request)
    {
        //
    }
}
