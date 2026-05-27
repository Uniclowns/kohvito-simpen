<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CheckRole
 * 
 * Middleware keamanan multi-user untuk membatasi hak akses halaman dashboard.
 * Memeriksa apakah pengguna yang terautentikasi memiliki peran (role) yang diizinkan
 * untuk mengakses suatu grup rute.
 *
 * @package App\Http\Middleware
 */
class CheckRole
{
    /**
     * Menangani pemrosesan HTTP request yang masuk.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP Request saat ini
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next  Callback Closure penerus request
     * @param  string  $role  Nama role yang diizinkan (admin / kasir)
     * @return \Symfony\Component\HttpFoundation\Response  Mengembalikan abort(403) jika ditolak, atau meloloskan request
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Mengambil data user yang sedang login saat ini dari request autentikasi
        $user = $request->user();

        // 2. Memastikan user terautentikasi dan objek relasi `role`-nya terdefinisi dengan benar
        if (! $user || ! $user->role) {
            // 3. Jika tidak terotentikasi atau relasi kosong, gagalkan akses dengan status 403 Forbidden
            abort(403, 'Akses ditolak.');
        }

        // 4. Membandingkan nama role pengguna saat ini dengan kriteria parameter rute.
        //    Gunakan standardisasi huruf kecil (strtolower) agar perbandingan bersifat case-insensitive
        //    serta menghindari kesalahan pengetikan karakter kapital di database.
        $rolePengguna = strtolower($user->role->nama_role);
        $roleSyarat   = strtolower($role);

        if ($rolePengguna !== $roleSyarat) {
            // 5. Jika peran tidak sesuai dengan syarat rute, batalkan request dengan respon 403 Forbidden
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // 6. Jika semua validasi lolos, ijinkan user melanjutkan ke controller rute yang dituju
        return $next($request);
    }
}
