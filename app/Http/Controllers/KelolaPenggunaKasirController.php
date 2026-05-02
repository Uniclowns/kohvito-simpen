<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KelolaPenggunaKasirController extends Controller
{
    /**
     * Tampilkan daftar semua akun kasir.
     */
    public function index()
    {
        return view('admin.kelola-pengguna-kasir');
    }

    /**
     * Validasi dan simpan akun kasir baru (hash password).
     */
    public function storePenggunaKasir(Request $request)
    {
        //
    }

    /**
     * Hapus atau nonaktifkan akun kasir.
     */
    public function destroyPenggunaKasir(string $id)
    {
        //
    }
}
