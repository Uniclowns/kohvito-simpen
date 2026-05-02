<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPesanan extends Model
{
    /**
     * Tabel yang digunakan oleh model.
     */
    protected $table = 'detail_pesanan';

    /**
     * Primary key tabel.
     */
    protected $primaryKey = 'id_detail';

    /**
     * Nonaktifkan timestamps.
     */
    public $timestamps = false;

    /**
     * Field yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'no_pesanan',
        'id_menu',
        'jumlah',
        'catatan',
        'subtotal',
    ];

    /**
     * Casting atribut.
     */
    protected function casts(): array
    {
        return [
            'jumlah'   => 'integer',
            'subtotal' => 'integer',
        ];
    }

    /**
     * Relasi: Detail pesanan terikat ke satu pesanan.
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'no_pesanan', 'no_pesanan');
    }

    /**
     * Relasi: Detail pesanan terikat ke satu menu.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'id_menu', 'id_menu');
    }
}
