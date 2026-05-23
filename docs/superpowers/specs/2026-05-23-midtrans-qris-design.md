# Design Spec — Midtrans QRIS Payment Gateway

**Date:** 2026-05-23
**Branch:** `feature/task2-cleanup`
**Author:** Pheterson Ferry Fernando (brainstormed with Claude Code)
**Status:** Draft — awaiting user review

---

## 1. Tujuan

Mengganti payment gateway aplikasi Kohvito-SIMPEN dari konfigurasi multi-driver (`mock` / `xendit` / `midtrans`-Snap) menjadi **single driver: Midtrans Core API QRIS**. Konsumen yang menyelesaikan checkout dari keranjang akan dibawa ke halaman pembayaran yang menampilkan QR Code dinamis (digenerate via API Midtrans), bisa diunduh sebagai PNG, dan halaman tersebut auto-deteksi perubahan status pembayaran lewat polling.

## 2. Keputusan Brainstorming

| # | Pertanyaan | Keputusan |
|---|---|---|
| 1 | Penanganan driver lama (Xendit & Mock)? | **Hapus keduanya.** Midtrans saja. |
| 2 | Kapan QR digenerate? | **Saat klik "Pesan"** di keranjang. QR sudah siap sebelum halaman pembayaran dirender. |
| 3 | Status update di halaman pembayaran? | **Auto-polling tiap 5 detik** ke endpoint backend. |
| 4 | Storage & rendering QR? | **Simpan `qr_string`** ke DB. Render SVG inline via `simplesoftwareio/simple-qrcode` (sudah terinstall). |

## 3. Arsitektur

### 3.1 Komponen yang berubah

| Komponen | Aksi |
|---|---|
| `composer.json` | Hapus `xendit/xendit-php`. Tambah `midtrans/midtrans-php`. |
| `config/services.php` | Hapus blok `xendit` & `bayar`. Pertahankan `midtrans` (sudah ada), tambah field `merchant_id`. |
| `.env.example` & `.env` | Hapus `BAYAR_DRIVER`, `XENDIT_*`. Set `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_MERCHANT_ID`, `MIDTRANS_IS_PRODUCTION=false`. |
| `app/Services/MidtransQrisService.php` | **Baru.** Encapsulate panggilan Midtrans: `charge(Pesanan $p): array`, `verifySignature(array $payload): bool`, `fetchStatus(string $orderId): array`. |
| `app/Exceptions/MidtransChargeException.php` | **Baru.** Domain exception untuk error API. |
| `app/Http/Controllers/BayarController.php` | Refactor total. Public methods: `qris()` (tampil halaman), `status()` (polling JSON), `downloadQr()` (PNG attachment), `callback()` (webhook), `regenerate()` (retry kalau gagal). |
| `app/Http/Controllers/KeranjangKonsumenController.php` | Method `storePesan` panggil `MidtransQrisService::charge()` setelah Pesanan ter-create. |
| `app/Models/Pesanan.php` | Tambah `qr_string`, `midtrans_transaction_id`, `qr_expired_at` ke `$fillable` + cast `qr_expired_at` ke datetime. |
| Migration baru | `add_midtrans_fields_to_pesanan_table.php` — tambah 3 kolom nullable. |
| `resources/views/konsumen/pembayaran.blade.php` | Render QR SVG dari `$pesanan->qr_string`. Tombol unduh = link ke endpoint download. Tambah JS polling status. Hapus referensi ke `qris-code.png` statis. |
| `routes/web.php` | Hapus `bayar.simulator*` & POST `bayar`. Tambah `GET /bayar/{noPesanan}/status`, `GET /bayar/{noPesanan}/qr-download`, `POST /bayar/{noPesanan}/regenerate`. Pertahankan `POST /bayar/callback`. |
| `bootstrap/app.php` | Pastikan `bayar/callback` di-exclude CSRF (cek state sekarang). |
| `resources/views/konsumen/bayar-simulator.blade.php` | **Hapus** file. |
| `PANDUAN-PAYMENT-GATEWAY.md` | Rewrite total → fokus Midtrans QRIS sandbox + ngrok. |

### 3.2 Service layer

`MidtransQrisService` di-inject ke controllers via constructor / `app()`. Method:

```php
class MidtransQrisService
{
    public function charge(Pesanan $pesanan): array
    {
        // POST {endpoint}/v2/charge
        // payload: payment_type=qris, transaction_details, customer_details, qris.acquirer=gopay
        // return: ['qr_string'=>..., 'transaction_id'=>..., 'expiry_time'=>...]
    }

    public function verifySignature(array $payload): bool
    {
        $expected = hash('sha512',
            $payload['order_id']
            . ($payload['status_code'] ?? '')
            . ($payload['gross_amount'] ?? '')
            . config('services.midtrans.server_key')
        );
        return hash_equals($expected, $payload['signature_key'] ?? '');
    }

    public function fetchStatus(string $orderId): array
    {
        // GET {endpoint}/v2/{orderId}/status
        // Dipakai oleh regenerate() untuk cek transaksi pending lama.
    }
}
```

