<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Pesanan
 * 
 * Model ini mewakili header transaksi pemesanan di database (tabel `pesanan`).
 * Merupakan pusat kendali alur pesanan dari konsumen, penanganan integrasi pembayaran
 * gateway (Xendit/Midtrans), pencatatan nominal belanja, serta koordinasi status penyajian dapur.
 *
 * @package App\Models
 * @property string $no_pesanan ID Unik Pesanan bertipe String/Kode Kustom (Primary Key)
 * @property int|null $id_user ID Kasir yang memproses/mengonfirmasi pesanan (Foreign Key dari `users`)
 * @property int $id_meja ID Meja fisik tempat pemesanan dilakukan (Foreign Key dari `meja`)
 * @property string $nama_konsumen Nama pemesan/konsumen
 * @property int $total_harga Akumulasi total harga seluruh item dalam pesanan ini
 * @property string $status_pembayaran Status bayar ('belum bayar', 'menunggu', 'lunas', 'gagal')
 * @property string $status_pesanan Status progres ('menunggu konfirmasi', 'diproses', 'selesai', 'dibatalkan')
 * @property string|null $catatan_pesanan Catatan umum global untuk pesanan
 * @property string|null $midtrans_transaction_id ID transaksi eksternal dari sistem Midtrans
 * @property string|null $qr_code Konten string gambar QR untuk scan bayar
 * @property string|null $qr_url Tautan URL QR Code pembayaran instan dari Midtrans/Xendit
 * @property \Illuminate\Support\Carbon|null $tgl_pembayaran Tanggal dan waktu pelunasan transaksi dilakukan
 */
class Pesanan extends Model
{
    /**
     * Nama tabel database yang dikaitkan dengan model ini.
     *
     * @var string
     */
    protected $table = 'pesanan';

    /**
     * Nama kolom kunci primer (Primary Key) di tabel.
     * Menggunakan 'no_pesanan' (kode unik string) alih-alih ID auto-increment biasa.
     *
     * @var string
     */
    protected $primaryKey = 'no_pesanan';

    /**
     * Menentukan jenis tipe data kunci primer.
     * Diberi nilai 'string' karena primary key menggunakan kode transaksi unik alfabetik.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Menonaktifkan sistem auto-incrementing kunci primer.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Menonaktifkan penambahan timestamp otomatis (`created_at` & `updated_at`).
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
        'no_pesanan',              // Kode transaksi unik, misal: KOH-260512-XYZ
        'id_user',                 // ID staf kasir pembantu transaksi (nullable)
        'id_meja',                 // Referensi meja fisik pelanggan
        'nama_konsumen',           // Nama pelanggan
        'total_harga',             // Nilai tagihan bruto transaksi
        'status_pembayaran',       // Enum: belum bayar, menunggu, lunas, gagal
        'status_pesanan',          // Enum: menunggu konfirmasi, diproses, selesai, dibatalkan
        'catatan_pesanan',         // Catatan khusus/request pelanggan secara global
        'midtrans_transaction_id', // ID Transaksi Midtrans (nullable)
        'qr_url',                  // Tautan QR Code dari gateway (nullable)
        'tgl_pembayaran',          // Waktu transaksi lunas (nullable)
    ];

    /**
     * Mengubah format tipe data atribut database menjadi tipe data bawaan PHP secara otomatis.
     * Memastikan integrasi tanggal menggunakan instance Carbon untuk kenyamanan manipulasi tanggal.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'no_pesanan'     => 'string',   // Kode dibaca string
            'id_user'        => 'integer',  // ID Kasir dibaca integer
            'id_meja'        => 'integer',  // ID Meja dibaca integer
            'total_harga'    => 'integer',  // Nominal harga dibaca integer
            'tgl_pembayaran' => 'datetime', // Tanggal pelunasan cast ke Carbon Instance
        ];
    }

    /**
     * Hubungan Banyak-ke-Satu (Many-to-One / BelongsTo) ke model User.
     * Mengaitkan pesanan ini dengan kasir pelayan transaksi di kasir.
     * Relasi ini bersifat opsional (nullable) karena saat checkout konsumen belum ditangani kasir.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,    // Model relasi tujuan
            'id_user',      // Foreign key di tabel pesanan
            'id_users'      // Owner key pada tabel users
        );
    }

    /**
     * Hubungan Banyak-ke-Satu (Many-to-One / BelongsTo) ke model Meja.
     * Menghubungkan pesanan ke meja asal pemesan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function meja(): BelongsTo
    {
        return $this->belongsTo(
            Meja::class,    // Model relasi tujuan
            'id_meja',      // Foreign key di tabel pesanan
            'id_meja'       // Owner key pada tabel meja
        );
    }

    /**
     * Hubungan Satu-ke-Banyak (One-to-Many / HasMany) ke model DetailPesanan.
     * Menghubungkan pesanan induk ke rincian makanan/minuman yang dibeli di dalamnya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detailPesanan(): HasMany
    {
        return $this->hasMany(
            DetailPesanan::class, // Model relasi tujuan
            'no_pesanan',         // Foreign key di tabel detail_pesanan
            'no_pesanan'          // Local key di tabel pesanan
        );
    }
}
