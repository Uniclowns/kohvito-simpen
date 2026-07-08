<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuKategoriSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['id_menu' => 1, 'id_kategori' => 1],
            ['id_menu' => 4, 'id_kategori' => 1],
            ['id_menu' => 6, 'id_kategori' => 1],
            ['id_menu' => 7, 'id_kategori' => 1],
            ['id_menu' => 8, 'id_kategori' => 1],
            ['id_menu' => 9, 'id_kategori' => 1],
            ['id_menu' => 10, 'id_kategori' => 1],
            ['id_menu' => 11, 'id_kategori' => 1],
            ['id_menu' => 12, 'id_kategori' => 1],
            ['id_menu' => 13, 'id_kategori' => 1],
            ['id_menu' => 14, 'id_kategori' => 1],
            ['id_menu' => 15, 'id_kategori' => 1],
            ['id_menu' => 16, 'id_kategori' => 1],
            ['id_menu' => 3, 'id_kategori' => 2],
            ['id_menu' => 21, 'id_kategori' => 2],
            ['id_menu' => 22, 'id_kategori' => 2],
            ['id_menu' => 23, 'id_kategori' => 2],
            ['id_menu' => 24, 'id_kategori' => 2],
            ['id_menu' => 25, 'id_kategori' => 3],
            ['id_menu' => 26, 'id_kategori' => 3],
            ['id_menu' => 27, 'id_kategori' => 3],
            ['id_menu' => 28, 'id_kategori' => 3],
            ['id_menu' => 29, 'id_kategori' => 3],
            ['id_menu' => 30, 'id_kategori' => 3],
            ['id_menu' => 31, 'id_kategori' => 3],
            ['id_menu' => 32, 'id_kategori' => 3],
            ['id_menu' => 33, 'id_kategori' => 3],
            ['id_menu' => 34, 'id_kategori' => 4],
            ['id_menu' => 35, 'id_kategori' => 4],
            ['id_menu' => 36, 'id_kategori' => 4],
            ['id_menu' => 37, 'id_kategori' => 4],
            ['id_menu' => 38, 'id_kategori' => 4],
            ['id_menu' => 39, 'id_kategori' => 4],
            ['id_menu' => 5, 'id_kategori' => 5],
            ['id_menu' => 17, 'id_kategori' => 5],
            ['id_menu' => 18, 'id_kategori' => 5],
            ['id_menu' => 19, 'id_kategori' => 5],
            ['id_menu' => 20, 'id_kategori' => 5],
        ];

        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table('menu_kategori')->insertOrIgnore($chunk);
        }

    }
}
