<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthApiController
 * 
 * Controller RESTful API ini menangani otentikasi berbasis token (Token-based Authentication)
 * menggunakan Laravel Sanctum untuk klien luar (seperti SPA atau aplikasi mobile).
 * Meliputi pemrosesan login aman, pembentukan personal access token terenkripsi,
 * pengambilan info identitas user terotentikasi, serta pencabutan token saat log out.
 *
 * @package App\Http\Controllers\Api
 */
class AuthApiController extends Controller
{
    use ApiResponses; // Menyertakan trait untuk standarisasi format respon JSON

    /**
     * Otentikasi pengguna (Login API) dan membangkitkan token akses Sanctum baru.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP Request pembawa username dan sandi
     * @return \Illuminate\Http\JsonResponse Respon JSON standar sukses berisi token, atau 401 jika gagal
     */
    public function login(Request $request): JsonResponse
    {
        // 1. Validasi input username dan password wajib dikirimkan
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $kredensial = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        // 2. Lakukan percobaan autentikasi
        if (! Auth::attempt($kredensial)) {
            // 3. Jika gagal, kembalikan respon error 401 Unauthorized terstandarisasi
            return $this->errorResponse('Username atau password salah', 401);
        }

        // 4. Autentikasi sukses. Tarik objek user dan lakukan eager-load relasi role
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load('role');

        // 5. Bangkitkan token personal access token baru menggunakan Sanctum
        $token = $user->createToken('api-token')->plainTextToken;

        // 6. Kembalikan respon sukses berisi payload data user dan token
        return $this->successResponse([
            'user' => [
                'id_users'     => $user->id_users,
                'nama_lengkap' => $user->nama_lengkap,
                'username'     => $user->username,
                'role'         => $user->role?->nama_role,
            ],
            'token' => $token,
        ], 'Login berhasil');
    }

    /**
     * Mengeluarkan pengguna dari sesi API (Logout) dengan menghapus token akses aktif saat ini.
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request pembawa token otentikasi
     * @return \Illuminate\Http\JsonResponse Respon sukses terstandarisasi
     */
    public function logout(Request $request): JsonResponse
    {
        // 1. Hapus (cabut) token akses aktif yang sedang digunakan untuk request ini
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout berhasil');
    }

    /**
     * Memperoleh detail informasi profil pengguna yang sedang login saat ini (Check Session API).
     *
     * @param  \Illuminate\Http\Request  $request  Objek HTTP request terotentikasi
     * @return \Illuminate\Http\JsonResponse Respon sukses berisi profil pengguna
     */
    public function me(Request $request): JsonResponse
    {
        // 1. Tarik user terotentikasi dan muat detail relasi perannya
        /** @var \App\Models\User $user */
        $user = $request->user();
        $user->load('role');

        return $this->successResponse([
            'id_users'     => $user->id_users,
            'nama_lengkap' => $user->nama_lengkap,
            'username'     => $user->username,
            'role'         => $user->role?->nama_role,
        ]);
    }
}
