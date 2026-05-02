<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Seed data contoh untuk tabel menu.
     * Isi: menu contoh per kategori.
     */
    public function run(): void
    {
        $menus = [
            // Kopi (id_kategori = 1)
            [
                'id_kategori'         => 1,
                'nama_menu'           => 'Espresso',
                'deskripsi'           => 'Espresso klasik dengan biji kopi pilihan.',
                'harga'               => 18000,
                'gambar_menu'         => 'menu/default.png',
                'status_ketersediaan' => 'Tersedia',
            ],
            [
                'id_kategori'         => 1,
                'nama_menu'           => 'Cappuccino',
                'deskripsi'           => 'Espresso dengan susu steamed dan foam lembut.',
                'harga'               => 25000,
                'gambar_menu'         => 'menu/default.png',
                'status_ketersediaan' => 'Tersedia',
            ],
            [
                'id_kategori'         => 1,
                'nama_menu'           => 'Caffe Latte',
                'deskripsi'           => 'Espresso dengan banyak susu steamed.',
                'harga'               => 28000,
                'gambar_menu'         => 'menu/default.png',
                'status_ketersediaan' => 'Tersedia',
            ],

            // Non-Kopi (id_kategori = 2)
            [
                'id_kategori'         => 2,
                'nama_menu'           => 'Matcha Latte',
                'deskripsi'           => 'Matcha premium Jepang dengan susu segar.',
                'harga'               => 30000,
                'gambar_menu'         => 'menu/default.png',
                'status_ketersediaan' => 'Tersedia',
            ],
            [
                'id_kategori'         => 2,
                'nama_menu'           => 'Cokelat Panas',
                'deskripsi'           => 'Cokelat premium dengan susu hangat.',
                'harga'               => 25000,
                'gambar_menu'         => 'menu/default.png',
                'status_ketersediaan' => 'Tersedia',
            ],

            // Makanan Berat (id_kategori = 3)
            [
                'id_kategori'         => 3,
                'nama_menu'           => 'Nasi Goreng Spesial',
                'deskripsi'           => 'Nasi goreng dengan telur, ayam, dan sayuran segar.',
                'harga'               => 35000,
                'gambar_menu'         => 'menu/default.png',
                'status_ketersediaan' => 'Tersedia',
            ],
            [
                'id_kategori'         => 3,
                'nama_menu'           => 'Mie Goreng',
                'deskripsi'           => 'Mie goreng dengan bumbu khas dan topping lengkap.',
                'harga'               => 30000,
                'gambar_menu'         => 'menu/default.png',
                'status_ketersediaan' => 'Tersedia',
            ],

            // Snack (id_kategori = 4)
            [
                'id_kategori'         => 4,
                'nama_menu'           => 'French Fries',
                'deskripsi'           => 'Kentang goreng renyah dengan saus pilihan.',
                'harga'               => 20000,
                'gambar_menu'         => 'menu/default.png',
                'status_ketersediaan' => 'Tersedia',
            ],
            [
                'id_kategori'         => 4,
                'nama_menu'           => 'Roti Bakar',
                'deskripsi'           => 'Roti bakar dengan selai dan keju.',
                'harga'               => 18000,
                'gambar_menu'         => 'menu/default.png',
                'status_ketersediaan' => 'Tidak Tersedia',
            ],

            // Minuman Segar (id_kategori = 5)
            [
                'id_kategori'         => 5,
                'nama_menu'           => 'Jus Jeruk',
                'deskripsi'           => 'Jus jeruk segar tanpa gula tambahan.',
                'harga'               => 15000,
                'gambar_menu'         => 'menu/default.png',
                'status_ketersediaan' => 'Tersedia',
            ],
            [
                'id_kategori'         => 5,
                'nama_menu'           => 'Es Teh Manis',
                'deskripsi'           => 'Teh manis dingin yang menyegarkan.',
                'harga'               => 10000,
                'gambar_menu'         => 'menu/default.png',
                'status_ketersediaan' => 'Tersedia',
            ],
        ];

        DB::table('menu')->insert($menus);
    }
}
