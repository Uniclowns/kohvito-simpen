<?php

namespace App\Models;

use Database\Factories\MenuFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Menu
 * 
 * Model ini mewakili entitas menu makanan/minuman di database (tabel `menu`).
 * Mengelola informasi detail hidangan, ketersediaan stok, harga jual, komposisi bahan,
 * kategori rasa, dan jenis penyajian (Makanan/Minuman).
 *
 * @package App\Models
 * @property int $id_menu ID Unik Menu (Primary Key)
 * @property string $nama_menu Nama hidangan/minuman (misal: "Nasi Goreng Kampung")
 * @property string|null $deskripsi Ulasan deskriptif mengenai menu terkait
 * @property int $harga Nilai nominal rupiah harga jual menu
 * @property int $stock Kuantitas sisa stok menu yang tersedia di dapur
 * @property string|null $komposisi Bahan penyusun menu (ditampilkan di detail modal konsumen)
 * @property string|null $gambar_menu Nama file gambar menu (disimpan di direktori public/images)
 * @property string $status_ketersediaan Status stok menu ('Tersedia' atau 'Habis')
 * @property string $jenis_menu Klasifikasi produk ('Makanan' atau 'Minuman')
 * @property string|null $kategori_makanan Kategori makanan (misal: 'Nasi', 'Mie', 'Cemilan')
 * @property string|null $tipe_minuman Kategori minuman (misal: 'Kopi', 'Teh', 'Susu')
 */
class Menu extends Model
{
    /** @use HasFactory<MenuFactory> */
    use HasFactory;

    /**
     * Nama tabel database yang diwakili oleh model Menu.
     *
     * @var string
     */
    protected $table = 'menu';

    /**
     * Kunci primer (Primary Key) dari tabel menu.
     *
     * @var string
     */
    protected $primaryKey = 'id_menu';

    /**
     * Menandakan apakah primary key di tabel ini bersifat auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Menonaktifkan timestamps `created_at` dan `updated_at`.
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
        'nama_menu',           // Nama item menu
        'deskripsi',           // Deskripsi rasa/porsi
        'harga',               // Harga jual satuan rupiah
        'stock',               // Stok porsi/gelas tersedia
        'komposisi',           // Komposisi bahan baku produk
        'gambar_menu',         // Nama file gambar produk
        'status_ketersediaan', // Enum: Tersedia, Habis
        'jenis_menu',          // Enum: Makanan, Minuman
        'kategori_makanan',    // Kategori spesifik makanan (misal: Pedas, Dingin)
        'tipe_minuman',        // Tipe penyajian minuman (misal: Panas, Dingin)
    ];

    /**
     * Cast otomatis tipe data dari database saat dibaca oleh sistem.
     * Menjaga konsistensi kalkulasi matematis agar tidak terjadi penanganan string-numeric error.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id_menu'             => 'integer', // ID Menu wajib integer
            'harga'               => 'integer', // Harga wajib bertipe integer untuk kalkulasi total
            'stock'               => 'integer', // Jumlah stok wajib integer untuk pengurangan stock-control
            'status_ketersediaan' => 'string',  // Status dibaca sebagai string
            'jenis_menu'          => 'string',  // Jenis menu dibaca sebagai string
        ];
    }

    /**
     * Hubungan Banyak-ke-Banyak (Many-to-Many) ke model KategoriMenu.
     * Digunakan untuk menghubungkan menu ke banyak kategori rasa atau penomoran
     * melalui perantara tabel pivot `menu_kategori`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function kategoris(): BelongsToMany
    {
        return $this->belongsToMany(
            KategoriMenu::class,  // Model tujuan
            'menu_kategori',      // Nama tabel pivot
            'id_menu',            // Foreign key tabel pivot untuk model Menu
            'id_kategori'         // Foreign key tabel pivot untuk model KategoriMenu
        );
    }

    /**
     * Hubungan Satu-ke-Banyak (One-to-Many) ke model DetailPesanan.
     * Memungkinkan penelusuran histori pemesanan produk menu ini di setiap transaksi.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detailPesanan(): HasMany
    {
        return $this->hasMany(
            DetailPesanan::class, // Model relasi tujuan
            'id_menu',            // Foreign key di tabel detail_pesanan
            'id_menu'             // Local key di tabel menu
        );
    }
}
