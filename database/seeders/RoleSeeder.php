<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Seed data awal untuk tabel role.
     * Isi: Admin dan Kasir.
     */
    public function run(): void
    {
        DB::table('role')->insert([
            ['nama_role' => 'Admin'],
            ['nama_role' => 'Kasir'],
        ]);
    }
}
