<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['id_users' => 1, 'id_role' => 1, 'nama_lengkap' => 'Administrator', 'username' => 'admin', 'password' => '$2y$12$mgTpr1KVzKdvuBVrtNWCR.VWF4wiOr5nZIqCI1zqIbvcxKd77hRA2'],
            ['id_users' => 4, 'id_role' => 2, 'nama_lengkap' => 'Budi', 'username' => 'kasir123', 'password' => '$2y$12$dY5vQICDHLCFPMNVOr3MdOwv44pfpws5QvWoINWL4Km43IaxghxNm'],
            ['id_users' => 5, 'id_role' => 3, 'nama_lengkap' => 'Super Administrator', 'username' => 'superadmin', 'password' => '$2y$12$Kek3iF7pWwyOX0/r4hGgrev46R7iu3Y3zPnXV6SXhiWS.M6UneX4G'],
        ];

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('users')->upsert($chunk, ['id_users'], ['id_role', 'nama_lengkap', 'username', 'password']);
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('users', 'id_users'), COALESCE((SELECT MAX(id_users) FROM users), 1), true)");
        }

    }
}
