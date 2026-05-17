<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meja extends Model
{
    /**
     * Tabel yang digunakan oleh model.
     */
    protected $table = 'meja';

    /**
     * Primary key tabel.
     */
    protected $primaryKey = 'id_meja';

    /**
     * Nonaktifkan timestamps.
     */
    public $timestamps = false;

    /**
     * Field yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'no_meja',
        'qr_code',
    ];

    /**
     * Relasi: Satu meja memiliki banyak pesanan.
     */
    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_meja', 'id_meja');
    }
}
