<?php

namespace Tests\Unit;

use App\Models\DetailPesanan;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Services\ThermalReceiptPrinter;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Menguji penyusunan byte ESC/POS struk tanpa printer fisik.
 * Hanya memverifikasi {@see ThermalReceiptPrinter::buildReceipt()} yang murni.
 */
class ThermalReceiptPrinterTest extends TestCase
{
    public function test_struk_memuat_kepala_info_item_dan_total(): void
    {
        $struk = $this->buildSampleReceipt();

        // Kepala & identitas pesanan
        $this->assertStringContainsString('KAFE KOHVITO', $struk);
        $this->assertStringContainsString('Struk Pesanan', $struk);
        $this->assertStringContainsString('KOH-TEST-001', $struk);
        $this->assertStringContainsString('Budi', $struk);
        $this->assertStringContainsString('LUNAS', $struk);

        // Daftar item + catatan item
        $this->assertStringContainsString('Kopi Susu', $struk);
        $this->assertStringContainsString('Croissant', $struk);
        $this->assertStringContainsString('tidak pakai gula', $struk);

        // Total mengikuti total_harga tersimpan (tanpa PPN tambahan)
        $this->assertStringContainsString('TOTAL', $struk);
        $this->assertStringContainsString('Rp 58.000', $struk);

        // Catatan global pesanan
        $this->assertStringContainsString('meja dekat jendela', $struk);
    }

    public function test_struk_diawali_inisialisasi_dan_diakhiri_potong_kertas(): void
    {
        $struk = $this->buildSampleReceipt();

        // ESC @ di awal (inisialisasi printer)
        $this->assertStringStartsWith("\x1B@", $struk);

        // GS V 0 di akhir aliran (potong penuh)
        $this->assertStringContainsString("\x1DV\x00", $struk);
    }

    /**
     * Membangun struk contoh dari objek Pesanan in-memory (tanpa basis data).
     *
     * @return string
     */
    private function buildSampleReceipt(): string
    {
        $menuKopi = new Menu();
        $menuKopi->nama_menu = 'Kopi Susu';

        $menuRoti = new Menu();
        $menuRoti->nama_menu = 'Croissant';

        $itemKopi = new DetailPesanan();
        $itemKopi->jumlah = 2;
        $itemKopi->subtotal = 36000;
        $itemKopi->catatan = 'tidak pakai gula';
        $itemKopi->setRelation('menu', $menuKopi);

        $itemRoti = new DetailPesanan();
        $itemRoti->jumlah = 1;
        $itemRoti->subtotal = 22000;
        $itemRoti->catatan = null;
        $itemRoti->setRelation('menu', $menuRoti);

        $meja = new Meja();
        $meja->no_meja = 5;

        $pesanan = new Pesanan();
        $pesanan->no_pesanan = 'KOH-TEST-001';
        $pesanan->nama_konsumen = 'Budi';
        $pesanan->status_pembayaran = 'lunas';
        $pesanan->total_harga = 58000;
        $pesanan->catatan_pesanan = 'meja dekat jendela';
        $pesanan->tgl_pembayaran = Carbon::create(2026, 5, 30, 21, 40);
        $pesanan->setRelation('meja', $meja);
        $pesanan->setRelation('detailPesanan', collect([$itemKopi, $itemRoti]));

        return (new ThermalReceiptPrinter('192.168.50.230', 9100, 5, 42))
            ->buildReceipt($pesanan);
    }
}
