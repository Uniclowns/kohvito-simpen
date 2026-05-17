<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Seed data contoh untuk tabel menu + pivot menu_kategori.
     */
    public function run(): void
    {
        $menus = [
            // Kopi (id_kategori = 1)
            [
                'nama_menu'           => 'Espresso',
                'deskripsi'           => 'Espresso klasik dengan biji kopi pilihan, kuat dan pekat.',
                'harga'               => 18000,
                'gambar_menu'         => 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=400&h=300&fit=crop&q=80',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu'          => 'Minuman',
                'kategori_makanan'    => null,
                'tipe_minuman'        => 'Panas',
                '_kategori_ids'       => [1],
            ],
            [
                'nama_menu'           => 'Cappuccino',
                'deskripsi'           => 'Espresso dengan susu steamed dan foam lembut yang creamy.',
                'harga'               => 25000,
                'gambar_menu'         => 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=400&h=300&fit=crop&q=80',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu'          => 'Minuman',
                'kategori_makanan'    => null,
                'tipe_minuman'        => 'Panas',
                '_kategori_ids'       => [1],
            ],
            [
                'nama_menu'           => 'Caffe Latte',
                'deskripsi'           => 'Espresso dengan banyak susu steamed dan sedikit foam.',
                'harga'               => 28000,
                'gambar_menu'         => 'https://images.unsplash.com/photo-1561047029-3000c68339ca?w=400&h=300&fit=crop&q=80',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu'          => 'Minuman',
                'kategori_makanan'    => null,
                'tipe_minuman'        => 'Keduanya',
                '_kategori_ids'       => [1],
            ],
            [
                'nama_menu'           => 'Americano',
                'deskripsi'           => 'Espresso yang diencerkan dengan air panas, rasa kopi yang bersih.',
                'harga'               => 20000,
                'gambar_menu'         => 'https://images.unsplash.com/photo-1551030173-122aabc4489c?w=400&h=300&fit=crop&q=80',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu'          => 'Minuman',
                'kategori_makanan'    => null,
                'tipe_minuman'        => 'Keduanya',
                '_kategori_ids'       => [1],
            ],

            // Non-Kopi (id_kategori = 2)
            [
                'nama_menu'           => 'Matcha Latte',
                'deskripsi'           => 'Matcha premium Jepang dengan susu segar yang creamy.',
                'harga'               => 30000,
                'gambar_menu'         => 'https://images.unsplash.com/photo-1536256263959-f5b5a4612df3?w=400&h=300&fit=crop&q=80',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu'          => 'Minuman',
                'kategori_makanan'    => null,
                'tipe_minuman'        => 'Keduanya',
                '_kategori_ids'       => [2],
            ],
            [
                'nama_menu'           => 'Cokelat Panas',
                'deskripsi'           => 'Cokelat premium dengan susu hangat yang meleleh di mulut.',
                'harga'               => 25000,
                'gambar_menu'         => 'https://images.unsplash.com/photo-1542990253-a781e04bfd2f?w=400&h=300&fit=crop&q=80',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu'          => 'Minuman',
                'kategori_makanan'    => null,
                'tipe_minuman'        => 'Panas',
                '_kategori_ids'       => [2],
            ],
            [
                'nama_menu'           => 'Teh Tarik',
                'deskripsi'           => 'Teh susu khas dengan teknik tarik yang menghasilkan busa lembut.',
                'harga'               => 18000,
                'gambar_menu'         => 'https://images.unsplash.com/photo-1571934811356-5cc061b6821f?w=400&h=300&fit=crop&q=80',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu'          => 'Minuman',
                'kategori_makanan'    => null,
                'tipe_minuman'        => 'Panas',
                '_kategori_ids'       => [2],
            ],

            // Makanan Berat (id_kategori = 3)
            [
                'nama_menu'           => 'Nasi Goreng Spesial',
                'deskripsi'           => 'Nasi goreng dengan telur, ayam, dan sayuran segar pilihan.',
                'harga'               => 35000,
                'gambar_menu'         => 'https://images.unsplash.com/photo-1603360946369-dc9bb6258143?w=400&h=300&fit=crop&q=80',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu'          => 'Makanan',
                'kategori_makanan'    => 'Pedas',
                'tipe_minuman'        => null,
                '_kategori_ids'       => [3],
            ],
            [
                'nama_menu'           => 'Mie Goreng',
                'deskripsi'           => 'Mie goreng dengan bumbu khas dan topping telur serta sayuran.',
                'harga'               => 30000,
                'gambar_menu'         => 'https://images.unsplash.com/photo-1569050467447-ce54b3bbc37d?w=400&h=300&fit=crop&q=80',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu'          => 'Makanan',
                'kategori_makanan'    => 'Pedas',
                'tipe_minuman'        => null,
                '_kategori_ids'       => [3],
            ],
            [
                'nama_menu'           => 'Sandwich Club',
                'deskripsi'           => 'Sandwich berlapis ayam panggang, keju, selada, dan tomat segar.',
                'harga'               => 40000,
                'gambar_menu'         => 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=400&h=300&fit=crop&q=80',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu'          => 'Makanan',
                'kategori_makanan'    => 'Tidak Pedas',
                'tipe_minuman'        => null,
                '_kategori_ids'       => [3],
            ],

            // Snack (id_kategori = 4)
            [
                'nama_menu'           => 'French Fries',
                'deskripsi'           => 'Kentang goreng renyah dengan saus tomat dan mayones.',
                'harga'               => 20000,
                'gambar_menu'         => 'https://images.unsplash.com/photo-1573080496219-5e73f29bc294?w=400&h=300&fit=crop&q=80',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu'          => 'Makanan',
                'kategori_makanan'    => 'Tidak Pedas',
                'tipe_minuman'        => null,
                '_kategori_ids'       => [4],
            ],
            [
                'nama_menu'           => 'Roti Bakar',
                'deskripsi'           => 'Roti bakar dengan pilihan selai cokelat, stroberi, atau keju.',
                'harga'               => 18000,
                'gambar_menu'         => 'https://images.unsplash.com/photo-1484723091739-30a097e8f929?w=400&h=300&fit=crop&q=80',
                'status_ketersediaan' => 'Tidak Tersedia',
                'jenis_menu'          => 'Makanan',
                'kategori_makanan'    => 'Tidak Pedas',
                'tipe_minuman'        => null,
                '_kategori_ids'       => [4],
            ],
            [
                'nama_menu'           => 'Cheese Stick',
                'deskripsi'           => 'Stik keju renyah dengan lapisan tepung panir yang gurih.',
                'harga'               => 22000,
                'gambar_menu'         => 'https://images.unsplash.com/photo-1548340748-6fe2a3e20852?w=400&h=300&fit=crop&q=80',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu'          => 'Makanan',
                'kategori_makanan'    => 'Tidak Pedas',
                'tipe_minuman'        => null,
                '_kategori_ids'       => [4],
            ],

            // Minuman Segar (id_kategori = 5)
            [
                'nama_menu'           => 'Jus Jeruk',
                'deskripsi'           => 'Jus jeruk segar peras tanpa gula tambahan, kaya vitamin C.',
                'harga'               => 15000,
                'gambar_menu'         => 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=400&h=300&fit=crop&q=80',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu'          => 'Minuman',
                'kategori_makanan'    => null,
                'tipe_minuman'        => 'Dingin',
                '_kategori_ids'       => [5],
            ],
            [
                'nama_menu'           => 'Es Teh Manis',
                'deskripsi'           => 'Teh manis dingin yang menyegarkan dengan es batu pilihan.',
                'harga'               => 10000,
                'gambar_menu'         => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&h=300&fit=crop&q=80',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu'          => 'Minuman',
                'kategori_makanan'    => null,
                'tipe_minuman'        => 'Dingin',
                '_kategori_ids'       => [5],
            ],
            [
                'nama_menu'           => 'Lemon Squash',
                'deskripsi'           => 'Air soda segar dengan perasan lemon dan sedikit madu.',
                'harga'               => 18000,
                'gambar_menu'         => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=400&h=300&fit=crop&q=80',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu'          => 'Minuman',
                'kategori_makanan'    => null,
                'tipe_minuman'        => 'Dingin',
                '_kategori_ids'       => [5],
            ],
        ];

        foreach ($menus as $data) {
            $kategoriIds = $data['_kategori_ids'];
            unset($data['_kategori_ids']);

            $menu = Menu::create($data);
            $menu->kategoris()->attach($kategoriIds);
        }
    }
}
