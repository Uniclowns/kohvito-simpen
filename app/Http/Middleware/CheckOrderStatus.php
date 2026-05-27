<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CheckOrderStatus
 * 
 * Middleware ini bertugas sebagai pelindung gerbang transaksi konsumen.
 * Berfungsi untuk memeriksa status operasional kafe (buka atau tutup) melalui
 * pencarian cepat pada sistem caching memori global (Laravel Cache).
 *
 * Jika status toko di-set 'tutup' oleh admin, middleware ini secara otomatis
 * memblokir pembuatan keranjang atau pemesanan menu dan mengalihkan konsumen
 * ke halaman khusus pemberitahuan tutup (`konsumen.order-tutup`).
 *
 * @package App\Http\Middleware
 */
class CheckOrderStatus
{
    /**
     * Menangani pemrosesan HTTP request yang masuk.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP Request dari klien
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next  Callback Closure untuk melanjutkan request berikutnya
     * @return \Symfony\Component\HttpFoundation\Response  Mengembalikan respon pengalihan (redirect) atau meloloskan request
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Mengambil status pemesanan global dari cache memori server.
        //    Jika data tidak ditemukan di cache, default status bernilai 'buka'.
        //    Penggunaan cache ini menghemat query database I/O secara repetitif.
        $statusPemesanan = Cache::get('order_status', 'buka');

        // 2. Mengecek apakah pemesanan telah dinonaktifkan (tutup).
        if ($statusPemesanan === 'tutup') {
            // 3. Jika tutup, segera hentikan proses request selanjutnya
            //    dan arahkan pengguna ke halaman informasi pemesanan ditutup.
            return redirect()->route('konsumen.order-tutup');
        }

        // 4. Jika status bernilai 'buka', loloskan request untuk diproses oleh controller.
        return $next($request);
    }
}
