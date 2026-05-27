<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Meja
 * 
 * Model ini merepresentasikan entitas meja pelanggan di database (tabel `meja`).
 * Meja memegang peranan krusial dalam sistem pemesanan *dine-in*, di mana setiap nomor meja
 * diasosiasikan dengan sebuah kode QR unik yang discan oleh konsumen untuk memesan makanan.
 *
 * @package App\Models
 * @property int $id_meja ID Unik Meja (Primary Key)
 * @property string $no_meja Nomor atau nama identifikasi meja (misal: "01", "02")
 * @property string|null $qr_code Berisi representasi path/link atau konten dari QR code meja tersebut
 */
class Meja extends Model
{
    /**
     * Nama tabel database yang dikaitkan dengan model Meja.
     *
     * @var string
     */
    protected $table = 'meja';

    /**
     * Kunci primer (Primary Key) dari tabel meja.
     *
     * @var string
     */
    protected $primaryKey = 'id_meja';

    /**
     * Menandakan apakah primary key di tabel ini bersifat auto-incrementing.
     * Bernilai true karena kunci primer meja adalah integer berurutan.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Menentukan apakah model menyertakan timestamp otomatis (`created_at` dan `updated_at`).
     * Diberi nilai false karena tabel meja dikelola secara statis tanpa tracking waktu pembuatan.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atribut-atribut yang dapat diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no_meja', // Identitas nomor meja fisik di kafe
        'qr_code', // Data/String QR Code unik untuk meja bersangkutan
    ];

    /**
     * Casting tipe data atribut agar sesuai dengan tipe data PHP asli saat diolah.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_meja' => 'integer', // Pastikan ID Meja terkonversi menjadi integer
            'no_meja' => 'string',  // Pastikan No Meja dibaca sebagai string
            'qr_code' => 'string',  // Pastikan QR Code dibaca sebagai string
        ];
    }

    /**
     * Mendefinisikan relasi One-to-Many (HasMany) ke model Pesanan.
     * Menghubungkan meja ini dengan semua riwayat transaksi pemesanan yang pernah dilakukan darinya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pesanan(): HasMany
    {
        return $this->hasMany(
            Pesanan::class, // Model tujuan relasi
            'id_meja',      // Foreign key di tabel pesanan yang mengarah ke meja ini
            'id_meja'       // Local key (primary key) pada tabel meja
        );
    }
}
