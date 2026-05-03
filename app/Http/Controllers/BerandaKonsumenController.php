<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\Meja;
use App\Models\Menu;
use Illuminate\Http\Request;

class BerandaKonsumenController extends Controller
{
    /**
     * Tampilkan halaman katalog menu konsumen (landing page setelah scan QR).
     */
    public function index(string $noMeja)
    {
        $meja = Meja::firstWhere('no_meja', $noMeja);

        if (! $meja) {
            abort(404);
        }

        session(['id_meja' => $meja->id_meja]);

        $kategoris = KategoriMenu::with(['menu' => function ($query) {
            $query->where('status_ketersediaan', 'tersedia');
        }])->get();

        return view('konsumen.beranda', compact('meja', 'kategoris'));
    }

    /**
     * Endpoint JSON: data menu untuk katalog (filter opsional by id_kategori).
     */
    public function getData(Request $request)
    {
        $request->validate(['id_kategori' => 'sometimes|integer|exists:kategori_menu,id_kategori']);

        $query = Menu::where('status_ketersediaan', 'tersedia')
            ->select('id_menu', 'nama_menu', 'deskripsi', 'harga', 'gambar_menu');

        if ($request->filled('id_kategori')) {
            $query->where('id_kategori', $request->input('id_kategori'));
        }

        $menus = $query->get();

        return response()->json($menus);
    }

    /**
     * Tampilkan detail satu item menu (JSON untuk modal AJAX).
     */
    public function detail(string $id)
    {
        $menu = Menu::select('id_menu', 'nama_menu', 'deskripsi', 'harga', 'gambar_menu')->find($id);

        if (! $menu) {
            abort(404);
        }

        return response()->json($menu);
    }
}
