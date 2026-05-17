<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KategoriMenu extends Model
{
    /**
     * Tabel yang digunakan oleh model.
     */
    protected $table = 'kategori_menu';

    /**
     * Primary key tabel.
     */
    protected $primaryKey = 'id_kategori';

    /**
     * Nonaktifkan timestamps.
     */
    public $timestamps = false;

    /**
     * Field yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'nama_kategori',
    ];

    /**
     * Relasi: Satu kategori bisa punya banyak menu (pivot).
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'menu_kategori', 'id_kategori', 'id_menu');
    }
}
