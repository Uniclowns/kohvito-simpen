<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait ApiResponses
 * 
 * Trait ini berfungsi untuk menyediakan format respon JSON standar yang konsisten
 * pada seluruh endpoint RESTful API di aplikasi SIMPEN Kohvito.
 * Standar respon terbagi menjadi dua jalur utama: Respon Sukses dan Respon Error.
 *
 * @package App\Traits
 */
trait ApiResponses
{
    /**
     * Mengembalikan respon JSON terstandardisasi untuk kasus keberhasilan (Success Response).
     *
     * @param  mixed  $data  Data payload utama hasil query/pemrosesan (bisa berupa model, array, null)
     * @param  string|null  $message  Pesan deskriptif keberhasilan operasi (misal: "Login Berhasil")
     * @param  int  $code  Kode status HTTP respon (default: 200 OK)
     * @return \Illuminate\Http\JsonResponse  Objek respon JSON Laravel
     */
    protected function successResponse(mixed $data, ?string $message = null, int $code = 200): JsonResponse
    {
        // Menyusun wrapper respon JSON terstruktur secara baku
        return response()->json([
            'status'  => 'Success', // Status indikator sukses
            'message' => $message,   // Deskripsi informasi aksi
            'data'    => $data,      // Payload data yang diminta klien
        ], $code);
    }

    /**
     * Mengembalikan respon JSON terstandardisasi untuk kasus kegagalan/error (Error Response).
     *
     * @param  string  $message  Pesan deskripsi penyebab terjadinya error (misal: "Token Tidak Valid")
     * @param  int  $code  Kode status HTTP respon kegagalan (misal: 400 Bad Request, 401 Unauthorized, dsb.)
     * @return \Illuminate\Http\JsonResponse  Objek respon JSON Laravel dengan data di-set null
     */
    protected function errorResponse(string $message, int $code): JsonResponse
    {
        // Menyusun wrapper respon kegagalan JSON terstruktur secara baku
        return response()->json([
            'status'  => 'Error',   // Status indikator kegagalan
            'message' => $message,   // Deskripsi penyebab kegagalan
            'data'    => null,      // Data di-set kosong untuk menjaga konsistensi format klien
        ], $code);
    }
}
