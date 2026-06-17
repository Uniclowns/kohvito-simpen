<?php

namespace App\Services;

use App\Models\Pesanan;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Class ThermalReceiptPrinter
 *
 * Mencetak struk pesanan ke printer termal jaringan (ESC/POS) secara langsung.
 * Layanan ini punya satu tanggung jawab: menyusun byte ESC/POS dari sebuah
 * objek {@see Pesanan}, lalu mengirimkannya ke printer melalui socket TCP mentah
 * (port RAW/JetDirect, umumnya 9100).
 *
 * Pemisahan menjadi dua tahap memudahkan pengujian:
 *  - {@see buildReceipt()} murni (tanpa I/O) sehingga dapat diuji unit.
 *  - {@see print()} menggabungkan penyusunan struk dengan pengiriman socket.
 *
 * @package App\Services
 */
class ThermalReceiptPrinter
{
    /** Kode kontrol ESC (Escape) ESC/POS. */
    private const ESC = "\x1B";

    /** Kode kontrol GS (Group Separator) ESC/POS. */
    private const GS = "\x1D";

    /**
     * @param  string  $ip       Alamat IP printer jaringan.
     * @param  int     $port     Port cetak mentah (RAW/JetDirect).
     * @param  int     $timeout  Batas waktu koneksi/tulis socket (detik).
     * @param  int     $width    Lebar kertas dalam karakter (Font A).
     */
    public function __construct(
        private readonly string $ip,
        private readonly int $port,
        private readonly int $timeout = 5,
        private readonly int $width = 42,
    ) {
    }

    /**
     * Menyusun struk lalu mengirimkannya ke printer.
     *
     * @param  \App\Models\Pesanan  $pesanan
     * @return void
     *
     * @throws \RuntimeException Jika printer tidak dapat dihubungi.
     */
    public function print(Pesanan $pesanan): void
    {
        $this->send($this->buildDocument($pesanan));
    }

    /**
     * Menyusun seluruh dokumen cetak untuk satu pesanan: struk pelanggan diikuti
     * Checker internal Barista (minuman) dan Kitchen (makanan) bila ada itemnya.
     *
     * Disusun sebagai satu rangkaian byte sehingga seluruh lembar terkirim dalam
     * satu koneksi socket; tiap lembar diakhiri perintah potong (full cut) sendiri.
     *
     * Fungsi murni tanpa I/O — aman diuji unit.
     *
     * @param  \App\Models\Pesanan  $pesanan
     * @return string  Rangkaian byte ESC/POS untuk struk + checker.
     */
    public function buildDocument(Pesanan $pesanan): string
    {
        $out = $this->buildReceipt($pesanan);

        // Pemisahan kategori mengikuti aturan yang sama dengan tampilan cetak HTML:
        // Minuman -> Checker Barista, Makanan -> Checker Kitchen.
        $drinks = $pesanan->detailPesanan
            ->filter(fn ($d) => ($d->menu?->jenis_menu) === 'Minuman')
            ->values();
        $foods = $pesanan->detailPesanan
            ->filter(fn ($d) => ($d->menu?->jenis_menu) === 'Makanan')
            ->values();

        if ($drinks->isNotEmpty()) {
            $out .= $this->buildChecker($pesanan, 'BARISTA', 'MINUMAN', $drinks);
        }

        if ($foods->isNotEmpty()) {
            $out .= $this->buildChecker($pesanan, 'KITCHEN', 'MAKANAN', $foods);
        }

        return $out;
    }

