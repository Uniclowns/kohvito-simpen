<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KelolaMenuController extends Controller
{
    /**
     * Tampilkan daftar semua menu dengan paginasi.
     */
    public function index()
    {
        $menus = Menu::with('kategori')->paginate(10);
        $kategoris = KategoriMenu::all();

        return view('admin.kelola-menu', compact('menus', 'kategoris'));
    }

    /**
     * Validasi dan simpan menu baru (termasuk upload gambar).
     */
    public function storeMenu(Request $request)
    {
        $request->validate([
            'nama_menu'           => 'required|string|max:255',
            'id_kategori'         => 'required|exists:kategori_menu,id_kategori',
            'deskripsi'           => 'nullable|string',
            'harga'               => 'required|integer|min:0',
            'gambar_menu'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status_ketersediaan' => 'required|in:Tersedia,Tidak Tersedia',
        ]);

        $gambarFilename = null;
        if ($request->hasFile('gambar_menu')) {
            $gambarFilename = $request->file('gambar_menu')->store('menu-images', 'public');
            $gambarFilename = basename($gambarFilename);
        }

        Menu::create([
            'nama_menu'           => $request->nama_menu,
            'id_kategori'         => $request->id_kategori,
            'deskripsi'           => $request->deskripsi,
            'harga'               => $request->harga,
            'gambar_menu'         => $gambarFilename,
            'status_ketersediaan' => $request->status_ketersediaan,
        ]);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * Validasi dan update data menu yang sudah ada.
     */
    public function updateMenu(Request $request, string $id)
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'nama_menu'           => 'required|string|max:255',
            'id_kategori'         => 'required|exists:kategori_menu,id_kategori',
            'deskripsi'           => 'nullable|string',
            'harga'               => 'required|integer|min:0',
            'gambar_menu'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status_ketersediaan' => 'required|in:Tersedia,Tidak Tersedia',
        ]);

        $gambarFilename = $menu->gambar_menu;
        if ($request->hasFile('gambar_menu')) {
            if ($gambarFilename) {
                Storage::disk('public')->delete('menu-images/' . $gambarFilename);
            }
            $stored = $request->file('gambar_menu')->store('menu-images', 'public');
            $gambarFilename = basename($stored);
        }

        $menu->nama_menu           = $request->nama_menu;
        $menu->id_kategori         = $request->id_kategori;
        $menu->deskripsi           = $request->deskripsi;
        $menu->harga               = $request->harga;
        $menu->gambar_menu         = $gambarFilename;
        $menu->status_ketersediaan = $request->status_ketersediaan;
        $menu->save();

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Hapus menu berdasarkan ID.
     */
    public function destroyMenu(string $id)
    {
        $menu = Menu::findOrFail($id);

        if ($menu->gambar_menu) {
            Storage::disk('public')->delete('menu-images/' . $menu->gambar_menu);
        }

        $menu->delete();

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil dihapus.');
    }
}
