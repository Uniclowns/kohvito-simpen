<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens;

    /**
     * Tabel yang digunakan oleh model.
     */
    protected $table = 'users';

    /**
     * Primary key tabel.
     */
    protected $primaryKey = 'id_users';

    /**
     * Nonaktifkan timestamps (tabel tidak memiliki created_at/updated_at).
     */
    public $timestamps = false;

    /**
     * Field yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'id_role',
        'nama_lengkap',
        'username',
        'password',
    ];

    /**
     * Field yang disembunyikan dari serialisasi.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Casting atribut.
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi: User terikat ke satu role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    /**
     * Relasi: User memproses banyak pesanan.
     */
    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_user', 'id_users');
    }
}
