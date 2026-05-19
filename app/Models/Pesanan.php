<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    /**
     * Tabel yang digunakan oleh model.
     */
    protected $table = 'pesanan';

    /**
     * Primary key tabel (custom string PK).
     */
    protected $primaryKey = 'no_pesanan';

    /**
     * Tipe primary key adalah string (bukan integer).
     */
    protected $keyType = 'string';

    /**
     * Nonaktifkan auto-increment karena PK berupa string custom.
     */
    public $incrementing = false;

    /**
     * Nonaktifkan timestamps.
     */
    public $timestamps = false;

    /**
     * Field yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'no_pesanan',
        'id_user',
        'id_meja',
        'nama_konsumen',
        'total_harga',
        'status_pembayaran',
        'status_pesanan',
        'catatan_pesanan',
        'tgl_pembayaran',
    ];

    /**
     * Casting atribut.
     */
    protected function casts(): array
    {
        return [
            'total_harga' => 'integer',
            'tgl_pembayaran' => 'datetime',
        ];
    }

    /**
     * Relasi: Pesanan diproses oleh satu user (kasir). Nullable.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_users');
    }

    /**
     * Relasi: Pesanan berasal dari satu meja.
     */
    public function meja(): BelongsTo
    {
        return $this->belongsTo(Meja::class, 'id_meja', 'id_meja');
    }

    /**
     * Relasi: Satu pesanan memiliki banyak detail pesanan.
     */
    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'no_pesanan', 'no_pesanan');
    }
}
