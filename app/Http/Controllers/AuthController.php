<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman form login.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Proses login: validasi credential dan autentikasi user.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Logic redirect setelah login berdasarkan role.
     * Admin → /admin, Kasir → /kasir.
     */
    public function authenticated(Request $request)
    {
        //
    }
}