Endpoint sandbox: `https://api.sandbox.midtrans.com`. Production: `https://api.midtrans.com`. Pilihan via `config('services.midtrans.is_production')`.

## 4. Database

Migration `database/migrations/2026_05_23_xxxxxx_add_midtrans_fields_to_pesanan_table.php`:

```php
Schema::table('pesanan', function (Blueprint $t) {
    $t->text('qr_string')->nullable()->after('total_harga');
    $t->string('midtrans_transaction_id', 64)->nullable()->after('qr_string');
    $t->timestamp('qr_expired_at')->nullable()->after('midtrans_transaction_id');
});
```

`down()` drop 3 kolom tersebut. Pesanan lama tetap valid karena kolom nullable.

## 5. Data Flow

### Happy path

```
Konsumen klik "Pesan" di /keranjang
        │
        ▼
KeranjangKonsumenController@storePesan
  ├─ validate nama_konsumen, catatan_pesanan
  ├─ DB::transaction:
  │    ├─ Pesanan::create(no_pesanan, total_harga, status_pembayaran='menunggu', ...)
  │    └─ DetailPesanan::create() per item
  ├─ MidtransQrisService::charge($pesanan)
  │    ├─ POST /v2/charge
  │    └─ response: { qr_string, transaction_id, expiry_time }
  ├─ $pesanan->update(qr_string, midtrans_transaction_id, qr_expired_at)
  ├─ session->forget('keranjang'); session(['no_pesanan_baru' => $no])
  └─ redirect → /pembayaran/{no_pesanan}
        │
        ▼
BayarController@qris → view konsumen/pembayaran
  ├─ Render SVG QrCode::format('svg')->size(213)->generate($pesanan->qr_string)
  ├─ Tombol Unduh → /bayar/{no}/qr-download (PNG attachment)
  └─ JS polling tiap 5s → GET /bayar/{no}/status
        │
        ▼
Konsumen scan QR (mobile banking / e-wallet / simulator sandbox)
        │
        ▼
Midtrans webhook → POST {APP_URL}/bayar/callback
  ├─ body: { order_id, status_code, gross_amount, transaction_status, signature_key }
  ├─ MidtransQrisService::verifySignature() — sha512
  ├─ if transaction_status in ['capture','settlement']:
  │    └─ Pesanan::update(status_pembayaran='lunas', status_pesanan='menunggu konfirmasi', tgl_pembayaran=now())
  └─ response 200 OK
        │
        ▼
Polling next tick: GET /bayar/{no}/status
  → JSON { status_pembayaran: "lunas", redirect: "/pesanan/{no}" }
  → JS redirect ke /pesanan/{no}
```

### Polling endpoint shape

```http
GET /bayar/PS-20260523123456-ABCD/status
→ 200 { "status_pembayaran": "menunggu", "redirect": null }
→ 200 { "status_pembayaran": "lunas",    "redirect": "/pesanan/PS-20260523123456-ABCD" }
→ 200 { "status_pembayaran": "gagal",    "redirect": null, "reason": "QR expired" }
```

Route ada di group middleware `order.status` untuk konsistensi.

## 6. Error Handling & Edge Cases

| Skenario | Penanganan |
|---|---|
| Midtrans API gagal saat charge | Pesanan tetap commit ke DB, `qr_string=NULL`. Redirect ke `/pembayaran/{no}` dengan flash `errors[bayar]`. Halaman tampilkan box error + tombol "Coba Generate Ulang". |
| Webhook signature invalid | Log warning, return 401, tidak update DB. |
| Webhook order_id tidak ada | Return 200 `{status:ignored}` (biar Midtrans stop retry). Log info. |
| Webhook `transaction_status=pending` | Ignore (200 OK). |
| Webhook `transaction_status=expire/cancel/deny/failure` | Update `status_pembayaran='gagal'`. Polling FE tampilkan popup expired. |
| Webhook double settlement (retry Midtrans) | Idempoten: kalau status sudah `lunas`, langsung return 200 tanpa update. |
| Konsumen close browser sebelum bayar | Pesanan tetap `menunggu`. Webhook tetap diproses. Konsumen kembali ke `/pesanan/{no}` via nav untuk reopen QR (tombol "Lihat QR Pembayaran"). |
| QR expired di tengah | `transaction_status=expire` masuk → status `gagal` → konsumen lihat popup di halaman pembayaran via polling. Tombol "Order Ulang" recreate keranjang dari `DetailPesanan` snapshot (opsional iterasi nanti). |
| Endpoint `/bayar/callback` ke-block CSRF | Verifikasi `bootstrap/app.php` sudah exclude path ini. |
| `APP_URL` ngrok berubah | Documented di PANDUAN: update `.env` + Payment Notification URL di dashboard Midtrans. |
| Race condition polling vs webhook | Tidak ada (polling read-only, webhook write-only, eventual consistency 5s). |

