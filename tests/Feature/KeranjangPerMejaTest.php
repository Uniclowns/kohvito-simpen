<?php

namespace Tests\Feature;

use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menguji perbaikan dua bug utama aplikasi konsumen Kohvito:
 *
 *  BUG 1 — Keranjang antar-meja saling menimpa (session keranjang global).
 *  BUG 2 — Riwayat pesanan hilang saat session expired (tidak ada identitas permanen).
 *
 * Strategi solusi yang diuji:
 *  - Keranjang per-meja: session key "keranjang.{id_meja}".
 *  - tracking_code unik per pesanan untuk pelacakan device-independent.
 *  - Cookie riwayat 'riwayat_pesanan_kohvito' sebagai backup otomatis.
 *  - Checkout hanya menghapus keranjang meja aktif (bukan flush global).
 */
class KeranjangPerMejaTest extends TestCase
{
    use RefreshDatabase;

    private function buatMeja(string $noMeja): Meja
    {
        return Meja::create(['no_meja' => $noMeja, 'qr_code' => $noMeja]);
    }

    private function buatMenu(int $harga = 20000): Menu
    {
        return Menu::factory()->create([
            'harga' => $harga,
            'status_ketersediaan' => 'Tersedia',
        ]);
    }

    /**
     * Set konteks meja aktif seolah konsumen baru scan QR meja tsb.
     */
    private function aktifkanMeja(Meja $meja): void
    {
        session([
            'id_meja' => $meja->id_meja,
            'id_meja_no' => $meja->no_meja,

        ]);
    }

    // ---------------------------------------------------------------------
    // BUG 1: Isolasi keranjang per-meja
    // ---------------------------------------------------------------------

    public function test_keranjang_dua_meja_tidak_saling_menimpa(): void
    {
        $m01 = $this->buatMeja('M01');
        $m02 = $this->buatMeja('M02');
        $menuA = $this->buatMenu(20000);
        $menuB = $this->buatMenu(15000);

        // Meja M01: tambah menu A
        $this->aktifkanMeja($m01);
        $this->post(route('konsumen.keranjang.tambah', ['noMeja' => session('id_meja_no')]), [
            'cart_scope' => 'M01_SCOPE1',
            'id_menu' => $menuA->id_menu,
            'jumlah' => 2,
        ])->assertRedirect();

        // Verifikasi keranjang tersimpan di key per-meja M01
        $keranjangM01 = session('keranjang.'.$m01->no_meja.'.'.'M01_SCOPE1');
        $this->assertNotEmpty($keranjangM01);
        $this->assertSame(2, array_values($keranjangM01)[0]['jumlah']);

        // Sekarang konsumen (browser sama) berpindah ke M02, tambah menu B
        $this->aktifkanMeja($m02);
        $this->post(route('konsumen.keranjang.tambah', ['noMeja' => session('id_meja_no')]), [
            'cart_scope' => 'M02_SCOPE1',
            'id_menu' => $menuB->id_menu,
            'jumlah' => 1,
        ])->assertRedirect();

        // KUNCI BUG 1: keranjang M01 HARUS tetap utuh setelah operasi di M02
        $keranjangM01After = session('keranjang.'.$m01->no_meja.'.'.'M01_SCOPE1');
        $keranjangM02 = session('keranjang.'.$m02->no_meja.'.'.'M02_SCOPE1');

        $this->assertNotEmpty($keranjangM01After, 'Keranjang M01 hilang setelah menambah item di M02!');
        $this->assertSame($menuA->id_menu, array_values($keranjangM01After)[0]['id_menu']);
        $this->assertSame($menuB->id_menu, array_values($keranjangM02)[0]['id_menu']);

        // Keranjang tidak bercampur
        $this->assertCount(1, $keranjangM01After);
        $this->assertCount(1, $keranjangM02);
    }

    public function test_membuka_beranda_meja_lain_tidak_menghapus_keranjang(): void
    {
        $m01 = $this->buatMeja('M01');
        $m02 = $this->buatMeja('M02');
        $menuA = $this->buatMenu();

        // Isi keranjang M01
        $this->aktifkanMeja($m01);
        $this->post(route('konsumen.keranjang.tambah', ['noMeja' => session('id_meja_no')]), [
            'cart_scope' => 'M01_SCOPE1',
            'id_menu' => $menuA->id_menu,
            'jumlah' => 1,
        ]);
        $this->assertNotEmpty(session('keranjang.'.$m01->no_meja.'.'.'M01_SCOPE1'));

        // Buka beranda M02 (simulasi scan QR meja lain / tab baru).
        // Cukup pastikan request tidak error server (200 atau redirect wajar).
        $status = $this->get(route('konsumen.beranda', ['noMeja' => 'M02']))->getStatusCode();
        $this->assertContains($status, [200, 302], 'Beranda M02 mengembalikan error server.');

        // KUNCI: keranjang M01 tetap ada — beranda TIDAK boleh flush keranjang global
        $this->assertNotEmpty(
            session('keranjang.'.$m01->no_meja.'.'.'M01_SCOPE1'),
            'Membuka beranda M02 menghapus keranjang M01 (regresi flush global)!'
        );
    }

