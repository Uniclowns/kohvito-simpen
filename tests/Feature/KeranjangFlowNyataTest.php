<?php

namespace Tests\Feature;

use App\Models\KategoriMenu;
use App\Models\Meja;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeranjangFlowNyataTest extends TestCase
{
    use RefreshDatabase;

    private function menu(string $nama): Menu
    {
        KategoriMenu::firstOrCreate(['nama_kategori' => 'Test']);

        return Menu::factory()->create([
            'nama_menu' => $nama,
            'harga' => 20000,
            'status_ketersediaan' => 'Tersedia',
        ]);
    }

    private function scanDanTambah(string $noMeja, string $scope, int $idMenu): void
    {
        $this->get("/{$noMeja}")->assertOk();
        $this->post(route('konsumen.keranjang.tambah', ['noMeja' => $noMeja]), [
            'cart_scope' => $scope,
            'id_menu' => $idMenu,
            'jumlah' => 1,
        ])->assertRedirect(route('konsumen.keranjang', ['noMeja' => $noMeja, 's' => $scope]));
    }

    public function test_modal_detail_memakai_id_meja_request_bukan_session(): void
    {
        $m01 = Meja::create(['no_meja' => 'M01', 'qr_code' => 'M01']);
        $m03 = Meja::create(['no_meja' => 'M03', 'qr_code' => 'M03']);
        $menu = $this->menu('Angguro');

        session(['id_meja' => $m03->id_meja, 'id_meja_no' => 'M03']);

        $this->get("/M01/menu/{$menu->id_menu}/detail?partial=1&id_meja={$m01->id_meja}", [
            'X-Requested-With' => 'XMLHttpRequest',
        ])
            ->assertOk()
            ->assertSee('name="id_meja" value="'.$m01->id_meja.'"', false)
            ->assertSee('/M01/keranjang/tambah', false)
            ->assertSee('name="cart_scope"', false)
            ->assertDontSee('name="id_meja" value="'.$m03->id_meja.'"', false);
    }

    public function test_tiga_meja_via_scope_tab_tidak_bercampur(): void
    {
        Meja::create(['no_meja' => 'M01', 'qr_code' => 'M01']);
        Meja::create(['no_meja' => 'M02', 'qr_code' => 'M02']);
        Meja::create(['no_meja' => 'M03', 'qr_code' => 'M03']);

        $m01Menu = $this->menu('M01 Kopi');
        $m02Menu = $this->menu('Angguro');
        $m03Menu = $this->menu('M03 Teh');

        $this->scanDanTambah('M01', 'M01_A7F92K', $m01Menu->id_menu);
        $this->scanDanTambah('M02', 'M02_X8PL31', $m02Menu->id_menu);
        $this->scanDanTambah('M03', 'M03_QQQ111', $m03Menu->id_menu);

        $this->get('/M01/keranjang?s=M01_A7F92K')->assertOk()->assertSee('M01 Kopi', false);
        $this->get('/M02/keranjang?s=M02_X8PL31')->assertOk()->assertSee('Angguro', false);
        $this->get('/M03/keranjang?s=M03_QQQ111')->assertOk()->assertSee('M03 Teh', false);

        $this->assertArrayHasKey($m01Menu->id_menu, collect(session('keranjang.M01.M01_A7F92K'))->pluck('id_menu', 'id_menu')->all());
        $this->assertArrayHasKey($m02Menu->id_menu, collect(session('keranjang.M02.M02_X8PL31'))->pluck('id_menu', 'id_menu')->all());
        $this->assertArrayHasKey($m03Menu->id_menu, collect(session('keranjang.M03.M03_QQQ111'))->pluck('id_menu', 'id_menu')->all());
        $this->assertArrayNotHasKey($m02Menu->id_menu, collect(session('keranjang.M01.M01_A7F92K'))->pluck('id_menu', 'id_menu')->all());
    }

    public function test_global_keranjang_redirect_tidak_membaca_random_cart(): void
    {
        $this->get('/keranjang')->assertRedirect('/');
    }
}
