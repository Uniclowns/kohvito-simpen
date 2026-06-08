<?php

namespace Tests\Feature;

use App\Models\DetailPesanan;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Memverifikasi Checker internal (Barista untuk minuman, Kitchen untuk makanan)
 * ikut tercetak bersama Struk Pelanggan sesuai jenis pesanan — 3 skenario.
 */
class CetakCheckerTest extends TestCase
{
    private function detail(string $nama, string $jenis, int $jumlah, ?string $catatan = null): DetailPesanan
    {
        $menu = new Menu(['nama_menu' => $nama, 'jenis_menu' => $jenis]);

        $detail = new DetailPesanan([
            'jumlah'   => $jumlah,
            'subtotal' => 10000 * $jumlah,
            'catatan'  => $catatan,
        ]);
        $detail->setRelation('menu', $menu);

        return $detail;
    }

    private function renderStruk(Collection $details): string
    {
        $pesanan = new Pesanan([
            'no_pesanan'     => 'PS-CHK-001',
            'nama_konsumen'  => 'Budi',
            'total_harga'    => 50000,
            'tgl_pembayaran' => '2026-06-05 14:30:00',
        ]);
        $pesanan->setRelation('meja', new Meja(['no_meja' => 'M07', 'qr_code' => 'x']));
        $pesanan->setRelation('detailPesanan', $details);

        return view('kasir.cetak-pesanan-print', compact('pesanan'))->render();
    }

    /** Skenario 1: minuman saja -> Struk + Checker Barista, TANPA Checker Kitchen. */
    public function test_drinks_only_prints_barista_checker_not_kitchen(): void
    {
        $html = $this->renderStruk(collect([
            $this->detail('Angguro', 'Minuman', 1, 'Suhu: Dingin | Sugar: No Sugar | Catatan: Jangan terlalu manis'),
            $this->detail('Kiyomizu', 'Minuman', 2, 'Suhu: Dingin | Ice: Less Ice'),
        ]));

        // Struk pelanggan tetap ada (fungsi cetak struk tidak dihapus).
        $this->assertStringContainsString('Grand Total', $html);

        $this->assertStringContainsString('CHECKER BARISTA', $html);
        $this->assertStringNotContainsString('CHECKER KITCHEN', $html);
        $this->assertStringContainsString('Angguro', $html);
        $this->assertStringContainsString('Jangan terlalu manis', $html);
        $this->assertStringContainsString('TOTAL ITEM MINUMAN: 3', $html); // 1 + 2
    }

    /** Skenario 2: makanan saja -> Struk + Checker Kitchen, TANPA Checker Barista. */
    public function test_food_only_prints_kitchen_checker_not_barista(): void
    {
        $html = $this->renderStruk(collect([
            $this->detail('Ayam Duo Sambal', 'Makanan', 1, 'Catatan: Sambal dipisah'),
        ]));

        $this->assertStringContainsString('Grand Total', $html);

        $this->assertStringContainsString('CHECKER KITCHEN', $html);
        $this->assertStringNotContainsString('CHECKER BARISTA', $html);
        $this->assertStringContainsString('Ayam Duo Sambal', $html);
        $this->assertStringContainsString('Sambal dipisah', $html);
        $this->assertStringContainsString('TOTAL ITEM MAKANAN: 1', $html);
    }

    /** Skenario 3: makanan + minuman -> Struk + Barista (minuman) + Kitchen (makanan). */
    public function test_mixed_order_prints_both_checkers_split_by_category(): void
    {
        $html = $this->renderStruk(collect([
            $this->detail('Angguro', 'Minuman', 2, 'Suhu: Dingin'),
            $this->detail('Green Curry', 'Makanan', 1, 'Catatan: Tidak pedas'),
        ]));

        $this->assertStringContainsString('Grand Total', $html);

        // Kedua checker hadir...
        $this->assertStringContainsString('CHECKER BARISTA', $html);
        $this->assertStringContainsString('CHECKER KITCHEN', $html);
        $this->assertStringContainsString('TOTAL ITEM MINUMAN: 2', $html);
        $this->assertStringContainsString('TOTAL ITEM MAKANAN: 1', $html);

        // ...dan tiap sheet dipisah dengan page-break agar tidak tercampur.
        $this->assertStringContainsString('page-break-after: always', $html);

        // Pengelompokan benar berdasarkan urutan kemunculan section.
        $kitchenPos = strpos($html, 'CHECKER KITCHEN');
        $angguroPos = strpos($html, 'Angguro');
        $curryPos   = strpos($html, 'Green Curry');
        $this->assertNotFalse($kitchenPos);
        // Angguro (minuman) berada di section Barista (sebelum judul Kitchen).
        $this->assertLessThan($kitchenPos, $angguroPos);
        // Green Curry (makanan) berada di section Kitchen (setelah judul Kitchen).
        $this->assertGreaterThan($kitchenPos, $curryPos);
    }
}
