<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class KategoriMenu
 * 
 * Model ini merepresentasikan entitas kategori menu di database (tabel `kategori_menu`).
 * Digunakan untuk mengelompokkan menu (misalnya: Kopi, Cemilan, Makanan Berat, dsb.)
 * agar mempermudah proses penyaringan dan navigasi oleh konsumen maupun admin.
 *
 * @package App\Models
 * @property int $id_kategori ID Unik Kategori Menu (Primary Key)
 * @property string $nama_kategori Nama Kategori (misal: "Kopi", "Non-Kopi", "Makanan Utama")
 */
class KategoriMenu extends Model
{
    /**
     * Nama tabel database yang direpresentasikan oleh model ini.
     *
     * @var string
     */
    protected $table = 'kategori_menu';

    /**
     * Nama kolom kunci primer (Primary Key) di tabel.
     *
     * @var string
     */
    protected $primaryKey = 'id_kategori';

    /**
     * Menentukan apakah tipe primary key auto-increment.
     * Bernilai true karena menggunakan auto-increment integer bawaan.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Menonaktifkan sistem timestamp otomatis (`created_at` & `updated_at`).
     * Disesuaikan dengan struktur tabel database yang tidak menggunakan penanda waktu.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Kolom yang diperbolehkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_kategori', // Menyimpan label/nama kategori menu
    ];

    /**
     * Casting atribut bawaan Laravel ke tipe data PHP asli.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_kategori'   => 'integer', // Pastikan ID Kategori dibaca sebagai integer
            'nama_kategori' => 'string',  // Pastikan Nama Kategori dibaca sebagai string
        ];
    }

    /**
     * Hubungan Banyak-ke-Banyak (Many-to-Many) ke model Menu.
     * Menghubungkan kategori ini ke daftar produk menu yang termasuk di dalamnya,
     * dijembatani oleh tabel pivot `menu_kategori`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(
            Menu::class,          // Model relasi tujuan
            'menu_kategori',      // Nama tabel perantara (pivot table)
            'id_kategori',        // Foreign key di tabel pivot untuk model asal (KategoriMenu)
            'id_menu'             // Foreign key di tabel pivot untuk model tujuan (Menu)
        );
    }
}
