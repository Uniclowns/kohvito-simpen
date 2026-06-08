<?php

namespace Tests\Feature;

use App\Models\Meja;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression: isi keranjang tidak boleh hilang saat konsumen berpindah dari
 * tab Keranjang ke tab Menu lalu kembali.
 *
 * Tab "Menu" menaut ke route('konsumen.beranda', $noMeja) yang TIDAK membawa
 * token ?u={scope}. Sebelum perbaikan, BerandaKonsumenController@index
 * menganggap setiap kunjungan tanpa token sebagai sesi baru dan menghapus
 * keranjang (session 'keranjang' di-forget). Sesi yang sudah valid untuk meja
 * yang sama harus dipertahankan.
 */
class KeranjangPersistsOnMenuNavigationTest extends TestCase
{
    use RefreshDatabase;

    private function sampleCart(): array
    {
        return [
            '1:abc' => [
                'id_menu'        => 1,
                'nama_menu'      => 'Kopi Susu',
                'harga'          => 20000,
                'harga_dasar'    => 20000,
                'harga_tambahan' => 0,
                'jumlah'         => 2,
                'catatan'        => null,
                'subtotal'       => 40000,
            ],
        ];
    }

    public function test_cart_survives_navigating_to_menu_tab_without_scope_token(): void
    {
        $meja = Meja::create(['no_meja' => 'M01', 'qr_code' => 'qr-m01']);
        $cart = $this->sampleCart();

        // Sesi pemesanan yang sudah berjalan: scope valid untuk meja ini + ada isi keranjang.
        $response = $this->withSession([
            'konsumen_scope_id' => 'scope123',
            'id_meja'           => $meja->id_meja,
            'id_meja_no'        => 'M01',
            'keranjang'         => $cart,
        ])->get('/M01'); // tautan tab "Menu" — tanpa ?u=

        // Harus merender katalog langsung (bukan redirect bikin scope baru) ...
        $response->assertOk();
        // ... dan yang terpenting keranjang tetap utuh.
        $response->assertSessionHas('keranjang', $cart);
    }

    public function test_switching_physical_table_still_resets_cart(): void
    {
        Meja::create(['no_meja' => 'M01', 'qr_code' => 'qr-m01']);
        Meja::create(['no_meja' => 'M02', 'qr_code' => 'qr-m02']);

        // Sesi aktif di meja M01, lalu scan QR meja fisik berbeda (M02).
        $response = $this->withSession([
            'konsumen_scope_id' => 'scope123',
            'id_meja'           => 1,
            'id_meja_no'        => 'M01',
            'keranjang'         => $this->sampleCart(),
        ])->get('/M02');

        // Pindah meja = sesi belanja baru: redirect membawa scope baru & keranjang dibersihkan.
        $response->assertRedirect();
        $response->assertSessionMissing('keranjang');
    }
}