    /**
     * Menyusun satu lembar Checker internal (Barista / Kitchen).
     *
     * Checker adalah cetakan operasional, BUKAN untuk pelanggan: tanpa harga,
     * diskon, atau total pembayaran — hanya nama menu, jumlah, varian, dan catatan
     * agar tim dapur/bar cepat membaca.
     *
     * @param  \App\Models\Pesanan  $pesanan
     * @param  string  $type      Judul checker: 'BARISTA' | 'KITCHEN'.
     * @param  string  $kategori  Label kategori: 'MINUMAN' | 'MAKANAN'.
     * @param  \Illuminate\Support\Collection  $items  Item kategori ini saja.
     * @return string  Rangkaian byte ESC/POS satu lembar checker.
     */
    public function buildChecker(Pesanan $pesanan, string $type, string $kategori, Collection $items): string
    {
        $waktu = $pesanan->tgl_pembayaran?->format('d-m-Y H:i')
            ?? now()->format('d-m-Y H:i');

        $out = self::ESC . '@'; // Reset state untuk lembar baru

        // ---- Judul checker ----
        $out .= self::ESC . 'a' . "\x01";            // rata tengah
        $out .= self::GS . '!' . "\x01";             // tinggi ganda
        $out .= self::ESC . 'E' . "\x01";            // tebal
        $out .= 'CHECKER ' . $type . "\n";
        $out .= self::ESC . 'E' . "\x00";            // tebal mati
        $out .= self::GS . '!' . "\x00";             // ukuran normal
        $out .= self::ESC . 'a' . "\x00";            // rata kiri
        $out .= $this->divider();

        // ---- Info operasional (tanpa harga) ----
        $out .= $this->infoLine('No Order', (string) $pesanan->no_pesanan);
        $out .= $this->infoLine('Tanggal', $waktu);
        $out .= $this->infoLine('Meja', (string) ($pesanan->meja?->no_meja ?? 'Quick Service'));
        $out .= $this->infoLine('Customer', (string) ($pesanan->nama_konsumen ?? '-'));
        $out .= $this->infoLine('Purpose', 'DINE IN');
        $out .= $this->divider();

        // ---- Label kategori ----
        $out .= self::ESC . 'E' . "\x01";            // tebal
        $out .= strtoupper($kategori) . "\n";
        $out .= self::ESC . 'E' . "\x00";

        // ---- Daftar item + varian/catatan (tanpa harga) ----
        foreach ($items as $item) {
            $nama = $item->menu?->nama_menu ?? 'Menu';

            $out .= self::ESC . 'E' . "\x01";        // tebal
            $out .= ((int) $item->jumlah) . 'x ' . $this->clean($nama) . "\n";
            $out .= self::ESC . 'E' . "\x00";

            foreach ($this->splitCatatan((string) ($item->catatan ?? '')) as [$label, $value]) {
                $baris = $value !== '' ? $label . ': ' . $value : $label;
                $out .= $this->wrap('  ' . $baris);
            }
        }
        $out .= $this->divider();

        // ---- Total item kategori ----
        $out .= self::ESC . 'E' . "\x01";            // tebal
        $out .= 'TOTAL ITEM ' . strtoupper($kategori) . ': ' . (int) $items->sum('jumlah') . "\n";
        $out .= self::ESC . 'E' . "\x00";

        // ---- Umpan kertas + potong ----
        $out .= "\n\n\n";
        $out .= self::GS . 'V' . "\x00";             // potong penuh (full cut)

        return $out;
    }

    /**
     * Memecah field catatan berpola "Label: Value | Label: Value | Catatan ..."
     * menjadi pasangan [label, value]. Segmen tanpa ':' dikembalikan apa adanya
     * sebagai [teks, ''].
     *
     * @param  string  $catatan
     * @return array<int, array{0: string, 1: string}>
     */
    private function splitCatatan(string $catatan): array
    {
        $segments = array_filter(
            array_map('trim', explode('|', $catatan)),
            static fn ($s) => $s !== ''
        );

        $pairs = [];
        foreach ($segments as $seg) {
            $pos = strpos($seg, ':');

            $pairs[] = $pos !== false
                ? [trim(substr($seg, 0, $pos)), trim(substr($seg, $pos + 1))]
                : [$seg, ''];
        }

        return $pairs;
    }

