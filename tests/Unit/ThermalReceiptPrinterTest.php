<?php

namespace Tests\Unit;

use App\Models\DetailPesanan;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Services\ThermalReceiptPrinter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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

    public function test_dokumen_menggabungkan_struk_dengan_checker_barista_dan_kitchen(): void
    {
        $dok = $this->buildSampleDocument();

        // Struk pelanggan tetap ada di awal dokumen.
        $this->assertStringContainsString('KAFE KOHVITO', $dok);

        // Checker Barista untuk minuman + Checker Kitchen untuk makanan.
        $this->assertStringContainsString('CHECKER BARISTA', $dok);
        $this->assertStringContainsString('CHECKER KITCHEN', $dok);
        $this->assertStringContainsString('MINUMAN', $dok);
        $this->assertStringContainsString('MAKANAN', $dok);

        // Nama item per kategori muncul di checker masing-masing.
        $this->assertStringContainsString('Kopi Susu', $dok);
        $this->assertStringContainsString('Nasi Goreng', $dok);

        // Varian/catatan item dipecah menjadi label & nilai.
        $this->assertStringContainsString('Ukuran', $dok);
        $this->assertStringContainsString('Large', $dok);
        $this->assertStringContainsString('Gula', $dok);

        // Total item per kategori (operasional, bukan harga).
        $this->assertStringContainsString('TOTAL ITEM', $dok);

        // Checker BUKAN untuk pelanggan: tidak menampilkan harga.
        $this->assertStringNotContainsString('Rp', substr($dok, strpos($dok, 'CHECKER BARISTA')));

        // Tiga lembar (struk + 2 checker) → tiga perintah potong penuh.
        $this->assertSame(3, substr_count($dok, "\x1DV\x00"));
    }

    public function test_dokumen_tanpa_kategori_tidak_memunculkan_checker_kosong(): void
    {
        $menu = new Menu();
        $menu->nama_menu = 'Kopi Susu';
        $menu->jenis_menu = 'Minuman';

        $item = new DetailPesanan();
        $item->jumlah = 1;
        $item->subtotal = 18000;
        $item->catatan = null;
        $item->setRelation('menu', $menu);

        $dok = $this->buildDocumentFor(collect([$item]), 18000);

        // Hanya ada minuman → hanya Checker Barista, tanpa Checker Kitchen kosong.
        $this->assertStringContainsString('CHECKER BARISTA', $dok);
        $this->assertStringNotContainsString('CHECKER KITCHEN', $dok);

        // Dua lembar (struk + 1 checker) → dua perintah potong.
        $this->assertSame(2, substr_count($dok, "\x1DV\x00"));
    }

    /**
     * Membangun dokumen contoh (struk + checker) dari item lintas kategori.
     *
     * @return string
     */
    private function buildSampleDocument(): string
    {
        $menuKopi = new Menu();
        $menuKopi->nama_menu = 'Kopi Susu';
        $menuKopi->jenis_menu = 'Minuman';

        $menuNasi = new Menu();
        $menuNasi->nama_menu = 'Nasi Goreng';
        $menuNasi->jenis_menu = 'Makanan';

        $itemKopi = new DetailPesanan();
        $itemKopi->jumlah = 2;
        $itemKopi->subtotal = 36000;
        $itemKopi->catatan = 'Ukuran: Large | Gula: Sedikit';
        $itemKopi->setRelation('menu', $menuKopi);

        $itemNasi = new DetailPesanan();
        $itemNasi->jumlah = 1;
        $itemNasi->subtotal = 25000;
        $itemNasi->catatan = null;
        $itemNasi->setRelation('menu', $menuNasi);

        return $this->buildDocumentFor(collect([$itemKopi, $itemNasi]), 61000);
    }

    /**
     * Membangun dokumen ESC/POS lengkap dari kumpulan item dan total tertentu.
     *
     * @param  \Illuminate\Support\Collection  $details
     * @param  int  $total
     * @return string
     */
    private function buildDocumentFor(Collection $details, int $total): string
    {
        $meja = new Meja();
        $meja->no_meja = 5;

        $pesanan = new Pesanan();
        $pesanan->no_pesanan = 'KOH-TEST-002';
        $pesanan->nama_konsumen = 'Budi';
        $pesanan->status_pembayaran = 'lunas';
        $pesanan->total_harga = $total;
        $pesanan->tgl_pembayaran = Carbon::create(2026, 5, 30, 21, 40);
        $pesanan->setRelation('meja', $meja);
        $pesanan->setRelation('detailPesanan', $details);

        return (new ThermalReceiptPrinter('192.168.50.230', 9100, 5, 42))
            ->buildDocument($pesanan);
    }

    /**
     * Membangun struk contoh dari objek Pesanan in-memory (tanpa basis data).
     */
    private function buildSampleReceipt(): string
    {
        $menuKopi = new Menu;
        $menuKopi->nama_menu = 'Kopi Susu';

        $menuRoti = new Menu;
        $menuRoti->nama_menu = 'Croissant';

        $itemKopi = new DetailPesanan;
        $itemKopi->jumlah = 2;
        $itemKopi->subtotal = 36000;
        $itemKopi->catatan = 'tidak pakai gula';
        $itemKopi->setRelation('menu', $menuKopi);

        $itemRoti = new DetailPesanan;
        $itemRoti->jumlah = 1;
        $itemRoti->subtotal = 22000;
        $itemRoti->catatan = null;
        $itemRoti->setRelation('menu', $menuRoti);

        $meja = new Meja;
        $meja->no_meja = 5;

        $pesanan = new Pesanan;
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