## 7. Testing & Verifikasi

### Unit / Feature tests

| Test | Lokasi | Skenario |
|---|---|---|
| `MidtransQrisServiceTest` | `tests/Unit/Services/` | Mock HTTP client. Assert payload `charge()` valid. Assert `verifySignature()` true/false sesuai. |
| `BayarControllerTest` | `tests/Feature/Konsumen/` | GET `/bayar/{no}/status` return shape benar. POST callback signature valid → DB update. Signature invalid → 401. |
| `KeranjangPesanFlowTest` | `tests/Feature/Konsumen/` | Mock service. Setelah POST `/keranjang/pesan`, Pesanan ter-create, `qr_string` tersimpan, redirect ke `/pembayaran/{no}`. |

Tests di-skip otomatis kalau `MIDTRANS_SERVER_KEY` env kosong (CI tanpa creds).

### Manual QA checklist

1. `composer remove xendit/xendit-php && composer require midtrans/midtrans-php`.
2. Set `.env`:
   ```env
   MIDTRANS_SERVER_KEY=Mid-server-Pf_bNkvvQsy89n9tSioCqRbx
   MIDTRANS_CLIENT_KEY=Mid-client-HfDPgNf2SFzxuJFD
   MIDTRANS_MERCHANT_ID=M983662601
   MIDTRANS_IS_PRODUCTION=false
   APP_URL=https://557c-118-99-64-219.ngrok-free.app
   ```
3. `php artisan migrate && php artisan config:clear`.
4. Set Payment Notification URL di dashboard sandbox Midtrans ke `{APP_URL}/bayar/callback`.
5. Buka beranda konsumen → tambah menu → keranjang → isi nama → klik **Pesan**.
6. Halaman pembayaran muncul dengan QR Code (SVG inline, 213×213).
7. Klik **Unduh QR Code** → file `qr-PS-xxx.png` terdownload.
8. Buka [simulator QRIS sandbox](https://simulator.sandbox.midtrans.com/qris/index) → input qr_string → tandai bayar.
9. Tunggu ≤5 detik di halaman pembayaran → auto-redirect ke `/pesanan/{no}` dengan status "Lunas".
10. Cek `storage/logs/laravel.log` tidak ada error.

## 8. Out of Scope (iterasi nanti)

- Countdown timer expiry QR di FE.
- Multiple acquirer (saat ini default `gopay`, padahal QRIS bersifat universal — bisa pilih `airpay shopee` dll).
- Recreate keranjang dari `DetailPesanan` saat QR expired.
- Section "Pesanan belum dibayar" di beranda saat konsumen kembali scan QR meja.
- Refund handling.
- Email/WhatsApp notif konfirmasi bayar.

## 9. Risiko & Mitigasi

| Risiko | Mitigasi |
|---|---|
| Ngrok URL berubah & lupa update dashboard | Documented step di PANDUAN; bisa dipindah ke Cloudflare Tunnel kalau stable URL dibutuhkan. |
| Midtrans sandbox down saat demo | Service exception → flash error + tombol regenerate. Tidak ada data corruption (Pesanan tetap valid). |
| QR scan gagal (resolusi rendah di mobile) | Render SVG (vector, scale tanpa loss). Tombol unduh PNG resolusi tinggi (size 600px). |
| Konsumen bayar persis saat session expired di sisi mereka | Webhook tetap masuk dari Midtrans (independen dari session). DB update tetap terjadi. |

## 10. Migration / Deployment Notes

- Migration backward-compatible (kolom nullable).
- Tidak ada data Xendit yang perlu di-purge (Pesanan lama tetap valid, kolom `tgl_pembayaran` dll tidak berubah).
- File `bayar-simulator.blade.php` & route terkait dihapus → pastikan tidak ada link aktif (sudah dicek: hanya BayarController yang reference).
- Setelah deploy, `php artisan config:clear && php artisan route:clear`.

---

**Next step:** Setelah user approve spec ini → invoke `superpowers:writing-plans` untuk generate implementation plan terperinci.
