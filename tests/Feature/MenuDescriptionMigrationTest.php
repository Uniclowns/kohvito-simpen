<?php

namespace Tests\Feature;

use App\Models\KategoriMenu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MenuDescriptionMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_deskripsi_is_moved_to_komposisi_then_replaced_with_flavor_description(): void
    {
        $kategori = KategoriMenu::create(['nama_kategori' => 'Kopi']);

        DB::table('menu')->insert([
            [
                'id_menu' => 1,
                'id_kategori' => $kategori->id_kategori,
                'nama_menu' => 'V Loco',
                'deskripsi' => 'Espresso // Milk // Signature Base',
                'harga' => 30000,
                'gambar_menu' => 'vloco.png',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu' => 'Minuman',
                'kategori_makanan' => null,
                'tipe_minuman' => 'Dingin',
                'komposisi' => null,
                'stock' => 100,
            ],
            [
                'id_menu' => 4,
                'id_kategori' => $kategori->id_kategori,
                'nama_menu' => 'Angguro',
                'deskripsi' => 'Original "Kopi Anggur" - The most best seller at Kohvito since 2023',
                'harga' => 28000,
                'gambar_menu' => 'angguro.png',
                'status_ketersediaan' => 'Tersedia',
                'jenis_menu' => 'Minuman',
                'kategori_makanan' => null,
                'tipe_minuman' => 'Dingin',
                'komposisi' => null,
                'stock' => 100,
            ],
        ]);

        $migration = require database_path('migrations/2026_05_15_000001_swap_deskripsi_to_komposisi_and_generate_flavor.php');
        $migration->up();

        $vLoco = DB::table('menu')->where('id_menu', 1)->first();
        $angguro = DB::table('menu')->where('id_menu', 4)->first();

        $this->assertSame('Espresso // Milk // Signature Base', $vLoco->komposisi);
        $this->assertSame('Kopi susu signature creamy dengan espresso bold dipadu manis lembut khas Kohvito.', $vLoco->deskripsi);
        $this->assertSame('Espresso // Wine Reduction // Sweet Base', $angguro->komposisi);
        $this->assertSame('Kopi anggur signature: aroma anggur segar berpadu kopi pekat, menyegarkan dan ringan di tenggorokan.', $angguro->deskripsi);

        $migration->down();

        $vLocoAfterRollback = DB::table('menu')->where('id_menu', 1)->first();
        $this->assertSame('Espresso // Milk // Signature Base', $vLocoAfterRollback->deskripsi);
        $this->assertNull($vLocoAfterRollback->komposisi);
    }
}
