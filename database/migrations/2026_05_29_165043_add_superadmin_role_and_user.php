<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Data migration: Seed peran "Super Admin" beserta akun default-nya.
 *
 * Super Admin adalah god-mode user yang dapat mengakses semua panel
 * (Admin, Kasir, Konsumen). Penggunaan migration—bukan seeder—agar
 * idempotent & otomatis ter-apply di environment lain via `php artisan migrate`.
 *
 * Idempotent: cek eksistensi sebelum insert agar aman dijalankan ulang.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Insert role "Super Admin" jika belum ada
        $existingRole = DB::table('role')->where('nama_role', 'Super Admin')->first();
        $roleId = $existingRole->id_role ?? null;

        if (! $roleId) {
            $roleId = DB::table('role')->insertGetId([
                'nama_role' => 'Super Admin',
            ]);
        }

        // 2. Insert user superadmin default jika username belum dipakai
        $existingUser = DB::table('users')->where('username', 'superadmin')->first();

        if (! $existingUser) {
            DB::table('users')->insert([
                'id_role'      => $roleId,
                'nama_lengkap' => 'Super Administrator',
                'username'     => 'superadmin',
                'password'     => Hash::make('superadmin123'),
            ]);
        }
    }

    public function down(): void
    {
        // Hapus user superadmin terlebih dahulu (FK reference ke roles)
        DB::table('users')->where('username', 'superadmin')->delete();
        DB::table('role')->where('nama_role', 'Super Admin')->delete();
    }
};
