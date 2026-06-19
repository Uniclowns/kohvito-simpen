<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Urutan pemanggilan seeder mengikuti dependensi tabel:
     * Role → User → Meja → KategoriMenu → Menu → MenuKategori → Pesanan → DetailPesanan
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            MejaSeeder::class,
            KategoriMenuSeeder::class,
            MenuSeeder::class,
            MenuKategoriSeeder::class,
            PesananSeeder::class,
            DetailPesananSeeder::class,
        ]);
    }
}
