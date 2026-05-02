<?php

namespace App\Models;

use Database\Factories\MenuFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'id_kategori',
        'nama_menu',
        'deskripsi',
        'harga',
        'gambar_menu',
        'status_ketersediaan',
    ];

    /**
     * Casting atribut.
     */
    protected function casts(): array
    {
        return [
            'harga' => 'integer',
        ];
    }

    /**
     * Relasi: Menu terikat ke satu kategori.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriMenu::class, 'id_kategori', 'id_kategori');
    }

    /**
     * Relasi: Satu menu dipesan di banyak detail pesanan.
     */
    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'id_menu', 'id_menu');
    }
}
