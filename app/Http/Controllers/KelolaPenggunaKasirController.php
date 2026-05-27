<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class KelolaPenggunaKasirController
 * 
 * Controller ini bertugas mengatur manajemen (CRUD) akun staf Kasir di panel Administrator.
 * Meliputi filter pencarian akun, pendaftaran akun baru dengan hashing password otomatis,
 * pembaruan kredensial staf secara fleksibel (password bersifat opsional saat edit),
 * serta proteksi keamanan yang menolak manipulasi akun Administrator lewat endpoint kasir.
 *
 * @package App\Http\Controllers
 */
class KelolaPenggunaKasirController extends Controller
{
    /**
     * Tampilkan daftar seluruh staf Kasir (id_role = 2) dengan dukungan pencarian nama / username.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request pembawa parameter filter pencarian
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        // 1. Mengambil seluruh user bertipe Kasir (id_role = 2) beserta relasi role-nya
        $kasirs = User::with('role')
            ->where('id_role', 2)
            // 2. Tambahkan pencarian kondisional (pencarian nama_lengkap / username) jika parameter diisi
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
     * Memvalidasi dan menyimpan akun Kasir baru ke database dengan password terhashing otomatis.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request pembawa form isian kasir baru
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePenggunaKasir(Request $request): RedirectResponse
    {
        // 1. Validasi formal isian kasir baru beserta pesan kesalahan lokal
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

        // 2. Simpan kasir baru ke database
        //    Laravel secara otomatis menghash password dengan bcrypt lewat setter casts atau manual hashing
        User::create([
            'id_role'      => 2, // ID Role 2 diatur mutlak mewakili peran Kasir
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'password'     => bcrypt($request->password), // Enkripsi hash satu arah (one-way hashing)
        ]);

        return redirect()->route('admin.pengguna-kasir.index')
            ->with('success', 'Akun kasir berhasil ditambahkan.');
    }

    /**
     * Memvalidasi dan memperbarui data akun Kasir terpilih.
     * Menerapkan password opsional (hanya di-update dan di-hash jika diisi oleh Administrator).
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request
     * @param  string  $id  ID Kasir target perubahan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePenggunaKasir(Request $request, string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // 1. Proteksi Keamanan: Jangan biarkan akun non-kasir (misal Admin) dimodifikasi lewat rute ini
        if ($user->id_role !== 2) {
            abort(403, 'Akses ilegal.');
        }

        // 2. Tentukan aturan validasi standar untuk nama dan keunikan username (mengabaikan ID diri sendiri)
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|min:6|max:255|unique:users,username,' . $id . ',id_users',
        ];

        // 3. Tambahkan aturan validasi sandi hanya jika Administrator mengisi kolom sandi baru
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

        // 4. Susun struktur array data yang akan diperbarui
        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
        ];

        // 5. Masukkan password terhashing ke dalam array data jika diisi
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        // 6. Jalankan pembaruan data
        $user->update($data);

        return redirect()->route('admin.pengguna-kasir.index')
            ->with('success', 'Akun kasir berhasil diperbarui.');
    }

    /**
     * Menghapus akun Kasir dari database.
     * Mencegah penghapusan akun Administrator (id_role = 1) untuk perlindungan keamanan level sistem.
     *
     * @param  string  $id  ID Kasir target penghapusan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyPenggunaKasir(string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // 1. Proteksi Keamanan: Batalkan penghapusan jika sasaran adalah akun Admin
        if ($user->id_role === 1) {
            abort(403, 'Menghapus Admin dilarang.');
        }

        // 2. Hapus kasir secara bersih
        $user->delete();

        return redirect()->route('admin.pengguna-kasir.index')
            ->with('success', 'Akun kasir berhasil dihapus.');
    }
}
