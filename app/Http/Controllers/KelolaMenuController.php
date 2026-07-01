<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveMenuRequest;
use App\Models\KategoriMenu;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Class KelolaMenuController
 *
 * Controller ini bertindak sebagai pengatur modul manajemen (CRUD) katalog menu makanan/minuman
 * di panel Administrator. Menyediakan fitur visualisasi katalog berpaginasi, pemfilteran berdasarkan
 * kategori master, pemrosesan unggah gambar menu dengan pemotongan aspek rasio 16:9 (854x440 px),
 * konversi gambar dinamis ke WebP, serta penanganan sinkronisasi relasi pivot tabel kategori.
 */
class KelolaMenuController extends Controller
{
    /**
     * Tampilkan antarmuka daftar menu (Kelola Menu) dengan dukungan pencarian & filter kategori.
     *
     * @param  Request  $request  Objek HTTP request pembawa parameter filter pencarian
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $kategoriId = $request->get('kategori_id');

        // 1. Inisialisasi query Eloquent untuk model Menu beserta eager loading kategori terkait
        $query = Menu::with('kategoris');

        // 2. Tambahkan klausa pencarian kata kunci multi-kolom (nama menu, deskripsi, komposisi)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_menu', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('komposisi', 'like', "%{$search}%");
            });
        }

        // 3. Tambahkan penyaringan berdasarkan kategori master terpilih
        if ($kategoriId) {
            $query->whereHas('kategoris', function ($q) use ($kategoriId) {
                $q->where('kategori_menu.id_kategori', $kategoriId);
            });
        }

        // 4. Lakukan paginasi hasil (20 item per halaman) dengan mempertahankan parameter filter URL aktif
        $menus = $query->paginate(20)->withQueryString();

        // 5. Ambil daftar seluruh kategori menu untuk opsi isian form modal
        $kategoris = KategoriMenu::all();

        return view('admin.kelola-menu', compact('menus', 'kategoris', 'search', 'kategoriId'));
    }

    /**
     * Memvalidasi dan menyimpan data produk menu baru ke database beserta manipulasi unggah berkas gambar.
     *
     * @param  SaveMenuRequest  $request  Form request khusus validasi input menu
     */
    public function storeMenu(SaveMenuRequest $request): RedirectResponse
    {
        $kategoriIds = $this->resolveKategoriIds($request);

        // Gambar wajib saat create (lihat SaveMenuRequest); simpan hasil resize WebP-nya
        $gambarFilename = $request->hasFile('gambar_menu')
            ? $this->saveMenuImage($request->file('gambar_menu'))
            : null;

        $menu = Menu::create($this->buildMenuAttributes($request, $gambarFilename));
        $menu->kategoris()->sync($kategoriIds);

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menu berhasil ditambahkan.')
            ->with('menu_action_success', 'add');
    }

