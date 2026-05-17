<?php

namespace Tests\Feature;

use App\Models\Menu;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class MenuDetailModalTest extends TestCase
{
    public function test_menu_detail_modal_shows_komposisi_and_deskripsi_rasa_separately(): void
    {
        $menu = new Menu([
            'nama_menu' => 'Smoked Beef Pasta',
            'deskripsi' => 'Pasta fettuccine creamy dengan smoked beef gurih dan taburan parsley segar.',
            'komposisi' => 'Pasta Fettuchini // Smoked Beef // Milk // Parsley',
            'harga' => 42000,
            'gambar_menu' => 'smoked_beef_pasta.png',
            'jenis_menu' => 'Makanan',
            'kategori_makanan' => 'Tidak Pedas',
            'tipe_minuman' => null,
        ]);
        $menu->id_menu = 32;

        $html = Blade::render('<x-menu-detail-modal id="detail-menu-32" :menu="$menu" />', [
            'menu' => $menu,
        ]);

        $this->assertStringContainsString('Komposisi:', $html);
        $this->assertStringContainsString('Pasta Fettuchini // Smoked Beef // Milk // Parsley', $html);
        $this->assertStringContainsString('Deskripsi Rasa:', $html);
        $this->assertStringContainsString('Pasta fettuccine creamy dengan smoked beef gurih dan taburan parsley segar.', $html);
    }
}
