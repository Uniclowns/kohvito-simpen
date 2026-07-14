<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SuperadminUiIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('users')->delete();
        DB::table('role')->delete();
        DB::table('role')->insert([
            ['id_role' => 1, 'nama_role' => 'Admin'],
            ['id_role' => 2, 'nama_role' => 'Kasir'],
            ['id_role' => 3, 'nama_role' => 'Super Admin'],
        ]);
    }

    public function test_superadmin_pages_render_the_console_shell(): void
    {
        $superadmin = User::create([
            'id_role' => 3,
            'nama_lengkap' => 'Super Administrator',
            'username' => 'superadmin-test',
            'password' => 'password-test',
        ]);

        $this->actingAs($superadmin)
            ->get(route('superadmin.beranda'))
            ->assertOk()
            ->assertSee('superadmin-sidebar', false)
            ->assertSee('System console');

        $this->get(route('superadmin.admin.index'))
            ->assertOk()
            ->assertSee('superadmin-sidebar', false)
            ->assertSee('Manajemen admin');
    }

    public function test_admin_keeps_the_existing_shell_and_cannot_open_superadmin_pages(): void
    {
        $admin = User::create([
            'id_role' => 1,
            'nama_lengkap' => 'Admin Test',
            'username' => 'admin-test',
            'password' => 'password-test',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.pengguna-kasir.index'))
            ->assertOk()
            ->assertDontSee('superadmin-sidebar', false)
            ->assertDontSee('System console');

        $this->get(route('superadmin.beranda'))->assertForbidden();
    }
}
