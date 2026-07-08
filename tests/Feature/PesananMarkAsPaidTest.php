<?php

namespace Tests\Feature;

use App\Models\Meja;
use App\Models\Pesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PesananMarkAsPaidTest extends TestCase
{
    use RefreshDatabase;

    public function test_mark_as_paid_sets_lunas_queues_order_and_stamps_time(): void
    {
        $meja = Meja::create(['no_meja' => 'M01', 'qr_code' => 'M01']);

        $pesanan = Pesanan::create([
            'no_pesanan' => 'KHV-TEST-PAID',
            'id_meja' => $meja->id_meja,
            'nama_konsumen' => 'Budi',
            'total_harga' => 30000,
            'status_pembayaran' => 'menunggu',
            // Sengaja diawali 'diproses' untuk memastikan markAsPaid menormalkan
            // ulang status pesanan ke antrean dapur 'menunggu konfirmasi'.
            'status_pesanan' => 'diproses',
        ]);

        $pesanan->markAsPaid();

        $this->assertSame('lunas', $pesanan->status_pembayaran);
        $this->assertSame('menunggu konfirmasi', $pesanan->status_pesanan);
        $this->assertNotNull($pesanan->tgl_pembayaran);

        $fresh = Pesanan::find('KHV-TEST-PAID');
        $this->assertSame('lunas', $fresh->status_pembayaran);
        $this->assertSame('menunggu konfirmasi', $fresh->status_pesanan);
        $this->assertNotNull($fresh->tgl_pembayaran);
    }
}
