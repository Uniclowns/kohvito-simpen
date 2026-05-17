<?php

namespace Tests\Feature;

use App\Models\KategoriMenu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminKategoriMenuPopupTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_kategori_menu_redirects_with_status_modal_flash(): void
    {
        $role = Role::create(['nama_role' => 'Admin']);
        $admin = User::create([
            'id_role' => $role->id_role,
            'nama_lengkap' => 'Admin Test',
            'username' => 'admin-test',
            'password' => bcrypt('password'),
        ]);

        KategoriMenu::create(['nama_kategori' => 'Coffee']);

        $response = $this
            ->actingAs($admin)
            ->from(route('admin.kategori.index'))
            ->post(route('admin.kategori.store'), [
                'nama_kategori' => 'Coffee',
            ]);

        $response->assertRedirect(route('admin.kategori.index'));
        $response->assertSessionHas('status_modal.title', 'Gagal Menambah Kategori Menu');
        $response->assertSessionHas('status_modal.message', 'Nama kategori yang Anda masukkan sudah digunakan. Silakan gunakan nama kategori lain.');
    }

    public function test_duplicate_update_kategori_menu_redirects_with_status_modal_flash(): void
    {
        $role = Role::create(['nama_role' => 'Admin']);
        $admin = User::create([
            'id_role' => $role->id_role,
            'nama_lengkap' => 'Admin Test',
            'username' => 'admin-test',
            'password' => bcrypt('password'),
        ]);

        KategoriMenu::create(['nama_kategori' => 'Coffee']);
        $tea = KategoriMenu::create(['nama_kategori' => 'Tea']);

        $response = $this
            ->actingAs($admin)
            ->from(route('admin.kategori.index'))
            ->put(route('admin.kategori.update', $tea->id_kategori), [
                'nama_kategori' => 'Coffee',
            ]);

        $response->assertRedirect(route('admin.kategori.index'));
        $response->assertSessionHas('status_modal.title', 'Gagal Memperbarui Kategori Menu');
        $response->assertSessionHas('status_modal.message', 'Nama kategori yang Anda masukkan sudah digunakan. Silakan gunakan nama kategori lain.');
    }
}
