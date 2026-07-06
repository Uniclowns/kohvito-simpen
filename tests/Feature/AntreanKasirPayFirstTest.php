<?php

namespace Tests\Feature;

use App\Models\Meja;
use App\Models\Pesanan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Menguji alur pay-first pada antrean kasir:
 * pesanan yang belum lunas tidak boleh tampil di antrean kasir
 * dan tidak boleh bisa diproses (transisi status) oleh kasir.
 */
class AntreanKasirPayFirstTest extends TestCase
{
    use RefreshDatabase;

    private function buatKasir(): User
    {
        $role = Role::create(['nama_role' => 'Kasir']);

        return User::create([
            'id_role' => $role->id_role,
            'nama_lengkap' => 'Kasir Test',
            'username' => 'kasir.test',
            'password' => 'rahasia-test',
        ]);
    }

    private function buatPesanan(Meja $meja, string $statusBayar): Pesanan
    {
        return Pesanan::create([
            'no_pesanan' => 'PS-'.now()->format('YmdHis').'-'.strtoupper(Str::random(4)),
            'id_meja' => $meja->id_meja,
            'nama_konsumen' => 'Tester',
            'total_harga' => 10000,
            'status_pembayaran' => $statusBayar,
            'status_pesanan' => 'menunggu konfirmasi',
            'tgl_pembayaran' => $statusBayar === 'lunas' ? now() : null,
        ]);
    }

    public function test_pesanan_belum_lunas_tidak_masuk_antrean_kasir(): void
    {
        $meja = Meja::create(['no_meja' => 'M01', 'qr_code' => 'M01']);
        $lunas = $this->buatPesanan($meja, 'lunas');
        $belumBayar = $this->buatPesanan($meja, 'menunggu');

        $this->actingAs($this->buatKasir())
            ->get(route('kasir.pesanan.index'))
            ->assertOk()
            ->assertSee($lunas->no_pesanan)
            ->assertDontSee($belumBayar->no_pesanan);
    }

    public function test_pesanan_belum_lunas_tidak_bisa_diproses_kasir(): void
    {
        $meja = Meja::create(['no_meja' => 'M01', 'qr_code' => 'M01']);
        $belumBayar = $this->buatPesanan($meja, 'menunggu');

        $this->actingAs($this->buatKasir())
            ->put(route('kasir.pesanan.update-status', $belumBayar->no_pesanan))
            ->assertSessionHas('error');

        // Status pesanan tidak boleh berubah
        $this->assertSame('menunggu konfirmasi', $belumBayar->fresh()->status_pesanan);
    }

    public function test_pesanan_lunas_tetap_bisa_diproses_kasir(): void
    {
        $meja = Meja::create(['no_meja' => 'M01', 'qr_code' => 'M01']);
        $lunas = $this->buatPesanan($meja, 'lunas');

        $this->actingAs($this->buatKasir())
            ->put(route('kasir.pesanan.update-status', $lunas->no_pesanan))
            ->assertSessionHas('success');

        $this->assertSame('diproses', $lunas->fresh()->status_pesanan);
    }
}
