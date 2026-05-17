<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelolaKategoriMenuController extends Controller
{
    /**
     * Tampilkan daftar semua kategori menu.
     */
    public function index()
    {
        $kategoris = KategoriMenu::withCount('menus')->get();

        return view('admin.kelola-kategori-menu', compact('kategoris'));
    }

    /**
     * Validasi dan simpan kategori menu baru.
     */
    public function storeKategoriMenu(Request $request)
    {
        $request->merge([
            'nama_kategori' => trim((string) $request->input('nama_kategori')),
        ]);

        if (KategoriMenu::where('nama_kategori', $request->input('nama_kategori'))->exists()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('status_modal', [
                    'id' => 'modal-gagal-tambah-kategori',
                    'title' => 'Gagal Menambah Kategori Menu',
                    'message' => 'Nama kategori yang Anda masukkan sudah digunakan. Silakan gunakan nama kategori lain.',
                    'buttonLabel' => 'Tutup',
                    'variant' => 'error',
                ]);
        }

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

        KategoriMenu::create(['nama_kategori' => $request->nama_kategori]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Update nama kategori menu existing.
     */
    public function updateKategoriMenu(Request $request, string $id)
    {
        $kategori = KategoriMenu::findOrFail($id);

        $request->merge([
            'nama_kategori' => trim((string) $request->input('nama_kategori')),
        ]);

        if (KategoriMenu::where('nama_kategori', $request->input('nama_kategori'))
            ->where('id_kategori', '!=', $id)
            ->exists()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('status_modal', [
                    'id' => 'modal-gagal-update-kategori',
                    'title' => 'Gagal Memperbarui Kategori Menu',
                    'message' => 'Nama kategori yang Anda masukkan sudah digunakan. Silakan gunakan nama kategori lain.',
                    'buttonLabel' => 'Tutup',
                    'variant' => 'error',
                ]);
        }

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

        $kategori->update(['nama_kategori' => $request->nama_kategori]);

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Hapus kategori menu (cek relasi menu aktif terlebih dahulu).
     */
    public function destroyKategoriMenu(string $id)
    {
        $kategori = KategoriMenu::findOrFail($id);

        if ($kategori->menus()->count() > 0) {
            return redirect()->route('admin.kategori.index')->with('error', 'Kategori tidak dapat dihapus karena masih memiliki menu.');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
