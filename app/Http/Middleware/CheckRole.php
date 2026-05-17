<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Middleware untuk memvalidasi role user setelah login.
     *
     * Contoh penggunaan di route:
     *   ->middleware('role:admin')
     *   ->middleware('role:kasir')
     *
     * @param  string  $role  Nama role yang diizinkan (admin/kasir)
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user || ! $user->role) {
            abort(403, 'Akses ditolak.');
        }

        if (strtolower($user->role->nama_role) !== strtolower($role)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
