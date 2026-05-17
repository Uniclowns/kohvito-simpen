<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serves menu images compressed/resized on-the-fly.
 *
 * GET /img/{type}/{file}
 *   - type: 'food' atau 'drink'
 *   - file: nama file di public/images/{type}/{file}
 *
 * Compressed output di-cache di public/images/cache/{type}/{w}x{q}/{file}
 * (w = max width, q = quality). Request berikutnya langsung baca dari cache.
 */
class ImageCompressController extends Controller
{
    private const ALLOWED_TYPES = ['food', 'drink'];
    private const DEFAULT_WIDTH = 600;
    private const DEFAULT_QUALITY = 78;
    private const CACHE_TTL_SECONDS = 31536000; // 1 tahun

    public function __invoke(Request $request, string $type, string $file): BinaryFileResponse|Response
    {
        if (!in_array($type, self::ALLOWED_TYPES, true)) {
            abort(404, 'Tipe tidak dikenal.');
        }

        if (!preg_match('/^[A-Za-z0-9_\-]+\.(png|jpe?g|webp)$/i', $file)) {
            abort(404, 'Nama file tidak valid.');
        }

        $width = max(100, min(1600, (int) $request->query('w', self::DEFAULT_WIDTH)));
        $quality = max(40, min(95, (int) $request->query('q', self::DEFAULT_QUALITY)));

        $srcPath = public_path("images/{$type}/{$file}");
        if (!is_file($srcPath)) {
            abort(404, 'Gambar tidak ditemukan.');
        }

        // Cache selalu sebagai .webp — kompresi terbaik untuk PNG/JPEG dengan alpha support.
        $basename = pathinfo($file, PATHINFO_FILENAME);
        $cacheDir = public_path("images/cache/{$type}/{$width}x{$quality}");
        $cachePath = "{$cacheDir}/{$basename}.webp";

        if (!is_file($cachePath) || filemtime($cachePath) < filemtime($srcPath)) {
            $this->compressImage($srcPath, $cachePath, $width, $quality);
        }

        // Jika hasil kompresi masih lebih besar dari source, serve source asli.
        $servePath = (filesize($cachePath) < filesize($srcPath)) ? $cachePath : $srcPath;

        return response()->file($servePath, [
            'Cache-Control'   => 'public, max-age=' . self::CACHE_TTL_SECONDS . ', immutable',
            'X-Compressed-By' => 'kohvito-image-compress',
        ]);
    }

    private function compressImage(string $srcPath, string $cachePath, int $maxWidth, int $quality): void
    {
        if (!is_dir(dirname($cachePath))) {
            @mkdir(dirname($cachePath), 0755, true);
        }

        $info = @getimagesize($srcPath);
        if ($info === false) {
            abort(500, 'Format gambar tidak terbaca.');
        }

        [$srcW, $srcH, $type] = $info;

        $src = match ($type) {
            IMAGETYPE_PNG  => imagecreatefrompng($srcPath),
            IMAGETYPE_JPEG => imagecreatefromjpeg($srcPath),
            IMAGETYPE_WEBP => imagecreatefromwebp($srcPath),
            default        => null,
        };

        if (!$src) {
            abort(500, 'Format gambar tidak didukung.');
        }

        $ratio = $srcW > $maxWidth ? $maxWidth / $srcW : 1.0;
        $dstW = (int) round($srcW * $ratio);
        $dstH = (int) round($srcH * $ratio);

        $dst = imagecreatetruecolor($dstW, $dstH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefilledrectangle($dst, 0, 0, $dstW, $dstH, $transparent);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);

        // WebP: kompresi terbaik, alpha preserved, dukungan browser modern penuh.
        imagewebp($dst, $cachePath, $quality);

        imagedestroy($src);
        imagedestroy($dst);
    }
}
