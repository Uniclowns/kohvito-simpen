<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KelolaKategoriMenuController extends Controller
{
    /**
     * Tampilkan daftar semua kategori menu.
     */
    public function index()
    {
        return view('admin.kelola-kategori-menu');
    }

    /**
     * Validasi dan simpan kategori menu baru.
     */
    public function storeKategoriMenu(Request $request)
    {
        //
    }

    /**
     * Hapus kategori menu (cek relasi menu aktif terlebih dahulu).
     */
    public function destroyKategoriMenu(string $id)
    {
        //
    }
}
