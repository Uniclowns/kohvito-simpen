<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class KelolaPenggunaKasirController extends Controller
{
    /**
     * Tampilkan daftar semua akun kasir.
     */
    public function index()
    {
        $kasirs = User::with('role')->where('id_role', 2)->get();
        return view('admin.kelola-pengguna-kasir', compact('kasirs'));
    }

    /**
     * Validasi dan simpan akun kasir baru (hash password).
     */
    public function storePenggunaKasir(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username',
            'password'     => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'id_role'      => 2,
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'password'     => $request->password,
        ]);

        return redirect()->route('admin.pengguna-kasir.index')
            ->with('success', 'Akun kasir berhasil ditambahkan.');
    }

    /**
     * Hapus atau nonaktifkan akun kasir.
     */
    public function destroyPenggunaKasir(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id_role === 1) {
            abort(403);
        }

        $user->delete();

        return redirect()->route('admin.pengguna-kasir.index')
            ->with('success', 'Akun kasir berhasil dihapus.');
    }
}
