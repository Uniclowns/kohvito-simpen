<?php

namespace Tests\Feature;

use App\Models\DetailPesanan;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pesanan;
use Tests\TestCase;

/**
 * Memverifikasi struk kasir kini berupa halaman HTML siap-cetak (window.print)
 * alih-alih unduhan PDF, sekaligus memastikan ISI struk tidak berubah.
 */
class CetakStrukPrintViewTest extends TestCase
{
    public function test_struk_print_view_is_printable_html_and_keeps_receipt_content(): void
    {
        $menu = new Menu(['nama_menu' => 'Kopi Susu']);

        $detail = new DetailPesanan([
            'jumlah'   => 2,
            'subtotal' => 40000,
            'catatan'  => 'Less sugar',
        ]);
        $detail->setRelation('menu', $menu);

        $pesanan = new Pesanan([
            'no_pesanan'      => 'PS-20260605-AB12',
            'nama_konsumen'   => 'Budi',
            'total_harga'     => 44400,
            'catatan_pesanan' => 'Meja dekat jendela',
            'tgl_pembayaran'  => '2026-06-05 14:30:00',
        ]);
        $pesanan->setRelation('meja', new Meja(['no_meja' => 'M05', 'qr_code' => 'x']));
        $pesanan->setRelation('detailPesanan', collect([$detail]));

        $html = view('kasir.cetak-pesanan-print', compact('pesanan'))->render();

        // Halaman HTML siap-cetak (bukan PDF) yang mencetak dirinya sendiri.
        $this->assertStringContainsString('<!DOCTYPE html', $html);
        $this->assertStringContainsString('window.print', $html);
        // Struk thermal monospace 80mm.
        $this->assertStringContainsString('80mm', $html);
        $this->assertStringContainsString('monospace', $html);

        // Logo Kohvito (file putih) dipaksa hitam agar tampak di kertas putih.
        $this->assertStringContainsString('KOHVITO LOGO WHITE.png', $html);
        $this->assertStringContainsString('brightness(0)', $html);

        // Header info transaksi.
        $this->assertStringContainsString('PS-20260605-AB12', $html); // No
        $this->assertStringContainsString('M05', $html);              // Table
        $this->assertStringContainsString('Budi', $html);             // Info (nama konsumen)
        $this->assertStringContainsString('DINE IN', $html);          // Purpose

        // Item (nama di-uppercase ala struk) + catatan item.
        $this->assertStringContainsString('KOPI SUSU', $html);
        $this->assertStringContainsString('Less sugar', $html);

        // Ringkasan pembayaran: angka format koma, tanpa "Rp".
        $this->assertStringContainsString('1 Items', $html);
        $this->assertStringContainsString('40,000', $html); // subtotal
        $this->assertStringContainsString('4,400', $html);  // PPN 11%
        $this->assertStringContainsString('Grand Total', $html);
        $this->assertStringContainsString('44,400', $html); // grand total
        $this->assertStringContainsString('QRIS', $html);

        // Footer.
        $this->assertStringContainsString('KOHVITO 5G', $html);
        $this->assertStringContainsString('UBESERIES', $html);
        $this->assertStringContainsString('Thank You', $html);
    }
}
