<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Role
 * 
 * Model ini merepresentasikan entitas peran pengguna di database (tabel `role`).
 * Menentukan tingkat otoritas pengguna (seperti 'Admin' atau 'Kasir') yang kemudian digunakan
 * oleh sistem otentikasi & middleware untuk melindungi akses dashboard masing-masing.
 *
 * @package App\Models
 * @property int $id_role ID Unik Hak Akses Peran (Primary Key)
 * @property string $nama_role Nama Peran (misal: "Admin", "Kasir")
 */
class Role extends Model
{
    /**
     * Nama tabel database yang dihubungkan dengan model ini.
     *
     * @var string
     */
    protected $table = 'role';

    /**
     * Kunci primer (Primary Key) dari tabel role.
     *
     * @var string
     */
    protected $primaryKey = 'id_role';

    /**
     * Menandakan apakah primary key di tabel ini bersifat auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Menonaktifkan sistem timestamp otomatis (`created_at` & `updated_at`).
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_role', // Nama otorisasi peran pengguna
    ];

    /**
     * Cast otomatis tipe data kolom ke tipe data PHP asli.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_role'   => 'integer', // Pastikan ID Role dibaca sebagai integer
            'nama_role' => 'string',  // Pastikan Nama Role dibaca sebagai string
        ];
    }

    /**
     * Hubungan Satu-ke-Banyak (One-to-Many / HasMany) ke model User.
     * Menghubungkan satu jenis hak akses peran ke daftar akun pengguna yang memiliki peran tersebut.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(
            User::class, // Model relasi tujuan
            'id_role',   // Foreign key di tabel users yang mengarah ke role ini
            'id_role'    // Local key (primary key) pada tabel role
        );
    }
}
