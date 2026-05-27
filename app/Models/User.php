<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 * 
 * Model ini mewakili entitas pengguna (staf kafe) di database (tabel `users`).
 * Digunakan untuk menangani proses autentikasi dashboard admin, panel kasir,
 * serta verifikasi token otentikasi API via Laravel Sanctum untuk aplikasi eksternal.
 *
 * @package App\Models
 * @property int $id_users ID Unik Pengguna (Primary Key)
 * @property int $id_role ID Hak Akses Otoritas (Foreign Key dari `role`)
 * @property string $nama_lengkap Nama lengkap staf pengguna
 * @property string $username Nama akun unik untuk proses masuk sistem (login)
 * @property string $password Kata sandi terenkripsi (hashed) pengguna
 */
class User extends Authenticatable
{
    use HasFactory, HasApiTokens;

    /**
     * Nama tabel database yang dikaitkan dengan model User.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Kunci primer (Primary Key) dari tabel users.
     *
     * @var string
     */
    protected $primaryKey = 'id_users';

    /**
     * Menonaktifkan timestamps otomatis (`created_at` & `updated_at`).
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atribut-atribut yang diperbolehkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_role',      // ID level hak akses (Admin/Kasir)
        'nama_lengkap', // Nama asli pengguna
        'username',     // Username login
        'password',     // Password terenkripsi
    ];

    /**
     * Atribut yang disembunyikan dari representasi JSON/Array serialisasi demi keamanan data.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', // Sembunyikan hash password dari query API
    ];

    /**
     * Cast otomatis tipe data dari database ke PHP bawaan.
     * Mengamankan enkripsi hash sandi menggunakan mekanisme hashing bawaan Laravel secara implisit.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_users' => 'integer', // Pastikan ID dibaca integer
            'id_role'  => 'integer', // Pastikan ID Role dibaca integer
            'password' => 'hashed',  // Enkripsi otomatis/hashing sandi saat disimpan
        ];
    }

    /**
     * Hubungan Banyak-ke-Satu (Many-to-One / BelongsTo) ke model Role.
     * Mengaitkan pengguna dengan hak akses otoritas perannya (Admin / Kasir).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(
            Role::class,   // Model relasi tujuan
            'id_role',     // Foreign key di tabel users
            'id_role'      // Owner key pada tabel role
        );
    }

    /**
     * Hubungan Satu-ke-Banyak (One-to-Many / HasMany) ke model Pesanan.
     * Menghubungkan staf kasir/pengguna ini dengan pesanan-pesanan yang pernah dikonfirmasi/diproses olehnya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pesanan(): HasMany
    {
        return $this->hasMany(
            Pesanan::class, // Model relasi tujuan
            'id_user',      // Foreign key di tabel pesanan yang mengarah ke id_users
            'id_users'      // Local key (primary key) pada tabel users
        );
    }
}
