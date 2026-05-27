<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class DetailPesanan
 * 
 * Model ini merepresentasikan entitas detail pesanan di database (tabel `detail_pesanan`).
 * Detail pesanan mencatat relasi banyak-ke-banyak (many-to-many) antara Pesanan dan Menu
 * beserta atribut transaksional tambahan seperti jumlah, catatan khusus, dan subtotal harga.
 *
 * @package App\Models
 * @property int $id_detail ID Unik Auto-increment Detail Pesanan
 * @property string $no_pesanan Kode Transaksi Referensi (Foreign Key dari `pesanan`)
 * @property int $id_menu ID Menu Referensi (Foreign Key dari `menu`)
 * @property int $jumlah Kuantitas menu yang dipesan oleh konsumen
 * @property string|null $catatan Instruksi khusus dari konsumen (misal: "tidak pakai es")
 * @property int $subtotal Hasil kali kuantitas pesanan dengan harga menu pada saat dipesan
 */
class DetailPesanan extends Model
{
    /**
     * Nama tabel di database yang dikaitkan dengan model ini.
     * Secara default Eloquent menggunakan bentuk jamak bahasa Inggris,
     * sehingga kita perlu mendefinisikan ini secara manual untuk tabel 'detail_pesanan'.
     *
     * @var string
     */
    protected $table = 'detail_pesanan';

    /**
     * Kunci primer (Primary Key) dari tabel detail_pesanan.
     *
     * @var string
     */
    protected $primaryKey = 'id_detail';

    /**
     * Menandakan apakah primary key di tabel ini bersifat auto-incrementing.
     * Bernilai true karena kunci primer berupa integer dinamis.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Menentukan apakah model menyertakan timestamp otomatis (`created_at` dan `updated_at`).
     * Diatur ke false karena skema tabel ini tidak memiliki kedua kolom tersebut.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Kolom-kolom (atribut) yang diizinkan untuk diisi menggunakan mass-assignment.
     * Melindungi sistem dari kerentanan keamanan berupa Mass Assignment Vulnerability.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no_pesanan',   // Kolom penghubung ke header pesanan
        'id_menu',      // Kolom penghubung ke data menu
        'jumlah',       // Jumlah porsi menu yang dipesan
        'catatan',      // Instruksi tambahan / kustomisasi makanan/minuman
        'subtotal',     // Nilai akumulasi harga (harga_menu * jumlah)
    ];

    /**
     * Cast type dari atribut-atribut agar terkonversi otomatis ke tipe data PHP yang sesuai.
     * Hal ini memastikan integritas tipe data saat berinteraksi dengan API atau komponen Vue/React.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_detail'  => 'integer', // Konversi eksplisit ID Detail ke integer
            'id_menu'    => 'integer', // Konversi eksplisit ID Menu ke integer
            'jumlah'     => 'integer', // Konversi jumlah kuantitas ke integer
            'subtotal'   => 'integer', // Konversi nominal subtotal harga ke integer
        ];
    }

    /**
     * Mendefinisikan relasi Many-to-One (BelongsTo) ke model Pesanan.
     * Menghubungkan kolom `no_pesanan` di tabel detail ke kolom `no_pesanan` utama.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(
            Pesanan::class,    // Model tujuan relasi
            'no_pesanan',      // Foreign key di tabel detail_pesanan
            'no_pesanan'       // Owner key (primary key) di tabel pesanan
        );
    }

    /**
     * Mendefinisikan relasi Many-to-One (BelongsTo) ke model Menu.
     * Menghubungkan setiap baris detail pesanan dengan produk menu yang bersangkutan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(
            Menu::class,       // Model tujuan relasi
            'id_menu',         // Foreign key di tabel detail_pesanan
            'id_menu'          // Owner key (primary key) di tabel menu
        );
    }
}
