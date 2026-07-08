<?php

namespace Tests\Feature;

use App\Models\Meja;
use App\Models\Pesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Menguji pemulihan riwayat pesanan lintas-browser TANPA login:
 * riwayat otomatis mengikuti pesanan yang berhasil dilacak (auto-merge
 * diam-diam) di semua jalur pelacakan — /lacak-pesanan (form maupun
 * ?kode= dari QR kuitansi), halaman lacak per meja, dan kuitansi.
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

    // ------------------------------------------------------------------
    // Auto-submit via query param ?kode= (QR di kuitansi/struk)
    // ------------------------------------------------------------------

    public function test_query_kode_valid_langsung_menampilkan_status_dan_menambah_riwayat(): void
    {
        $this->buatPesanan('KV-AB2C3');

        $response = $this->get(route('konsumen.lacak.form', ['kode' => 'KV-AB2C3']));

        $response->assertOk();
        $response->assertSee('KV-AB2C3');
        $this->assertContains('KV-AB2C3', session('riwayat_pesanan', []));
        $this->assertContains('KV-AB2C3', $this->riwayatDariCookie($response));
    }

    public function test_query_kode_format_salah_dialihkan_ramah_tanpa_menyentuh_riwayat(): void
    {
        $response = $this->get(route('konsumen.lacak.form', ['kode' => 'NGACO-123']));

        $response->assertRedirect(route('konsumen.lacak.form'));
        $response->assertSessionHas('error');
        $this->assertEmpty(session('riwayat_pesanan', []));
    }

    // ------------------------------------------------------------------
    // Auto-merge diam-diam di jalur lacak per meja & kuitansi
    // ------------------------------------------------------------------

    public function test_halaman_lacak_per_meja_menambah_riwayat(): void
    {
        $pesanan = $this->buatPesanan('KV-AB2C3');

        $response = $this->get(route('konsumen.lacak.detail', [
            'noMeja' => 'M01',
            'noPesanan' => $pesanan->no_pesanan,
        ]));

        $response->assertOk();
        $this->assertContains('KV-AB2C3', session('riwayat_pesanan', []));
        $this->assertContains('KV-AB2C3', $this->riwayatDariCookie($response));
    }

    public function test_halaman_kuitansi_menambah_riwayat(): void
    {
        $pesanan = $this->buatPesanan('KV-AB2C3');

        $response = $this->get(route('konsumen.pesanan.kuitansi', $pesanan->no_pesanan));

        $response->assertOk();
        $this->assertContains('KV-AB2C3', session('riwayat_pesanan', []));
        $this->assertContains('KV-AB2C3', $this->riwayatDariCookie($response));
    }

    // ------------------------------------------------------------------
    // Kontrak format riwayat
    // ------------------------------------------------------------------

    public function test_struktur_riwayat_identik_dengan_format_checkout_lama(): void
    {
        $this->buatPesanan('KV-AB2C3');

        $response = $this->post(route('konsumen.lacak.cari'), ['tracking_code' => 'KV-AB2C3']);

        // Session: array numerik berisi string kode — persis format alur checkout.
        $this->assertSame(['KV-AB2C3'], session('riwayat_pesanan'));

        // Cookie: JSON array of string, byte-per-byte sama dengan json_encode.
        $raw = null;
        foreach ($response->baseResponse->headers->getCookies() as $cookie) {
            if ($cookie->getName() === 'riwayat_pesanan_kohvito') {
                $raw = $cookie->getValue();
            }
        }

        $this->assertSame(json_encode(['KV-AB2C3']), $raw);
    }
}
