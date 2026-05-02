<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use Illuminate\Http\Request;

class KelolaKategoriMenuController extends Controller
{
    /**
     * Tampilkan daftar semua kategori menu.
     */
    public function index()
    {
        $kategoris = KategoriMenu::withCount('menu')->get();

        return view('admin.kelola-kategori-menu', compact('kategoris'));
    }

    /**
     * Validasi dan simpan kategori menu baru.
     */
    public function storeKategoriMenu(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_menu,nama_kategori',
        ]);

        KategoriMenu::create(['nama_kategori' => $request->nama_kategori]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Hapus kategori menu (cek relasi menu aktif terlebih dahulu).
     */
    public function destroyKategoriMenu(string $id)
    {
        $kategori = KategoriMenu::findOrFail($id);

        if ($kategori->menu()->count() > 0) {
            return redirect()->route('admin.kategori.index')->with('error', 'Kategori tidak dapat dihapus karena masih memiliki menu.');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
