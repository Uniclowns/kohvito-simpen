<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class SuperadminController
 *
 * Pusat kendali (hub) untuk akun Super Admin — god-mode user yang dapat
 * mengakses seluruh panel sistem (Admin, Kasir, Konsumen). Controller ini
 * menyediakan:
 *   1. Dashboard hub (beranda) berisi kartu navigasi & statistik ringkas.
 *   2. CRUD penuh terhadap akun Administrator (id_role = 1).
 *
 * Manajemen meja/QR berada di KelolaMejaController; manajemen kasir tetap
 * di KelolaPenggunaKasirController (dapat diakses Super Admin via god-mode).
 *
 * @package App\Http\Controllers
 */
class SuperadminController extends Controller
{
    /** ID role konstan untuk keterbacaan & menghindari magic number. */
    private const ROLE_ADMIN = 1;
    private const ROLE_KASIR = 2;
    private const ROLE_SUPERADMIN = 3;

    /**
     * Dashboard hub Super Admin — launchpad ke semua panel + statistik ringkas.
     *
     * @return \Illuminate\View\View
     */
    public function beranda(): View
    {
        $stats = [
            'admin' => User::where('id_role', self::ROLE_ADMIN)->count(),
            'kasir' => User::where('id_role', self::ROLE_KASIR)->count(),
            'meja'  => Meja::count(),
        ];

        // Meja pertama dipakai sebagai target tombol "Lihat sebagai Konsumen".
        $firstMeja = Meja::orderBy('no_meja')->first()?->no_meja;

        return view('superadmin.beranda', compact('stats', 'firstMeja'));
    }

    /**
     * Tampilkan daftar akun Administrator (id_role = 1) dengan pencarian.
     *
     * @param  \Illuminate\Http\Request  $request  Pembawa parameter ?search=
     * @return \Illuminate\View\View
     */
    public function indexAdmin(Request $request): View
    {
        $search = $request->query('search');

        $admins = User::with('role')
            ->where('id_role', self::ROLE_ADMIN)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->orderBy('id_users')
            ->get();

        return view('superadmin.kelola-admin', compact('admins', 'search'));
    }

    /**
     * Validasi & simpan akun Administrator baru (password di-hash bcrypt).
     *
     * @param  \Illuminate\Http\Request  $request  Pembawa form akun admin baru
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAdmin(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|min:6|max:255|unique:users,username',
            'password'     => 'required|string|min:9',
        ], [
            'nama_lengkap.required' => 'Nama lengkap admin wajib diisi',
            'username.required'     => 'Username admin wajib diisi',
            'username.min'          => 'Username minimal 6 karakter',
            'username.unique'       => 'Username sudah digunakan, silakan pilih yang lain',
            'password.required'     => 'Password wajib diisi',
            'password.min'          => 'Password harus lebih dari 8 karakter',
        ]);

        User::create([
            'id_role'      => self::ROLE_ADMIN,
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'password'     => bcrypt($request->password),
        ]);

        return redirect()->route('superadmin.admin.index')
            ->with('success', 'Akun admin berhasil ditambahkan.');
    }

    /**
     * Validasi & perbarui akun Administrator. Password opsional saat edit.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id  ID admin target perubahan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAdmin(Request $request, string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Proteksi: endpoint ini hanya boleh memodifikasi akun Admin (id_role = 1).
        // Mencegah Super Admin tak sengaja mengubah akun kasir/super admin lewat sini.
        if ($user->id_role !== self::ROLE_ADMIN) {
            abort(403, 'Endpoint ini hanya untuk mengelola akun Admin.');
        }

        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|min:6|max:255|unique:users,username,' . $id . ',id_users',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:9';
        }

        $request->validate($rules, [
            'nama_lengkap.required' => 'Nama lengkap admin wajib diisi',
            'username.required'     => 'Username admin wajib diisi',
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

        return redirect()->route('superadmin.admin.index')
            ->with('success', 'Akun admin berhasil diperbarui.');
    }

    /**
     * Hapus akun Administrator. Dilindungi dua aturan:
     *  - Hanya boleh menghapus akun ber-role Admin (id_role = 1).
     *  - Tidak boleh menghapus admin terakhir (mencegah sistem tanpa admin).
     *
     * @param  string  $id  ID admin target penghapusan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyAdmin(string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id_role !== self::ROLE_ADMIN) {
            abort(403, 'Hanya akun Admin yang dapat dihapus dari sini.');
        }

        // Cegah penghapusan admin terakhir agar panel admin tidak yatim.
        if (User::where('id_role', self::ROLE_ADMIN)->count() <= 1) {
            return redirect()->route('superadmin.admin.index')
                ->with('error', 'Tidak dapat menghapus admin terakhir. Minimal harus ada 1 admin.');
        }

        $user->delete();

        return redirect()->route('superadmin.admin.index')
            ->with('success', 'Akun admin berhasil dihapus.');
    }
}
