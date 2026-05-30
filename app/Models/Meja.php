<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

    /**
     * Accessor: URL yang di-encode ke QR Code meja ini.
     * Konsumen scan QR → langsung masuk halaman menu konsumen meja ini.
     *
     * Contoh hasil: "http://192.168.1.48:8000/01"
     *
     * Base URL diambil dari config('app.qr_meja_base_url') agar bisa diganti
     * tanpa menyentuh kode (cukup ubah .env saat pindah lokasi/WiFi).
     *
     * @return string
     */
    public function getScanUrlAttribute(): string
    {
        $base = rtrim(config('app.qr_meja_base_url'), '/');

        return "{$base}/{$this->no_meja}";
    }

    /**
     * Accessor: SVG string QR Code siap ditampilkan/dicetak.
     *
     * Menggunakan format SVG karena:
     *  - Tidak butuh ekstensi PHP imagick/gd
     *  - Scalable tanpa kehilangan kualitas saat dicetak A4
     *  - Ukuran file kecil, embed langsung ke HTML
     *
     * Error correction level 'M' (~15% redundansi) — cukup untuk meja indoor
     * yang dilaminasi. Untuk lingkungan kotor/outdoor pakai 'Q' atau 'H'.
     *
     * @return string SVG markup, gunakan {!! $meja->qr_svg !!} di Blade
     */
    public function getQrSvgAttribute(): string
    {
        return QrCode::format('svg')
            ->size(280)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($this->scan_url);
    }
}