    // ---------------------------------------------------------------------
    // Checkout: hanya hapus keranjang meja aktif + tracking_code + cookie
    // ---------------------------------------------------------------------

    public function test_checkout_hanya_menghapus_keranjang_meja_aktif_dan_membuat_tracking_code(): void
    {
        $m01 = $this->buatMeja('M01');
        $m02 = $this->buatMeja('M02');
        $menuA = $this->buatMenu(20000);
        $menuB = $this->buatMenu(15000);

        // Isi kedua keranjang lebih dulu
        $this->aktifkanMeja($m01);
        $this->post(route('konsumen.keranjang.tambah', ['noMeja' => session('id_meja_no')]), ['cart_scope' => 'M01_SCOPE1', 'id_menu' => $menuA->id_menu, 'jumlah' => 2]);

        $this->aktifkanMeja($m02);
        $this->post(route('konsumen.keranjang.tambah', ['noMeja' => session('id_meja_no')]), ['cart_scope' => 'M02_SCOPE1', 'id_menu' => $menuB->id_menu, 'jumlah' => 1]);

        // Checkout meja M01 (aktifkan lagi M01)
        $this->aktifkanMeja($m01);
        $response = $this->post(route('konsumen.keranjang.pesan', ['noMeja' => session('id_meja_no')]), [
            'cart_scope' => 'M01_SCOPE1',
            'nama_konsumen' => 'Budi',
        ]);
        $response->assertRedirect();

        // Pesanan tersimpan dengan tracking_code
        $pesanan = Pesanan::where('id_meja', $m01->id_meja)->first();
        $this->assertNotNull($pesanan, 'Pesanan M01 tidak tersimpan.');
        $this->assertNotEmpty($pesanan->tracking_code);
        $this->assertStringStartsWith('KV-', $pesanan->tracking_code);

        // Keranjang M01 kosong setelah checkout
        $this->assertEmpty(session('keranjang.'.$m01->no_meja.'.'.'M01_SCOPE1'), 'Keranjang M01 belum dibersihkan setelah checkout.');

        // KUNCI: keranjang M02 TIDAK ikut terhapus
        $this->assertNotEmpty(
            session('keranjang.'.$m02->no_meja.'.'.'M02_SCOPE1'),
            'Checkout M01 ikut menghapus keranjang M02 (flush global masih terjadi)!'
        );

        // Cookie riwayat backup ter-set berisi tracking_code
        $response->assertCookie('riwayat_pesanan_kohvito');
    }

    // ---------------------------------------------------------------------
    // BUG 2: Pelacakan via tracking_code + restore cookie
    // ---------------------------------------------------------------------

    public function test_pelacakan_pesanan_via_tracking_code(): void
    {
        $meja = $this->buatMeja('M01');
        $pesanan = Pesanan::create([
            'no_pesanan' => 'PS-LACAK-1',
            'tracking_code' => 'KV-ABCDE',
            'id_meja' => $meja->id_meja,
            'nama_konsumen' => 'Siti',
            'total_harga' => 22200,
            'status_pembayaran' => 'menunggu',
            'status_pesanan' => 'menunggu konfirmasi',
        ]);

        // Konsumen scan QR ulang di device baru (tanpa session lama), lacak via kode
        $response = $this->post(route('konsumen.lacak.cari'), [
            'tracking_code' => 'kv-abcde', // sengaja huruf kecil → harus dinormalisasi
        ]);

        $response->assertOk();
        $response->assertSee('KV-ABCDE');
        $response->assertSee('Siti');

        // Kode yang ketemu ikut disimpan ke cookie backup
        $response->assertCookie('riwayat_pesanan_kohvito');
    }

    public function test_tracking_code_tidak_ditemukan_memberi_pesan_error(): void
    {
        $this->buatMeja('M01');

        $this->post(route('konsumen.lacak.cari'), [
            'tracking_code' => 'KV-XXXXX',
        ])->assertRedirect(route('konsumen.lacak.form'))
            ->assertSessionHas('error');
    }

    public function test_riwayat_pesanan_pulih_dari_cookie_saat_session_kosong(): void
    {
        $meja = $this->buatMeja('M01');
        Pesanan::create([
            'no_pesanan' => 'PS-COOKIE-1',
            'tracking_code' => 'KV-CKIE1',
            'id_meja' => $meja->id_meja,
            'nama_konsumen' => 'Andi',
            'total_harga' => 20000,
            'status_pembayaran' => 'menunggu',
            'status_pesanan' => 'menunggu konfirmasi',
        ]);

        // Simulasikan: session riwayat kosong (expired), tapi cookie backup masih ada.
        $cookieValue = json_encode(['KV-CKIE1']);

        $response = $this->withUnencryptedCookie('riwayat_pesanan_kohvito', $cookieValue)
            ->get(route('konsumen.pesanan', ['noMeja' => 'M01']));

        $response->assertOk();
        // Pesanan dari cookie harus muncul di daftar riwayat
        $response->assertSee('KV-CKIE1');
    }

    public function test_tracking_code_unik_tergenerasi_dengan_prefix_benar(): void
    {
        $code = Pesanan::generateTrackingCode();

        $this->assertStringStartsWith('KV-', $code);
        $this->assertMatchesRegularExpression('/^KV-[A-Z0-9]{5}$/', $code);
    }
}
