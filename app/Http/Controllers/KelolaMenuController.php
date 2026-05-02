<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KelolaMenuController extends Controller
{
    /**
     * Tampilkan daftar semua menu dengan paginasi.
     */
    public function index()
    {
        return view('admin.kelola-menu');
    }

    /**
     * Validasi dan simpan menu baru (termasuk upload gambar).
     */
    public function storeMenu(Request $request)
    {
        //
    }

    /**
     * Validasi dan update data menu yang sudah ada.
     */
    public function updateMenu(Request $request, string $id)
    {
        //
    }

    /**
     * Hapus menu berdasarkan ID.
     */
    public function destroyMenu(string $id)
    {
        //
    }
}
