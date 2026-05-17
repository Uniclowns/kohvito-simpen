<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();

            $role = strtolower(Auth::user()->role->nama_role);

            if ($role === 'admin') {
                return redirect()->route('admin.beranda');
            }

            if ($role === 'kasir') {
                return redirect()->route('kasir.beranda');
            }
        }

        return back()->withErrors(['loginError' => 'Username atau password salah.']);
    }

    /**
     * Logout user dan invalidate session.
     * Dipanggil oleh route POST /logout.
     */
    public function authenticated(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
