<?php

namespace Tests\Feature;

use App\Models\KategoriMenu;
use App\Models\Meja;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KonsumenMenuFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_konsumen_menu_filter_query_sets_initial_visible_cards(): void
    {
        $meja = $this->createMeja();
        $coffee = KategoriMenu::create(['nama_kategori' => 'Kopi']);
        $food = KategoriMenu::create(['nama_kategori' => 'Makanan']);

        $latte = $this->createMenu('Latte Aren', 'Kopi susu gula aren', 'Minuman');
        $nasi = $this->createMenu('Nasi Goreng', 'Nasi goreng pedas', 'Makanan');
        $latte->kategoris()->attach($coffee->id_kategori);
        $nasi->kategoris()->attach($food->id_kategori);

        $response = $this
            ->withSession([
                'konsumen_scope_id' => 'abc12345',
                'id_meja' => $meja->id_meja,
                'id_meja_no' => $meja->no_meja,
            ])
            ->get(route('konsumen.beranda', [
                'noMeja' => $meja->no_meja,
                'u' => 'abc12345',
                'kategori' => $coffee->id_kategori,
                'search' => 'LATTE',
            ]));

        $html = $response->assertOk()->getContent();

        $this->assertStringContainsString('value="LATTE"', $html);
        $this->assertStringNotContainsString(' hidden', $this->articleTagFor($html, 'latte aren'));
        $this->assertStringContainsString(' hidden', $this->articleTagFor($html, 'nasi goreng'));
    }

    public function test_konsumen_menu_grid_renders_multi_category_menu_once(): void
    {
        $meja = $this->createMeja();
        $coffee = KategoriMenu::create(['nama_kategori' => 'Kopi']);
        $signature = KategoriMenu::create(['nama_kategori' => 'Signature']);

        $latte = $this->createMenu('Latte Aren', 'Kopi susu gula aren', 'Minuman');
        $latte->kategoris()->attach([$coffee->id_kategori, $signature->id_kategori]);

        $response = $this
            ->withSession([
                'konsumen_scope_id' => 'abc12345',
                'id_meja' => $meja->id_meja,
                'id_meja_no' => $meja->no_meja,
            ])
            ->get(route('konsumen.beranda', [
                'noMeja' => $meja->no_meja,
                'u' => 'abc12345',
            ]));

        $html = $response->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'data-nama="latte aren"'));
        $this->assertStringContainsString(
            'data-kategori="'.$coffee->id_kategori.','.$signature->id_kategori.'"',
            $html
        );
    }

    public function test_konsumen_menu_data_filters_by_category_and_search_case_insensitive(): void
    {
        $coffee = KategoriMenu::create(['nama_kategori' => 'Kopi']);
        $food = KategoriMenu::create(['nama_kategori' => 'Makanan']);

        $latte = $this->createMenu('Latte Aren', 'Kopi susu gula aren', 'Minuman');
        $nasi = $this->createMenu('Nasi Goreng', 'Nasi goreng pedas', 'Makanan');
        $latte->kategoris()->attach($coffee->id_kategori);
        $nasi->kategoris()->attach($food->id_kategori);

        $response = $this->getJson(route('konsumen.menu.data', [
            'id_kategori' => $coffee->id_kategori,
            'search' => 'LATTE',
        ]));

        $response
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['nama_menu' => 'Latte Aren'])
            ->assertJsonMissing(['nama_menu' => 'Nasi Goreng']);
    }

    private function createMeja(): Meja
    {
        return Meja::create([
            'no_meja' => 'M01',
            'qr_code' => 'qrcodes/meja-01.png',
        ]);
    }

    private function createMenu(string $nama, string $deskripsi, string $jenis): Menu
    {
        return Menu::create([
            'nama_menu' => $nama,
            'deskripsi' => $deskripsi,
            'komposisi' => $deskripsi,
            'harga' => 25000,
            'stock' => 10,
            'gambar_menu' => 'latte.png',
            'status_ketersediaan' => 'Tersedia',
            'jenis_menu' => $jenis,
            'kategori_makanan' => $jenis === 'Makanan' ? 'Pedas' : null,
            'tipe_minuman' => $jenis === 'Minuman' ? 'Dingin' : null,
        ]);
    }

    private function articleTagFor(string $html, string $dataName): string
    {
        $pattern = '/<article\b(?=[^>]*data-nama="'.preg_quote($dataName, '/').'")[^>]*>/s';

        $this->assertMatchesRegularExpression($pattern, $html);
        preg_match($pattern, $html, $matches);

        return $matches[0];
    }
}
