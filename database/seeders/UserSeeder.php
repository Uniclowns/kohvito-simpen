<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed data awal untuk tabel users.
     * Isi: 1 akun Admin default dan 1 akun Kasir default.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id_role'       => 1, // Admin
                'nama_lengkap'  => 'Administrator',
                'username'      => 'admin',
                'password'      => Hash::make('password'),
            ],
            [
                'id_role'       => 2, // Kasir
                'nama_lengkap'  => 'Kasir Default',
                'username'      => 'kasir',
                'password'      => Hash::make('password'),
            ],
        ]);
    }
}
