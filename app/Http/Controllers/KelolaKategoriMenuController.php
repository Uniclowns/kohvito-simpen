<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Class KelolaKategoriMenuController
 * 
 * Controller ini mengatur manajemen (CRUD) kategori menu di panel Administrator.
 * Menyediakan pengelompokan menu, validasi duplikasi kategori yang aman (tanpa case-sensitive conflict),
 * integrasi feedback UI modal kegagalan kustom, serta proteksi penghapusan kategori yang masih
 * memiliki ketergantungan relasi menu aktif (Restricted Delete Protection).
 *
 * @package App\Http\Controllers
 */
class KelolaKategoriMenuController extends Controller
{
    /**
     * Tampilkan daftar seluruh kategori menu beserta jumlah menu yang terkait di dalamnya.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // 1. Mengambil seluruh kategori menu beserta hitungan (agregasi) jumlah menu terkait secara eager-loading
        $kategoris = KategoriMenu::withCount('menus')->get();

        return view('admin.kelola-kategori-menu', compact('kategoris'));
    }

    /**
     * Validasi dan simpan kategori menu baru ke database.
     * Mencegah duplikasi nama kategori menggunakan validasi manual dan sistem integrasi Modal Error.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request pembawa data kategori
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeKategoriMenu(Request $request): RedirectResponse
    {
        // 1. Bersihkan spasi berlebih di awal & akhir baris nama kategori agar pencarian presisi
        $request->merge([
            'nama_kategori' => trim((string) $request->input('nama_kategori')),
        ]);

        // 2. Cek apakah nama kategori tersebut sudah digunakan sebelumnya di database
        if (KategoriMenu::where('nama_kategori', $request->input('nama_kategori'))->exists()) {
            // 3. Jika ya, segera alihkan kembali dengan menyertakan payload modal status error kustom
            return redirect()
                ->back()
                ->withInput()
                ->with('status_modal', [
                    'id'          => 'modal-gagal-tambah-kategori',
                    'title'       => 'Gagal Menambah Kategori Menu',
                    'message'     => 'Nama kategori yang Anda masukkan sudah digunakan. Silakan gunakan nama kategori lain.',
                    'buttonLabel' => 'Tutup',
                    'variant'     => 'error',
                ]);
        }

        // 4. Jalankan validasi formal tingkat lanjut menggunakan Rule Eloquent bawaan
        $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kategori_menu', 'nama_kategori'),
            ],
        ], [
            'nama_kategori.unique' => 'Nama kategori yang Anda masukkan sudah digunakan. Silakan gunakan nama kategori lain.',
        ]);

        // 5. Simpan kategori baru ke database
        KategoriMenu::create(['nama_kategori' => $request->nama_kategori]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Memperbarui nama kategori menu terpilih.
     * Mengabaikan ID kategori saat ini (ignore self) ketika memvalidasi keunikan nama kategori.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request
     * @param  string  $id  ID kategori target perubahan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateKategoriMenu(Request $request, string $id): RedirectResponse
    {
        $kategori = KategoriMenu::findOrFail($id);

        // 1. Lakukan pemangkasan spasi (trimming) nama kategori
        $request->merge([
            'nama_kategori' => trim((string) $request->input('nama_kategori')),
        ]);

        // 2. Validasi duplikasi dengan mengecualikan ID kategori milik dirinya sendiri
        if (KategoriMenu::where('nama_kategori', $request->input('nama_kategori'))
            ->where('id_kategori', '!=', $id)
            ->exists()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('status_modal', [
                    'id'          => 'modal-gagal-update-kategori',
                    'title'       => 'Gagal Memperbarui Kategori Menu',
                    'message'     => 'Nama kategori yang Anda masukkan sudah digunakan. Silakan gunakan nama kategori lain.',
                    'buttonLabel' => 'Tutup',
                    'variant'     => 'error',
                ]);
        }

        // 3. Menjalankan validasi formal ignore self
        $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kategori_menu', 'nama_kategori')->ignore($id, 'id_kategori'),
            ],
        ], [
            'nama_kategori.unique' => 'Nama kategori yang Anda masukkan sudah digunakan. Silakan gunakan nama kategori lain.',
        ]);

        // 4. Simpan perubahan ke database
        $kategori->update(['nama_kategori' => $request->nama_kategori]);

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Menghapus kategori menu terpilih dari database.
     * Melindungi integritas data: Kategori yang memiliki keterikatan menu tidak boleh dihapus.
     *
     * @param  string  $id  ID kategori target penghapusan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyKategoriMenu(string $id): RedirectResponse
    {
        $kategori = KategoriMenu::findOrFail($id);

        // 1. Cek apakah ada menu yang sedang berelasi dengan kategori ini
        if ($kategori->menus()->count() > 0) {
            // 2. Jika ada, gagalkan penghapusan dan kembalikan pesan error
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki menu.');
        }

        // 3. Hapus kategori secara bersih
        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
