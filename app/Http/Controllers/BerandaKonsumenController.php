<?php

namespace App\Http\Controllers;

use App\Models\KategoriMenu;
use App\Models\Meja;
use App\Models\Menu;
use App\Traits\CartSessionScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class BerandaKonsumenController
 *
 * Controller ini bertindak sebagai gerbang masuk utama antarmuka Konsumen.
 * Mengelola logic pencocokan sesi unik per meja kafe setelah pemindaian kode QR (*scan QR*),
 * standardisasi session-scoping konsumen baru/pindah meja, penyediaan data katalog menu,
 * serta menyajikan fragment HTML detail hidangan untuk disisipkan ke dalam slide-up drawer mobile
 * atau dialog centered box pada desktop.
 */
class BerandaKonsumenController extends Controller
{
    use CartSessionScope;

    /**
     * Tampilkan halaman utama katalog menu konsumen (Landing Page scan QR).
     * Menerapkan session-scoping dinamis berdasarkan parameter URL `?u={code}` dan nomor meja.
     * Mencegah tumpang tindih keranjang antar user yang memindai meja sama atau saat berpindah meja fisik.
     *
     * @param  string  $noMeja  Nomor identitas meja asal scan QR
     * @param  Request  $request  Objek HTTP request aktif
     * @return View|RedirectResponse
     */
    public function index(string $noMeja, Request $request)
    {
        // 1. Cari data meja berdasarkan nomor unik meja di database
        $meja = Meja::firstWhere('no_meja', $noMeja);

        if (! $meja) {
            abort(404);
        }

        // Pulihkan riwayat pesanan dari cookie backup (30 hari) ke session.
        $this->restoreRiwayatDariCookie();

        // Simpan konteks meja hanya sebagai cache tampilan/backward-compat.
        // Sumber utama keranjang adalah URL {noMeja} + cart_scope per-tab dari sessionStorage.
        session([
            'id_meja' => $meja->id_meja,
            'id_meja_no' => $meja->no_meja,
        ]);

        // 9. Ambil semua kategori menu beserta menu terikat yang berstatus 'Tersedia' (tersaring rapi)
        $kategoris = KategoriMenu::with(['menus' => function ($query) {
            $query->where('status_ketersediaan', 'Tersedia');
        }])->get();

        // 10. Kembalikan view katalog beranda konsumen
        return view('konsumen.beranda', compact('meja', 'kategoris'));
    }

    /**
     * Endpoint API JSON: Memperoleh daftar katalog menu berstatus 'Tersedia' untuk keperluan filtering instan.
     * Mendukung pemfilteran opsional berbasis id_kategori.
     *
     * @param  Request  $request  Objek HTTP request pembawa filter kategori
     */
    public function getData(Request $request): JsonResponse
    {
        // 1. Validasi opsional parameter kategori jika disertakan
        $request->validate([
            'id_kategori' => 'sometimes|integer|exists:kategori_menu,id_kategori',
        ]);

        // 2. Susun query pencarian menu yang berstatus aktif/tersedia
        $query = Menu::where('status_ketersediaan', 'Tersedia')
            ->select('id_menu', 'nama_menu', 'deskripsi', 'harga', 'gambar_menu', 'jenis_menu');

        // 3. Tambahkan filter kategori jika form dikirimkan oleh pengguna
        if ($request->filled('id_kategori')) {
            $kategoriId = $request->input('id_kategori');
            $query->whereHas('kategoris', function ($q) use ($kategoriId) {
                $q->where('kategori_menu.id_kategori', $kategoriId);
            });
        }

        $menus = $query->get();

        return response()->json($menus);
    }

    /**
     * Tampilkan detail menu lengkap beserta kustomisasi komposisi bahan.
     * Menyediakan dua variasi keluaran (Hybrid Mode):
     * 1. **Partial Fragment**: Dipicu via permintaan AJAX atau parameter `?partial=1`,
     *    mengembalikan potongan HTML saja untuk di-inject ke sheet slide-up / modal centered dialog.
     * 2. **Full Page**: Permintaan penelusuran URL biasa, menyajikan halaman penuh mandiri.
     *
     * @param  Request  $request  Objek HTTP request
     * @param  string  $id  ID Unik menu yang dicari detailnya
     */
    public function detail(Request $request, string $noMeja, string $id): View
    {
        // 1. Tarik detail menu beserta komposisi bahan baku hidangan
        $menu = Menu::select(
            'id_menu', 'nama_menu', 'deskripsi', 'komposisi', 'harga', 'stock',
            'gambar_menu', 'jenis_menu', 'kategori_makanan', 'tipe_minuman'
        )->find($id);

        if (! $menu) {
            abort(404);
        }

        // 2. Hubungkan data meja dari request partial dulu.
        //    Jangan percaya session untuk modal: satu browser bisa buka banyak meja.
        $mejaId = $request->input('id_meja') ?: session('id_meja');
        $meja = $mejaId ? Meja::find($mejaId) : null;

        // 3. Evaluasi apakah client meminta respon parsial (AJAX / partial=true)
        $wantsPartial = $request->ajax() || $request->boolean('partial');

        // 4. Render template yang sesuai berdasarkan jenis request
        return view(
            $wantsPartial ? 'konsumen.partials.detail-menu-content' : 'konsumen.detail-menu',
            compact('menu', 'meja')
        );
    }
}
