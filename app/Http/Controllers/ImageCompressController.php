<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class ImageCompressController
 * 
 * Controller premium untuk melakukan transformasi dan kompresi gambar secara dinamis (On-The-Fly).
 * Bertugas memproses request gambar, mengubah resolusi (resize), mengonversi format file asli
 * (PNG/JPEG) menjadi WebP modern dengan transparansi tetap terjaga (Alpha Channel preserved),
 * merekam hasil kompresi ke folder cache rekursif untuk mempercepat load pemesanan konsumen,
 * serta menyertakan mekanisme fallback menyajikan file asli jika file hasil kompresi lebih besar.
 *
 * Rute Akses:
 *   GET /img/{type}/{file}?w=400&q=75
 *   - type: 'food' (makanan) atau 'drink' (minuman)
 *   - file: nama file gambar produk
 *
 * @package App\Http\Controllers
 */
class ImageCompressController extends Controller
{
    /**
     * Tipe direktori gambar menu yang diizinkan untuk diakses.
     *
     * @var array<int, string>
     */
    private const ALLOWED_TYPES = ['food', 'drink'];

    /**
     * Lebar piksel standar (default width) gambar jika tidak didefinisikan di URL.
     *
     * @var int
     */
    private const DEFAULT_WIDTH = 600;

    /**
     * Tingkat kualitas standar (default quality) WebP hasil kompresi (skala 1-100).
     *
     * @var int
     */
    private const DEFAULT_QUALITY = 78;

    /**
     * Durasi cache browser (Time-To-Live) yang disarankan dalam detik (31536000 detik = 1 Tahun).
     * Mempercepat performa loading aplikasi pada kunjungan berulang di device konsumen.
     *
     * @var int
     */
    private const CACHE_TTL_SECONDS = 31536000;

    /**
     * Menangani pemrosesan dynamic request gambar menu.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request pembawa parameter w (lebar) dan q (kualitas)
     * @param  string  $type  Kategori direktori gambar ('food' atau 'drink')
     * @param  string  $file  Nama file fisik gambar
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function __invoke(Request $request, string $type, string $file): BinaryFileResponse|Response
    {
        // 1. Validasi tipe folder untuk mencegah pembacaan folder sistem secara liar
        if (!in_array($type, self::ALLOWED_TYPES, true)) {
            abort(404, 'Tipe tidak dikenal.');
        }

        // 2. Validasi nama file dengan ekspresi reguler (Regex) untuk mencegah serangan directory traversal
        if (!preg_match('/^[A-Za-z0-9_\-]+\.(png|jpe?g|webp)$/i', $file)) {
            abort(404, 'Nama file tidak valid.');
        }

        // 3. Batasi lebar minimum 100px dan maksimum 1600px untuk mencegah eksploitasi beban memori RAM server
        $width   = max(100, min(1600, (int) $request->query('w', self::DEFAULT_WIDTH)));
        
        // 4. Batasi tingkat kualitas kompresi minimum 40% dan maksimum 95%
        $quality = max(40, min(95, (int) $request->query('q', self::DEFAULT_QUALITY)));

        // 5. Cek eksistensi berkas gambar asli di folder publik
        $srcPath = public_path("images/{$type}/{$file}");
        if (!is_file($srcPath)) {
            abort(404, 'Gambar tidak ditemukan.');
        }

        // 6. Tentukan nama berkas cache yang akan disimpan (Format keluaran dipaksa WebP)
        $basename  = pathinfo($file, PATHINFO_FILENAME);
        $cacheDir  = public_path("images/cache/{$type}/{$width}x{$quality}");
        $cachePath = "{$cacheDir}/{$basename}.webp";

        // 7. Bangkitkan file cache baru jika file belum ada atau berkas asli telah mengalami pembaruan (update)
        if (!is_file($cachePath) || filemtime($cachePath) < filemtime($srcPath)) {
            $this->compressImage($srcPath, $cachePath, $width, $quality);
        }

        // 8. Mekanisme Fallback: Jika ukuran file kompresi ternyata lebih besar dari file asli (misal pada WebP asli),
        //    sajikan kembali berkas asli demi efisiensi bandwidth transfer.
        $servePath = (filesize($cachePath) < filesize($srcPath)) ? $cachePath : $srcPath;

        // 9. Kirim berkas biner gambar dengan menyertakan header cache jangka panjang (browser caching)
        return response()->file($servePath, [
            'Cache-Control'   => 'public, max-age=' . self::CACHE_TTL_SECONDS . ', immutable',
            'X-Compressed-By' => 'kohvito-image-compress',
        ]);
    }

    /**
     * Memproses modifikasi ukuran gambar asli dan menyimpannya sebagai file WebP terkompresi.
     * Menjamin alpha channel (transparansi) gambar PNG tetap utuh tanpa noise hitam/putih.
     *
     * @param  string  $srcPath  Path berkas gambar asli
     * @param  string  $cachePath  Path berkas cache tujuan
     * @param  int  $maxWidth  Ukuran lebar piksel maksimum target
     * @param  int  $quality  Kualitas persentase kompresi WebP (1-100)
     * @return void
     */
    private function compressImage(string $srcPath, string $cachePath, int $maxWidth, int $quality): void
    {
        // 1. Buat direktori cache jika belum terbentuk secara otomatis
        if (!is_dir(dirname($cachePath))) {
            @mkdir(dirname($cachePath), 0755, true);
        }

        // 2. Dapatkan informasi dimensi dan tipe data gambar asli
        $info = @getimagesize($srcPath);
        if ($info === false) {
            abort(500, 'Format gambar tidak terbaca.');
        }

        [$srcW, $srcH, $type] = $info;

        // 3. Baca resource gambar berdasarkan format tipe aslinya memanfaatkan extension GD library
        $src = match ($type) {
            IMAGETYPE_PNG  => imagecreatefrompng($srcPath),
            IMAGETYPE_JPEG => imagecreatefromjpeg($srcPath),
            IMAGETYPE_WEBP => imagecreatefromwebp($srcPath),
            default        => null,
        };

        if (!$src) {
            abort(500, 'Format gambar tidak didukung.');
        }

        // 4. Hitung aspek rasio agar dimensi gambar proporsional (tidak gepeng) saat diperkecil
        $ratio = $srcW > $maxWidth ? $maxWidth / $srcW : 1.0;
        $dstW  = (int) round($srcW * $ratio);
        $dstH  = (int) round($srcH * $ratio);

        // 5. Buat canvas true color baru berdimensi target
        $dst = imagecreatetruecolor($dstW, $dstH);

        // 6. Penanganan Aspek Transparansi (Alpha Channel Preservation)
        //    Sangat penting agar background gambar berformat PNG transparan tidak berubah menjadi hitam.
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefilledrectangle($dst, 0, 0, $dstW, $dstH, $transparent);

        // 7. Lakukan penggambaran ulang dengan resolusi yang disesuaikan secara presisi
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);

        // 8. Tulis resource gambar menjadi format WebP terkompresi ke dalam folder cache
        imagewebp($dst, $cachePath, $quality);

        // 9. Bersihkan memori RAM server dari objek temporary GD image resource
        imagedestroy($src);
        imagedestroy($dst);
    }
}
