<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthApiController extends Controller
{
    /**
     * Login user dan buat Sanctum token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Username atau password salah',
                'data'    => null,
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $user->load('role');
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil',
            'data'    => [
                'user' => [
                    'id_users'     => $user->id_users,
                    'nama_lengkap' => $user->nama_lengkap,
                    'username'     => $user->username,
                    'role'         => $user->role?->nama_role,
                ],
                'token' => $token,
            ],
        ]);
    }

    /**
     * Logout user dan cabut token saat ini.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout berhasil',
            'data'    => null,
        ]);
    }

    /**
     * Kembalikan info user yang sedang login.
     */
    public function me(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $user->load('role');

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id_users'     => $user->id_users,
                'nama_lengkap' => $user->nama_lengkap,
                'username'     => $user->username,
                'role'         => $user->role?->nama_role,
            ],
        ]);
    }
}
