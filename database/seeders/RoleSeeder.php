<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['id_role' => 1, 'nama_role' => 'Admin'],
            ['id_role' => 2, 'nama_role' => 'Kasir'],
            ['id_role' => 3, 'nama_role' => 'Super Admin'],
        ];

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('role')->upsert($chunk, ['id_role'], ['nama_role']);
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('role', 'id_role'), COALESCE((SELECT MAX(id_role) FROM role), 1), true)");
        }

    }
}
