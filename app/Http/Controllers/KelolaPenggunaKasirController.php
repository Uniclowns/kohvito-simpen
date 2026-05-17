<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class KelolaPenggunaKasirController extends Controller
{
    /**
     * Tampilkan daftar semua akun kasir.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $kasirs = User::with('role')
            ->where('id_role', 2)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->orderBy('id_users')
            ->get();

        return view('admin.kelola-pengguna-kasir', compact('kasirs', 'search'));
    }

    /**
     * Validasi dan simpan akun kasir baru (hash password).
     */
    public function storePenggunaKasir(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|min:6|max:255|unique:users,username',
            'password'     => 'required|string|min:9',
        ], [
            'nama_lengkap.required' => 'Nama lengkap pengguna wajib diisi',
            'username.required'     => 'Username pengguna wajib diisi',
            'username.min'          => 'Username minimal 6 karakter',
            'username.unique'       => 'Username sudah digunakan, silakan pilih yang lain',
            'password.required'     => 'Password wajib diisi',
            'password.min'          => 'Password harus lebih dari 8 karakter',
        ]);

        User::create([
            'id_role'      => 2,
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'password'     => bcrypt($request->password),
        ]);

        return redirect()->route('admin.pengguna-kasir.index')
            ->with('success', 'Akun kasir berhasil ditambahkan.');
    }

    /**
     * Update nama, username, atau password akun kasir.
     */
    public function updatePenggunaKasir(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Proteksi: jangan biarkan selain kasir di-edit lewat endpoint ini
        if ($user->id_role !== 2) {
            abort(403);
        }

        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|min:6|max:255|unique:users,username,' . $id . ',id_users',
        ];

        // Password optional di edit — kalau diisi baru di-validate
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:9';
        }

        $request->validate($rules, [
            'nama_lengkap.required' => 'Nama lengkap pengguna wajib diisi',
            'username.required'     => 'Username pengguna wajib diisi',
            'username.min'          => 'Username minimal 6 karakter',
            'username.unique'       => 'Username sudah digunakan, silakan pilih yang lain',
            'password.min'          => 'Password harus lebih dari 8 karakter',
        ]);

        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.pengguna-kasir.index')
            ->with('success', 'Akun kasir berhasil diperbarui.');
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
