<?php

namespace App\Models;

use Database\Factories\MenuFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    /** @use HasFactory<MenuFactory> */
    use HasFactory;

    /**
     * Tabel yang digunakan oleh model.
     */
    protected $table = 'menu';

    /**
     * Primary key tabel.
     */
    protected $primaryKey = 'id_menu';

    /**
     * Nonaktifkan timestamps.
     */
    public $timestamps = false;

    /**
     * Field yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'nama_menu',
        'deskripsi',
        'harga',
        'stock',
        'komposisi',
        'gambar_menu',
        'status_ketersediaan',
        'jenis_menu',
        'kategori_makanan',
        'tipe_minuman',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
        ];
    }

    /**
     * Relasi: Menu bisa punya banyak kategori (pivot).
     */
    public function kategoris(): BelongsToMany
    {
        return $this->belongsToMany(
            KategoriMenu::class,
            'menu_kategori',
            'id_menu',
            'id_kategori'
        );
    }

    /**
     * Relasi: Satu menu dipesan di banyak detail pesanan.
     */
    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'id_menu', 'id_menu');
    }
}
