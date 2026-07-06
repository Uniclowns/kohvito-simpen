<?php

namespace App\Traits;

use App\Models\Meja;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Trait CartSessionScope
 *
 * Menyediakan helper terpusat untuk dua kebutuhan lintas-controller konsumen:
 *
 * 1. KERANJANG PER-MEJA + SCOPE TAB
 *    Keranjang belanja tidak lagi disimpan pada satu key session global
 *    ('keranjang'), melainkan pada key ber-namespace per meja:
 *    "keranjang.{no_meja}.{scope}". Dengan begitu keranjang meja M01 dan M02 hidup
 *    berdampingan dalam satu session browser tanpa saling menimpa/menghapus.
 *
 * 2. RIWAYAT PESANAN (SESSION + COOKIE BACKUP)
 *    Setiap tracking_code pesanan yang dibuat konsumen disimpan ke dua tempat:
 *    - session('riwayat_pesanan') → cepat, tapi hilang saat session expired.
 *    - cookie 'riwayat_pesanan_kohvito' (30 hari) → jaring pengaman agar
 *      riwayat otomatis muncul lagi meski session sudah hangus.
 *
 * Konvensi penamaan meja: kita memakai ID meja numerik (id_meja) dari session
 * sebagai kunci scope, karena itulah identitas meja yang sudah dikunci saat
 * scan QR (lihat BerandaKonsumenController). Nomor meja tampilan (no_meja,
 * mis. "M01") tetap dipakai untuk URL & judul, tapi TIDAK dipakai sebagai key
 * agar aman dari perbedaan format/kapitalisasi.
 */
trait CartSessionScope
{
    /**
     * Nama cookie backup riwayat pesanan.
     */
    protected string $riwayatCookieName = 'riwayat_pesanan_kohvito';

    /**
     * Umur cookie riwayat dalam menit (30 hari).
     */
    protected int $riwayatCookieMinutes = 43200; // 60 * 24 * 30

    /**
     * Batas maksimum jumlah tracking_code yang disimpan di cookie
     * agar ukuran cookie tetap aman di bawah ~4KB.
     */
    protected int $riwayatCookieMax = 50;

    /**
     * Bangun key session keranjang dari URL konsumen.
     * Sumber utama: route {noMeja} + cart scope per-tab dari query ?s= atau input cart_scope.
     */
    protected function keranjangKey(int|string|null $idMeja = null): string
    {
        $noMeja = strtoupper((string) (request()->route('noMeja') ?: request()->input('no_meja') ?: session('id_meja_no') ?: 'guest'));
        $scope = $this->cartScope($noMeja);

        return 'keranjang.'.$noMeja.'.'.$scope;
    }

    protected function cartScope(?string $noMeja = null): string
    {
        $scope = (string) (request()->input('cart_scope') ?: request()->query('s') ?: '');
        if ($scope === '') {
            abort(422, 'Scope keranjang tidak ditemukan.');
        }

        $noMeja = strtoupper((string) ($noMeja ?: request()->route('noMeja') ?: request()->input('no_meja') ?: session('id_meja_no')));
        if ($noMeja !== '' && ! str_starts_with($scope, $noMeja.'_')) {
            abort(403, 'Scope keranjang tidak sesuai dengan nomor meja.');
        }

        return $scope;
    }

    protected function cartScopeQuery(): array
    {
        return ['s' => $this->cartScope()];
    }

    protected function scopedRoute(string $name, array $params = []): string
    {
        return route($name, array_merge([
            'noMeja' => request()->route('noMeja') ?: session('id_meja_no'),
            's' => $this->cartScope(),
        ], $params));
    }

    /**
     * Tentukan id_meja "aktif" untuk operasi keranjang secara ANDAL.
     *
     * PENTING: satu browser hanya punya SATU session server (SESSION_DRIVER=database),
     * sehingga session('id_meja') bisa "berpindah" saat user membuka beberapa meja di
     * tab berbeda. Karena itu operasi keranjang WAJIB mengutamakan identitas meja yang
     * dikirim eksplisit oleh request (hidden field 'no_meja' / 'id_meja' dari halaman
     * meja yang bersangkutan), bukan sekadar mengandalkan session global.
     *
     * Urutan prioritas:
     *   1. request('id_meja')  — id numerik meja (paling pasti).
     *   2. request('no_meja')  — nomor meja (mis. "M01") → di-resolve ke id_meja via DB.
     *   3. session('id_meja')  — fallback backward-compat.
     */
    protected function resolveIdMeja(): int|string|null
    {
        $req = request();

        // 1. id_meja eksplisit dari request.
        $idMeja = $req->input('id_meja');
        if ($idMeja !== null && $idMeja !== '') {
            return $idMeja;
        }

        // 2. no_meja dari URL/field eksplisit → resolve ke id_meja.
        $noMeja = $req->route('noMeja') ?: $req->input('no_meja');
        if ($noMeja !== null && $noMeja !== '') {
            $meja = Meja::firstWhere('no_meja', $noMeja);
            if ($meja) {
                return $meja->id_meja;
            }
        }

        // 3. Fallback ke session (kompatibilitas alur lama).
        return session('id_meja');
    }

