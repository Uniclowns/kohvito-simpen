<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriMenuSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['id_kategori' => 1, 'nama_kategori' => 'Kopi'],
            ['id_kategori' => 2, 'nama_kategori' => 'Non-Kopi'],
            ['id_kategori' => 3, 'nama_kategori' => 'Makanan Berat'],
            ['id_kategori' => 4, 'nama_kategori' => 'Snack'],
            ['id_kategori' => 5, 'nama_kategori' => 'Minuman Segar'],
        ];

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('kategori_menu')->upsert($chunk, ['id_kategori'], ['nama_kategori']);
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('kategori_menu', 'id_kategori'), COALESCE((SELECT MAX(id_kategori) FROM kategori_menu), 1), true)");
        }

    }
}