    /**
     * Memvalidasi dan memperbarui data produk menu terpilih di database.
     * Mengapus gambar lama secara bersih dari sistem penyimpanan jika melampirkan gambar baru.
     *
     * @param  SaveMenuRequest  $request  Form request validasi input menu
     * @param  string  $id  ID menu target perubahan
     */
    public function updateMenu(SaveMenuRequest $request, string $id): RedirectResponse
    {
        $menu = Menu::findOrFail($id);
        $kategoriIds = $this->resolveKategoriIds($request);

        // Pertahankan gambar lama; bila ada unggahan baru, hapus berkas lama lalu simpan yang baru
        $gambarFilename = $menu->gambar_menu;
        if ($request->hasFile('gambar_menu')) {
            if ($gambarFilename) {
                Storage::disk('public')->delete('menu-images/'.$gambarFilename);
            }
            $gambarFilename = $this->saveMenuImage($request->file('gambar_menu'));
        }

        try {
            $menu->fill($this->buildMenuAttributes($request, $gambarFilename))->save();
            // Sinkronisasi ulang relasi pivot kategori
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
     * Susun atribut model menu dari input form. Dipakai bersama oleh proses tambah & ubah
     * agar penurunan status ketersediaan (dari stok) serta kategori makanan/tipe minuman
     * (dari jenis menu) konsisten di kedua jalur.
     *
     * @return array<string, mixed>
     */
    private function buildMenuAttributes(SaveMenuRequest $request, ?string $gambarFilename): array
    {
        return [
            'nama_menu' => $request->nama_menu,
            'deskripsi' => $request->deskripsi,
            'komposisi' => $request->komposisi,
            'harga' => $request->harga,
            'stock' => $request->stock,
            'gambar_menu' => $gambarFilename,
            'status_ketersediaan' => $request->stock > 0 ? 'Tersedia' : 'Tidak Tersedia',
            'jenis_menu' => $request->jenis_menu,
            'kategori_makanan' => $request->jenis_menu === 'Makanan'
                ? ($request->has('is_pedas') ? 'Pedas' : 'Tidak Pedas')
                : null,
            'tipe_minuman' => $request->jenis_menu === 'Minuman' ? $request->tipe_minuman : null,
        ];
    }

    /**
     * Tentukan ID kategori terpilih; fallback ke kategori default bila input kosong
     * (Makanan → 3, Minuman → 1).
     *
     * @return array<int, mixed>
     */
    private function resolveKategoriIds(SaveMenuRequest $request): array
    {
        $kategoriIds = $request->input('id_kategori', []);

        return empty($kategoriIds)
            ? [$request->jenis_menu === 'Makanan' ? 3 : 1]
            : $kategoriIds;
    }

    /**
     * Memanipulasi, memotong (cropping), dan menyimpan berkas gambar menu ke penyimpanan publik.
     * Secara otomatis memaksa aspek rasio gambar menjadi widescreen 16:9 berukuran 854x440 px
     * serta mengonversinya menjadi format WebP terkompresi demi kecepatan load aplikasi.
     *
     * @param  mixed  $file  Berkas upload mentah dari form request
     * @return string Nama unik file gambar hasil manipulasi
     */
    private function saveMenuImage(mixed $file): string
    {
        // 1. Bangkitkan nama berkas unik berbasis timestamp dan ID unik
        $filename = time().'_'.uniqid().'.webp';
        $savePath = storage_path('app/public/menu-images/'.$filename);

        // 2. Buat subfolder tujuan jika belum terbentuk
        if (! is_dir(dirname($savePath))) {
            @mkdir(dirname($savePath), 0755, true);
        }

        // 3. Dapatkan dimensi dan tipe biner gambar asli
        $info = getimagesize($file->getRealPath());
        [$srcW, $srcH, $type] = $info;

        // 4. Bangkitkan temporary resource GD image sesuai format file asal
        $src = match ($type) {
            IMAGETYPE_PNG => imagecreatefrompng($file->getRealPath()),
            IMAGETYPE_JPEG => imagecreatefromjpeg($file->getRealPath()),
            IMAGETYPE_WEBP => imagecreatefromwebp($file->getRealPath()),
            default => abort(400, 'Format gambar tidak didukung.'),
        };

        // 5. Rekayasa Kalkulasi Resize & Crop Tengah ke 16:9 (854x440 px)
        $targetW = 854;
        $targetH = 440;
        $targetRatio = $targetW / $targetH;
        $srcRatio = $srcW / $srcH;

        if ($srcRatio > $targetRatio) {
            // Gambar asal terlalu lebar: Potong sisi kiri dan kanan secara proporsional dari tengah
            $cutW = (int) ($srcH * $targetRatio);
            $cutH = $srcH;
            $srcX = (int) (($srcW - $cutW) / 2);
            $srcY = 0;
        } else {
            // Gambar asal terlalu tinggi: Potong sisi atas dan bawah secara proporsional dari tengah
            $cutW = $srcW;
            $cutH = (int) ($srcW / $targetRatio);
            $srcX = 0;
            $srcY = (int) (($srcH - $cutH) / 2);
        }

        // 6. Buat canvas target kosong dan lestarikan properti transparansi PNG
        $dst = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        // 7. Salin potongan gambar asli dengan metode interpolasi piksel yang halus (resampled)
        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $targetW, $targetH, $cutW, $cutH);

        // 8. Tulis berkas target sebagai WebP dengan level kualitas optimal 85%
        imagewebp($dst, $savePath, 85);

        // 9. Bebaskan memori server dari image resource
        imagedestroy($src);
        imagedestroy($dst);

        return $filename;
    }

    /**
     * Hapus permanen menu beserta file fisiknya dari media penyimpanan.
     *
     * @param  string  $id  ID menu target penghapusan
     */
    public function destroyMenu(string $id): RedirectResponse
    {
        $menu = Menu::findOrFail($id);

        // 1. Bersihkan berkas gambar dari public storage
        if ($menu->gambar_menu) {
            Storage::disk('public')->delete('menu-images/'.$menu->gambar_menu);
        }

        // 2. Hapus baris data menu dari database
        $menu->delete();

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menu berhasil dihapus.')
            ->with('menu_action_success', 'delete');
    }
}
