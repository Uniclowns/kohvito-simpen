<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class AuthController
 * 
 * Controller ini menangani seluruh alur autentikasi berbasis sesi (web) untuk staf kafe.
 * Meliputi penyajian formulir login, pemrosesan validasi kredensial login,
 * proteksi pengalihan berdasarkan peran (Admin/Kasir), proteksi Session Fixation,
 * serta pembersihan sesi saat pengguna melakukan log out.
 *
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * Tampilkan halaman formulir login.
     *
     * @return \Illuminate\View\View  Halaman login (auth.login)
     */
    public function create(): View
    {
        // 1. Mengembalikan view form login untuk membiarkan pengguna menginput username dan password.
        return view('auth.login');
    }

    /**
     * Memproses masuk sistem (Login): memvalidasi kredensial dan mengotentikasi pengguna.
     * Mencegah serangan Session Fixation dengan meregenerasi ID sesi setelah login berhasil.
     *
     * @param  \Illuminate\Http\Request  $request  Objek data kiriman form login
     * @return \Illuminate\Http\RedirectResponse  Redirect ke halaman dashboard sesuai peran (role)
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi input username dan password wajib diisi serta bertipe string.
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Mencoba melakukan autentikasi (Auth::attempt) dengan mencocokkan kredensial.
        //    Laravel secara otomatis membandingkan password yang dimasukkan dengan hash bcrypt di database.
        $kredensial = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($kredensial)) {
            // 3. Login sukses. Lakukan regenerasi ID Session untuk menangkal serangan Session Fixation.
            //    Ini menghancurkan ID sesi lama dan membuat sesi baru dengan data yang tetap dipertahankan.
            $request->session()->regenerate();

            // 4. Mengambil nama role pengguna secara case-insensitive untuk menentukan halaman tujuan.
            //    Normalisasi sama dengan CheckRole middleware (lowercase + strip space)
            //    agar "Super Admin" → "superadmin".
            $role = str_replace(' ', '', strtolower(Auth::user()->role->nama_role));

            // 5. Super Admin: arahkan ke beranda admin sebagai launchpad. Dari situ
            //    bisa navigasi bebas ke panel kasir, halaman konsumen meja, dan kelola meja.
            if ($role === 'superadmin') {
                return redirect()->route('admin.beranda');
            }

            // 6. Jika role adalah Admin, arahkan ke beranda panel admin.
            if ($role === 'admin') {
                return redirect()->route('admin.beranda');
            }

            // 7. Jika role adalah Kasir, arahkan ke beranda panel kasir.
            if ($role === 'kasir') {
                return redirect()->route('kasir.beranda');
            }
        }

        // 7. Jika autentikasi gagal (salah username/password), kembalikan ke halaman login
        //    dengan menyertakan error flash session agar dapat ditampilkan di form.
        return back()->withErrors(['loginError' => 'Username atau password salah.']);
    }

    /**
     * Mengeluarkan pengguna dari sistem (Logout) dan membersihkan data sesi saat ini.
     * Dipanggil melalui request POST /logout terproteksi csrf.
     *
     * @param  \Illuminate\Http\Request  $request  Objek request saat ini
     * @return \Illuminate\Http\RedirectResponse  Redirect kembali ke form login
     */
    public function authenticated(Request $request): RedirectResponse
    {
        // 1. Membersihkan status autentikasi aktif pengguna pada session guard.
        Auth::logout();

        // 2. Menghapus semua data yang tersimpan di dalam session saat ini.
        $request->session()->invalidate();

        // 3. Meregenerasi token CSRF baru demi menjamin keamanan form login selanjutnya.
        $request->session()->regenerateToken();

        // 4. Mengalihkan pengguna kembali ke form login utama.
        return redirect()->route('login');
    }
}
