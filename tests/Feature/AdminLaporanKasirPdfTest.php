<?php

namespace Tests\Feature;

use App\Models\Meja;
use App\Models\Pesanan;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLaporanKasirPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_laporan_kasir_pdf(): void
    {
        $admin = $this->makeUserWithRole('Admin', 'Admin Test');
        $kasir = $this->makeUserWithRole('Kasir', 'Kasir Test');
        $meja = Meja::create([
            'no_meja' => 'M01',
            'qr_code' => 'M01',
        ]);

        Pesanan::create([
            'no_pesanan' => 'KHV-TEST-001',
            'id_user' => $kasir->id_users,
            'id_meja' => $meja->id_meja,
            'nama_konsumen' => 'Andi',
            'total_harga' => 45000,
            'status_pembayaran' => 'lunas',
            'status_pesanan' => 'selesai',
            'tgl_pembayaran' => Carbon::parse('2026-05-15 10:30:00'),
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.laporan.cetak', [
                'tanggal_mulai' => '2026-05-15',
                'tanggal_selesai' => '2026-05-15',
            ]));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('laporan-kasir.pdf', (string) $response->headers->get('content-disposition'));
    }

    public function test_laporan_kasir_pdf_view_renders_empty_state(): void
    {
        $this->view('admin.laporan-kasir-pdf', [
            'pesanan' => collect(),
            'tanggalMulai' => Carbon::parse('2026-05-15')->startOfDay(),
            'tanggalSelesai' => Carbon::parse('2026-05-15')->endOfDay(),
        ])
            ->assertSee('KAFE KOHVITO')
            ->assertSee('Tidak ada transaksi pada periode ini.')
            ->assertSee('Total Transaksi')
            ->assertSee('0 transaksi')
            ->assertSee('Rp 0');
    }

    private function makeUserWithRole(string $roleName, string $name): User
    {
        $role = Role::create(['nama_role' => $roleName]);

        return User::create([
            'id_role' => $role->id_role,
            'nama_lengkap' => $name,
            'username' => strtolower(str_replace(' ', '-', $name)),
            'password' => bcrypt('password'),
        ]);
    }
}