    /**
     * Ambil isi keranjang meja aktif (atau meja tertentu).
     *
     * Tanpa argumen, meja ditentukan via resolveIdMeja() (request → session fallback).
     *
     * @return array<string, array<string, mixed>>
     */
    protected function getKeranjang(int|string|null $idMeja = null): array
    {
        return session($this->keranjangKey($idMeja ?? $this->resolveIdMeja()), []);
    }

    /**
     * Simpan isi keranjang untuk meja aktif (atau meja tertentu).
     *
     * @param  array<string, array<string, mixed>>  $keranjang
     */
    protected function putKeranjang(array $keranjang, int|string|null $idMeja = null): void
    {
        session([$this->keranjangKey($idMeja ?? $this->resolveIdMeja()) => $keranjang]);
    }

    /**
     * Hapus HANYA keranjang meja aktif (atau meja tertentu).
     * TIDAK menyentuh keranjang meja lain maupun data session lain.
     */
    protected function forgetKeranjang(int|string|null $idMeja = null): void
    {
        session()->forget($this->keranjangKey($idMeja ?? $this->resolveIdMeja()));
    }

    /**
     * Validasi format kode pelacakan publik ("KV-" + 5 alfanumerik kapital).
     * Dipakai sebagai gerbang sebelum menyentuh database agar endpoint publik
     * tidak bisa dipakai menebak/enumerasi nilai lain.
     */
    protected function isValidTrackingCode(string $kode): bool
    {
        return (bool) preg_match('/^KV-[A-Z0-9]{5}$/', $kode);
    }

    /**
     * Catat sebuah tracking_code ke riwayat session konsumen.
     */
    protected function pushRiwayatSession(string $trackingCode): void
    {
        $riwayat = array_merge(session('riwayat_pesanan', []), [$trackingCode]);

        session(['riwayat_pesanan' => array_values(array_unique($riwayat))]);
    }

    /**
     * Bangun objek cookie riwayat pesanan yang sudah ditambahi tracking_code baru.
     *
     * Cookie berisi JSON array of tracking_code, dibatasi N kode terakhir.
     * Pemanggil bertanggung jawab menyertakan cookie ini ke response via
     * ->withCookie($cookie).
     */
    protected function buildRiwayatCookie(string $trackingCode): Cookie
    {
        $riwayat = array_merge($this->decodeRiwayatCookie(), [$trackingCode]);

        // Batasi hanya N kode terakhir agar cookie tidak membengkak.
        $riwayat = array_slice(array_values(array_unique($riwayat)), -$this->riwayatCookieMax);

        return cookie(
            $this->riwayatCookieName,
            json_encode($riwayat),
            $this->riwayatCookieMinutes
        );
    }

    /**
     * Baca & decode isi cookie riwayat pesanan menjadi array tracking_code.
     *
     * @return array<int, string>
     */
    protected function decodeRiwayatCookie(): array
    {
        $raw = request()->cookie($this->riwayatCookieName, '[]');
        $decoded = json_decode((string) $raw, true);

        return is_array($decoded) ? array_values(array_filter($decoded, 'is_string')) : [];
    }

    /**
     * Gabungkan riwayat dari cookie ke dalam session.
     *
     * Dipanggil saat konsumen masuk beranda (scan QR ulang). Bila session
     * sudah hilang tapi cookie masih ada, riwayat pesanan otomatis pulih.
     */
    protected function restoreRiwayatDariCookie(): void
    {
        $cookieRiwayat = $this->decodeRiwayatCookie();

        if (empty($cookieRiwayat)) {
            return;
        }

        $sessionRiwayat = session('riwayat_pesanan', []);
        $gabungan = array_values(array_unique(array_merge($sessionRiwayat, $cookieRiwayat)));

        session(['riwayat_pesanan' => $gabungan]);
    }
}
