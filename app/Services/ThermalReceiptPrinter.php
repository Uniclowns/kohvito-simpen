<?php

namespace App\Services;

use App\Models\Pesanan;
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
 */
class ThermalReceiptPrinter
{
    /** Kode kontrol ESC (Escape) ESC/POS. */
    private const ESC = "\x1B";

    /** Kode kontrol GS (Group Separator) ESC/POS. */
    private const GS = "\x1D";

    /**
     * @param  string  $ip  Alamat IP printer jaringan.
     * @param  int  $port  Port cetak mentah (RAW/JetDirect).
     * @param  int  $timeout  Batas waktu koneksi/tulis socket (detik).
     * @param  int  $width  Lebar kertas dalam karakter (Font A).
     */
    public function __construct(
        private readonly string $ip,
        private readonly int $port,
        private readonly int $timeout = 5,
        private readonly int $width = 42,
    ) {}

    /**
     * Menyusun struk lalu mengirimkannya ke printer.
     *
     *
     * @throws RuntimeException Jika printer tidak dapat dihubungi.
     */
    public function print(Pesanan $pesanan): void
    {
        $this->send($this->buildReceipt($pesanan));
    }

    /**
     * Menyusun seluruh byte ESC/POS untuk satu struk pesanan.
     *
     * Fungsi murni tanpa efek samping I/O — aman dipanggil dalam pengujian
     * untuk memverifikasi isi struk tanpa printer fisik.
     *
     * @return string Rangkaian byte ESC/POS siap kirim.
     */
    public function buildReceipt(Pesanan $pesanan): string
    {
        $waktu = $pesanan->tgl_pembayaran?->translatedFormat('d M Y H:i')
            ?? now()->translatedFormat('d M Y H:i');

        $out = self::ESC.'@'; // Inisialisasi printer (reset state)

        // ---- Kepala struk ----
        $out .= self::ESC.'a'."\x01";            // rata tengah
        $out .= self::GS.'!'."\x11";             // ukuran ganda (lebar+tinggi)
        $out .= "KAFE KOHVITO\n";
        $out .= self::GS.'!'."\x00";             // ukuran normal
        $out .= "Struk Pesanan\n";
        $out .= self::ESC.'a'."\x00";            // rata kiri
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
            $kiri = ((int) $detail->jumlah).'x '.$nama;
            $out .= $this->twoCol($kiri, $this->rupiah((int) $detail->subtotal));

            $catatan = $this->clean((string) ($detail->catatan ?? ''));
            if ($catatan !== '') {
                $out .= $this->wrap('  - '.$catatan);
            }
        }
        $out .= $this->divider();

        // ---- Total (sesuai nilai tersimpan / yang dibayar) ----
        $out .= self::ESC.'E'."\x01";            // tebal
        $out .= $this->twoCol('TOTAL', $this->rupiah((int) $pesanan->total_harga));
        $out .= self::ESC.'E'."\x00";            // tebal mati
        $out .= $this->divider();

        // ---- Catatan global (opsional) ----
        $catatanPesanan = $this->clean((string) ($pesanan->catatan_pesanan ?? ''));
        if ($catatanPesanan !== '') {
            $out .= "Catatan:\n";
            $out .= $this->wrap($catatanPesanan);
            $out .= $this->divider();
        }

        // ---- Kaki struk + potong kertas ----
        $out .= self::ESC.'a'."\x01";            // rata tengah
        $out .= "Terima kasih!\n";
        $out .= "\n\n\n";                            // umpan kertas sebelum potong
        $out .= self::GS.'V'."\x00";             // potong penuh (full cut)

        return $out;
    }

    /**
     * Mengirim byte mentah ke printer melalui socket TCP.
     *
     *
     * @throws RuntimeException Jika koneksi ke printer gagal.
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
     */
    private function divider(): string
    {
        return str_repeat('-', $this->width)."\n";
    }

    /**
     * Baris "Label : Nilai" dengan label berlebar tetap agar rapi.
     */
    private function infoLine(string $label, string $value): string
    {
        return str_pad($label, 10).': '.$this->clean($value)."\n";
    }

    /**
     * Baris dua kolom: teks kiri dan nilai kanan yang rata kanan.
     * Bila teks kiri terlalu panjang, ia dipotong agar tidak membungkus baris.
     */
    private function twoCol(string $left, string $right): string
    {
        $left = $this->clean($left);
        $right = $this->clean($right);

        $space = max(1, $this->width - mb_strlen($right));

        if (mb_strlen($left) > $space - 1) {
            $left = mb_substr($left, 0, max(0, $space - 1));
        }

        return str_pad($left, $space).$right."\n";
    }

    /**
     * Membungkus teks panjang ke beberapa baris selebar kertas.
     */
    private function wrap(string $text): string
    {
        return wordwrap($this->clean($text), $this->width, "\n", true)."\n";
    }

    /**
     * Memformat nominal ke format Rupiah (mis. "Rp 36.000").
     */
    private function rupiah(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }

    /**
     * Membersihkan teks dari karakter kontrol & merapikan spasi/baris baru
     * agar tidak mengacaukan aliran perintah ESC/POS.
     */
    private function clean(string $text): string
    {
        $text = preg_replace('/[\x00-\x1F\x7F]+/', ' ', $text) ?? '';

        return trim(preg_replace('/\s+/', ' ', $text) ?? '');
    }
}
