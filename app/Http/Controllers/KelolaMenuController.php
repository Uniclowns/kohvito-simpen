<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KelolaMenuController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $kategoriId = $request->get('kategori_id');

        $query = Menu::with('kategoris');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_menu', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('komposisi', 'like', "%{$search}%");
            });
        }

        if ($kategoriId) {
            $query->whereHas('kategoris', fn($q) => $q->where('kategori_menu.id_kategori', $kategoriId));
        }

        $menus = $query->paginate(20)->withQueryString();
        $kategoris = KategoriMenu::all();

        return view('admin.kelola-menu', compact('menus', 'kategoris', 'search', 'kategoriId'));
    }

    /**
     * Validasi dan simpan menu baru (termasuk upload gambar).
     */
    public function storeMenu(Request $request)
    {
        $request->validate([
            'jenis_menu'          => 'required|in:Makanan,Minuman',
            'nama_menu'           => 'required|string|max:255',
            'id_kategori'         => 'nullable|array',
            'id_kategori.*'       => 'exists:kategori_menu,id_kategori',
            'harga'               => 'required|integer|min:1',
            'stock'               => 'required|integer|min:0',
            'deskripsi'           => 'required|string|max:500',
            'komposisi'           => 'nullable|string|max:500',
            'is_pedas'            => 'nullable|boolean',
            'tipe_minuman'        => 'required_if:jenis_menu,Minuman|nullable|in:Panas,Dingin,Keduanya',
            'gambar_menu'         => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $kategoriIds = $request->input('id_kategori', []);
        if (empty($kategoriIds)) {
            $kategoriIds = [$request->jenis_menu === 'Makanan' ? 3 : 1];
        }

        $status = $request->stock > 0 ? 'Tersedia' : 'Tidak Tersedia';

        $kategoriMakanan = null;
        if ($request->jenis_menu === 'Makanan') {
            $kategoriMakanan = $request->has('is_pedas') ? 'Pedas' : 'Tidak Pedas';
        }

        $gambarFilename = null;
        if ($request->hasFile('gambar_menu')) {
            $gambarFilename = $this->saveMenuImage($request->file('gambar_menu'));
        }

        $menu = Menu::create([
            'nama_menu'           => $request->nama_menu,
            'deskripsi'           => $request->deskripsi,
            'komposisi'           => $request->komposisi,
            'harga'               => $request->harga,
            'stock'               => $request->stock,
            'gambar_menu'         => $gambarFilename,
            'status_ketersediaan' => $status,
            'jenis_menu'          => $request->jenis_menu,
            'kategori_makanan'    => $kategoriMakanan,
            'tipe_minuman'        => $request->jenis_menu === 'Minuman' ? $request->tipe_minuman : null,
        ]);

        $menu->kategoris()->sync($kategoriIds);

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menu berhasil ditambahkan.')
            ->with('menu_action_success', 'add');
    }

    /**
     * Validasi dan update data menu yang sudah ada.
     */
    public function updateMenu(Request $request, string $id)
    {
        $menu = Menu::findOrFail($id);

        $request->validate([
            'jenis_menu'          => 'required|in:Makanan,Minuman',
            'nama_menu'           => 'required|string|max:255',
            'id_kategori'         => 'nullable|array',
            'id_kategori.*'       => 'exists:kategori_menu,id_kategori',
            'harga'               => 'required|integer|min:1',
            'stock'               => 'required|integer|min:0',
            'deskripsi'           => 'required|string|max:500',
            'komposisi'           => 'nullable|string|max:500',
            'is_pedas'            => 'nullable|boolean',
            'tipe_minuman'        => 'required_if:jenis_menu,Minuman|nullable|in:Panas,Dingin,Keduanya',
            'gambar_menu'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $kategoriIds = $request->input('id_kategori', []);
        if (empty($kategoriIds)) {
            $kategoriIds = [$request->jenis_menu === 'Makanan' ? 3 : 1];
        }

        $status = $request->stock > 0 ? 'Tersedia' : 'Tidak Tersedia';

        $kategoriMakanan = null;
        if ($request->jenis_menu === 'Makanan') {
            $kategoriMakanan = $request->has('is_pedas') ? 'Pedas' : 'Tidak Pedas';
        }

        $gambarFilename = $menu->gambar_menu;
        if ($request->hasFile('gambar_menu')) {
            if ($gambarFilename) {
                Storage::disk('public')->delete('menu-images/' . $gambarFilename);
            }
            $gambarFilename = $this->saveMenuImage($request->file('gambar_menu'));
        }

        $menu->nama_menu           = $request->nama_menu;
        $menu->deskripsi           = $request->deskripsi;
        $menu->komposisi           = $request->komposisi;
        $menu->harga               = $request->harga;
        $menu->stock               = $request->stock;
        $menu->gambar_menu         = $gambarFilename;
        $menu->status_ketersediaan = $status;
        $menu->jenis_menu          = $request->jenis_menu;
        $menu->kategori_makanan    = $kategoriMakanan;
        $menu->tipe_minuman        = $request->jenis_menu === 'Minuman' ? $request->tipe_minuman : null;

        try {
            $menu->save();
            $menu->kategoris()->sync($kategoriIds);
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('menu_action_error', 'edit');
        }

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menu berhasil diperbarui.')
            ->with('menu_action_success', 'edit');
    }

    /**
     * Simpan gambar menu dengan auto-resize ke 854x440.
     */
    private function saveMenuImage($file): string
    {
        $filename = time() . '_' . uniqid() . '.webp';
        $savePath = storage_path('app/public/menu-images/' . $filename);
        
        if (!is_dir(dirname($savePath))) {
            @mkdir(dirname($savePath), 0755, true);
        }

        $info = getimagesize($file->getRealPath());
        [$srcW, $srcH, $type] = $info;

        $src = match ($type) {
            IMAGETYPE_PNG  => imagecreatefrompng($file->getRealPath()),
            IMAGETYPE_JPEG => imagecreatefromjpeg($file->getRealPath()),
            IMAGETYPE_WEBP => imagecreatefromwebp($file->getRealPath()),
            default        => abort(400, 'Format gambar tidak didukung.'),
        };

        // Resize & Crop ke 16:9 (854x440)
        $targetW = 854;
        $targetH = 440;
        $targetRatio = $targetW / $targetH;
        $srcRatio = $srcW / $srcH;

        if ($srcRatio > $targetRatio) {
            // Source lebih lebar -> crop kiri kanan
            $cutW = (int) ($srcH * $targetRatio);
            $cutH = $srcH;
            $srcX = (int) (($srcW - $cutW) / 2);
            $srcY = 0;
        } else {
            // Source lebih tinggi -> crop atas bawah
            $cutW = $srcW;
            $cutH = (int) ($srcW / $targetRatio);
            $srcX = 0;
            $srcY = (int) (($srcH - $cutH) / 2);
        }

        $dst = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        
        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $targetW, $targetH, $cutW, $cutH);
        imagewebp($dst, $savePath, 85);

        imagedestroy($src);
        imagedestroy($dst);

        return $filename;
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

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menu berhasil dihapus.')
            ->with('menu_action_success', 'delete');
    }
}
