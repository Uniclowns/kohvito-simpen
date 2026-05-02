<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriMenuSeeder extends Seeder
{
    /**
     * Seed data contoh untuk tabel kategori_menu.
     * Isi: Kopi, Non-Kopi, Makanan Berat, Snack, dll.
     */
    public function run(): void
    {
        DB::table('kategori_menu')->insert([
            ['nama_kategori' => 'Kopi'],
            ['nama_kategori' => 'Non-Kopi'],
            ['nama_kategori' => 'Makanan Berat'],
            ['nama_kategori' => 'Snack'],
            ['nama_kategori' => 'Minuman Segar'],
        ]);
    }
}