    /**
     * Menyusun seluruh byte ESC/POS untuk satu struk pesanan.
     *
     * Fungsi murni tanpa efek samping I/O — aman dipanggil dalam pengujian
     * untuk memverifikasi isi struk tanpa printer fisik.
     *
     * @param  \App\Models\Pesanan  $pesanan
     * @return string  Rangkaian byte ESC/POS siap kirim.
     */
    public function buildReceipt(Pesanan $pesanan): string
    {
        $waktu = $pesanan->tgl_pembayaran?->translatedFormat('d M Y H:i')
            ?? now()->translatedFormat('d M Y H:i');

        $out = self::ESC . '@'; // Inisialisasi printer (reset state)

        // ---- Kepala struk ----
        $out .= self::ESC . 'a' . "\x01";            // rata tengah
        $out .= self::GS . '!' . "\x11";             // ukuran ganda (lebar+tinggi)
        $out .= "KAFE KOHVITO\n";
        $out .= self::GS . '!' . "\x00";             // ukuran normal
        $out .= "Struk Pesanan\n";
        $out .= self::ESC . 'a' . "\x00";            // rata kiri
        $out .= $this->divider();

        // ---- Informasi pesanan ----
        $out .= $this->infoLine('No Pesanan', (string) $pesanan->no_pesanan);
        $out .= $this->infoLine('Meja', (string) ($pesanan->meja?->no_meja ?? '-'));
        $out .= $this->infoLine('Konsumen', (string) ($pesanan->nama_konsumen ?? '-'));
        $out .= $this->infoLine('Waktu', $waktu);
        $out .= $this->infoLine('Bayar', strtoupper((string) ($pesanan->status_pembayaran ?? '-')));
        $out .= $this->divider();

        // ---- Daftar item ----
        foreach ($pesanan->detailPesanan as $detail) {
            $nama = $detail->menu?->nama_menu ?? 'Menu';
            $kiri = ((int) $detail->jumlah) . 'x ' . $nama;
            $out .= $this->twoCol($kiri, $this->rupiah((int) $detail->subtotal));

            $catatan = $this->clean((string) ($detail->catatan ?? ''));
            if ($catatan !== '') {
                $out .= $this->wrap('  - ' . $catatan);
            }
        }
        $out .= $this->divider();

        // ---- Total (sesuai nilai tersimpan / yang dibayar) ----
        $out .= self::ESC . 'E' . "\x01";            // tebal
        $out .= $this->twoCol('TOTAL', $this->rupiah((int) $pesanan->total_harga));
        $out .= self::ESC . 'E' . "\x00";            // tebal mati
        $out .= $this->divider();

        // ---- Catatan global (opsional) ----
        $catatanPesanan = $this->clean((string) ($pesanan->catatan_pesanan ?? ''));
        if ($catatanPesanan !== '') {
            $out .= "Catatan:\n";
            $out .= $this->wrap($catatanPesanan);
            $out .= $this->divider();
        }

        // ---- Kaki struk + potong kertas ----
        $out .= self::ESC . 'a' . "\x01";            // rata tengah
        $out .= "Terima kasih!\n";
        $out .= "\n\n\n";                            // umpan kertas sebelum potong
        $out .= self::GS . 'V' . "\x00";             // potong penuh (full cut)

        return $out;
    }

    /**
     * Mengirim byte mentah ke printer melalui socket TCP.
     *
     * @param  string  $payload
     * @return void
     *
     * @throws \RuntimeException Jika koneksi ke printer gagal.
     */
    private function send(string $payload): void
    {
        $socket = @fsockopen($this->ip, $this->port, $errno, $errstr, $this->timeout);

        if ($socket === false) {
            throw new RuntimeException(
                "Tidak dapat terhubung ke printer {$this->ip}:{$this->port} — [{$errno}] {$errstr}"
            );
        }

        stream_set_timeout($socket, $this->timeout);
        fwrite($socket, $payload);
        fflush($socket);
        fclose($socket);
    }

    /**
     * Garis pemisah selebar kertas.
     *
     * @return string
     */
    private function divider(): string
    {
        return str_repeat('-', $this->width) . "\n";
    }

    /**
     * Baris "Label : Nilai" dengan label berlebar tetap agar rapi.
     *
     * @param  string  $label
     * @param  string  $value
     * @return string
     */
    private function infoLine(string $label, string $value): string
    {
        return str_pad($label, 10) . ': ' . $this->clean($value) . "\n";
    }

    /**
     * Baris dua kolom: teks kiri dan nilai kanan yang rata kanan.
     * Bila teks kiri terlalu panjang, ia dipotong agar tidak membungkus baris.
     *
     * @param  string  $left
     * @param  string  $right
     * @return string
     */
    private function twoCol(string $left, string $right): string
    {
        $left = $this->clean($left);
        $right = $this->clean($right);

        $space = max(1, $this->width - mb_strlen($right));

        if (mb_strlen($left) > $space - 1) {
            $left = mb_substr($left, 0, max(0, $space - 1));
        }

        return str_pad($left, $space) . $right . "\n";
    }

    /**
     * Membungkus teks panjang ke beberapa baris selebar kertas.
     *
     * @param  string  $text
     * @return string
     */
    private function wrap(string $text): string
    {
        return wordwrap($this->clean($text), $this->width, "\n", true) . "\n";
    }

    /**
     * Memformat nominal ke format Rupiah (mis. "Rp 36.000").
     *
     * @param  int  $amount
     * @return string
     */
    private function rupiah(int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Membersihkan teks dari karakter kontrol & merapikan spasi/baris baru
     * agar tidak mengacaukan aliran perintah ESC/POS.
     *
     * @param  string  $text
     * @return string
     */
    private function clean(string $text): string
    {
        $text = preg_replace('/[\x00-\x1F\x7F]+/', ' ', $text) ?? '';

        return trim(preg_replace('/\s+/', ' ', $text) ?? '');
    }
}
