<?php

namespace Tests\Feature;

use App\Models\Meja;
use App\Models\Pesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Menguji pemulihan riwayat pesanan lintas-browser TANPA login
 * via tracking_code di halaman /lacak-pesanan.
 *
 * tracking_code adalah satu-satunya kunci publik; kode berformat salah
 * ditolak sebelum menyentuh database.
 */
class RiwayatLintasBrowserTest extends TestCase
{
    use RefreshDatabase;

    private function buatPesanan(string $trackingCode): Pesanan
    {
        $meja = Meja::firstOrCreate(['no_meja' => 'M01'], ['qr_code' => 'M01']);

        return Pesanan::create([
            'no_pesanan' => 'PS-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4)),
            'tracking_code' => $trackingCode,
            'id_meja' => $meja->id_meja,
            'nama_konsumen' => 'Tester',
            'total_harga' => 10000,
            'status_pembayaran' => 'lunas',
            'status_pesanan' => 'menunggu konfirmasi',
            'tgl_pembayaran' => now(),
        ]);
    }

    /** Ambil isi cookie riwayat dari response sebagai array tracking_code. */
    private function riwayatDariCookie($response): array
    {
        foreach ($response->baseResponse->headers->getCookies() as $cookie) {
            if ($cookie->getName() === 'riwayat_pesanan_kohvito') {
                return json_decode((string) $cookie->getValue(), true) ?? [];
            }
        }

        return [];
    }

    // ------------------------------------------------------------------
    // Fitur A — impor via tracking code
    // ------------------------------------------------------------------

    public function test_impor_tracking_code_valid_menambah_riwayat(): void
    {
        $this->buatPesanan('KV-AB2C3');

        $response = $this->post(route('konsumen.lacak.cari'), ['tracking_code' => 'KV-AB2C3']);

        $response->assertOk();
        $this->assertContains('KV-AB2C3', session('riwayat_pesanan', []));
        $this->assertContains('KV-AB2C3', $this->riwayatDariCookie($response));
    }

    public function test_kode_format_salah_ditolak_tanpa_menyentuh_riwayat(): void
    {
        $response = $this->post(route('konsumen.lacak.cari'), ['tracking_code' => "KV-AB2C3' OR 1=1"]);

        $response->assertRedirect(route('konsumen.lacak.form'));
        $response->assertSessionHas('error');
        $this->assertEmpty(session('riwayat_pesanan', []));
    }

    public function test_kode_tidak_terdaftar_ditolak(): void
    {
        $response = $this->post(route('konsumen.lacak.cari'), ['tracking_code' => 'KV-ZZ9ZZ']);

        $response->assertRedirect(route('konsumen.lacak.form'));
        $response->assertSessionHas('error');
        $this->assertEmpty(session('riwayat_pesanan', []));
    }

    public function test_impor_kode_yang_sama_tidak_menghasilkan_duplikat(): void
    {
        $this->buatPesanan('KV-AB2C3');

        $response = $this->withSession(['riwayat_pesanan' => ['KV-AB2C3']])
            ->withUnencryptedCookie('riwayat_pesanan_kohvito', json_encode(['KV-AB2C3']))
            ->post(route('konsumen.lacak.cari'), ['tracking_code' => 'KV-AB2C3']);

        $response->assertOk();
        $this->assertSame(['KV-AB2C3'], array_values(session('riwayat_pesanan')));
        $this->assertSame(['KV-AB2C3'], array_values($this->riwayatDariCookie($response)));
    }
}
