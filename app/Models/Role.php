<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /**
     * Tabel yang digunakan oleh model.
     */
    protected $table = 'role';

    /**
     * Primary key tabel.
     */
    protected $primaryKey = 'id_role';

    /**
     * Nonaktifkan timestamps (tabel tidak memiliki created_at/updated_at).
     */
    public $timestamps = false;

    /**
     * Field yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'nama_role',
    ];

    /**
     * Relasi: Satu role memiliki banyak user.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_role', 'id_role');
    }
}
