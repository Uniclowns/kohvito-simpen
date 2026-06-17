# Entity Relationship Diagram (ERD) — Kohvito Simpen

Sistem pemesanan & kasir kafe (QR table ordering + POS). Dokumen ini
men-dokumentasikan skema database hasil rekonstruksi dari migrations
(`database/migrations`) dan model Eloquent (`app/Models`).

> Tabel framework Laravel (`cache`, `jobs`, `sessions`, `personal_access_tokens`)
> sengaja **tidak** dimasukkan ke ERD domain karena tidak menyimpan data bisnis.
> Catatan: `personal_access_tokens` dipakai Laravel Sanctum untuk autentikasi API.

## Diagram (Mermaid)

```mermaid
erDiagram
    role            ||--o{ users          : "memiliki"
    users           |o--o{ pesanan        : "memproses (kasir, opsional)"
    meja            ||--o{ pesanan        : "tempat pemesanan"
    pesanan         ||--o{ detail_pesanan : "berisi item"
    menu            ||--o{ detail_pesanan : "dipesan dalam"
    menu            ||--o{ menu_kategori  : "diklasifikasikan"
    kategori_menu   ||--o{ menu_kategori  : "mengelompokkan"

    role {
        bigint id_role PK "auto-increment"
        string nama_role
    }

    users {
        bigint id_users     PK "auto-increment"
        bigint id_role      FK "-> role.id_role"
        string nama_lengkap
        string username     UK "unique"
        string password         "hashed"
    }

    meja {
        bigint id_meja PK "auto-increment"
        string no_meja
        string qr_code
    }

    kategori_menu {
        bigint id_kategori   PK "auto-increment"
        string nama_kategori
    }

    menu {
        bigint  id_menu             PK "auto-increment"
        string  nama_menu
        text    deskripsi
        int     harga
        string  gambar_menu             "nullable"
        enum    status_ketersediaan     "Tersedia | Tidak Tersedia"
        enum    jenis_menu              "Makanan | Minuman"
        enum    kategori_makanan        "Pedas | Tidak Pedas, nullable"
        enum    tipe_minuman            "Panas | Dingin | Keduanya, nullable"
        text    komposisi               "nullable"
        int     stock                   "unsigned, default 0"
    }

    menu_kategori {
        bigint id_menu     PK "FK -> menu.id_menu, cascade"
        bigint id_kategori PK "FK -> kategori_menu.id_kategori, cascade"
    }

    pesanan {
        string   no_pesanan              PK "kode unik (string)"
        bigint   id_user                 FK "-> users.id_users, nullable"
        bigint   id_meja                 FK "-> meja.id_meja"
        string   nama_konsumen
        int      total_harga
        enum     status_pembayaran          "menunggu | lunas"
        enum     status_pesanan             "menunggu konfirmasi | diproses | selesai"
        text     catatan_pesanan            "nullable"
        string   midtrans_transaction_id    "nullable"
        string   qr_url                     "nullable"
        datetime tgl_pembayaran             "nullable"
    }

    detail_pesanan {
        bigint id_detail  PK "auto-increment"
        string no_pesanan FK "-> pesanan.no_pesanan"
        bigint id_menu    FK "-> menu.id_menu"
        int    jumlah
        string catatan       "nullable"
        int    subtotal
    }
```

## Ringkasan relasi

| Relasi | Kardinalitas | Keterangan |
|---|---|---|
| `role` → `users` | 1 : N | Satu peran (Admin/Kasir/Super Admin) dipakai banyak user. |
| `users` → `pesanan` | 0..1 : N | Kasir yang memproses pesanan. `id_user` nullable — pesanan checkout konsumen belum tentu ditangani kasir. |
| `meja` → `pesanan` | 1 : N | Satu meja menjadi asal banyak pesanan (dine-in via QR). |
| `pesanan` → `detail_pesanan` | 1 : N | Header pesanan memiliki banyak baris item. |
| `menu` → `detail_pesanan` | 1 : N | Satu menu muncul di banyak baris pesanan. |
| `menu` ↔ `kategori_menu` | M : N | Lewat tabel pivot `menu_kategori` (composite PK, cascade delete). |

## Catatan desain skema

- **Primary key non-standar.** Hampir semua tabel memakai PK custom (`id_role`,
  `id_menu`, dst.) alih-alih `id`. `pesanan` memakai PK **string** `no_pesanan`
  (kode transaksi unik, mis. `KOH-260512-XYZ`), bukan auto-increment.
- **Tanpa timestamps.** Seluruh model meng-set `$timestamps = false`; tidak ada
  kolom `created_at` / `updated_at`. Waktu transaksi dilacak via `tgl_pembayaran`.
- **Riwayat migrasi kategori.** Awalnya `menu` punya kolom `id_kategori` (FK
  langsung, 1:N). Sejak `2026_05_16` direfaktor menjadi M:N: kolom di-`drop` dan
  diganti pivot `menu_kategori`.
- **Integrasi pembayaran.** Kolom `midtrans_transaction_id` & `qr_url` di `pesanan`
  untuk gateway QRIS (Midtrans).
- **`password` di-hash otomatis** lewat cast `'hashed'` pada model `User`.
