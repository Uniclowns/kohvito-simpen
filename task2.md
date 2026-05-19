# Task 2 — Kasir UI Revisi (PM Plan)

Project Manager: AI Assistant
Audience: Senior Programmer / AI Implementer
Branch: `feature/task2-cleanup`
Issues closed: #31, #33

---

## 1. Konteks

`revisi.md` minta:
1. Implementasi 10 desain Figma (`702-18983`, `648-10937`, `702-17840`, `560-14648`, `560-14682`, `560-14715`, `554-8856`, `554-8950`, `560-13454`, `560-13455`).
2. Implementasi 1 desain Figma tambahan (`597-27188`).
3. Naikkan ukuran font dan spacing pada UI Kasir (1–2 step lebih besar) supaya tidak terlihat terlalu kecil.
4. Tutup GitHub Issues #31 (Dashboard Kasir) dan #33 (Kelola Pesanan Kasir) lewat satu Pull Request.

## 2. Pemetaan Desain → File

| Figma node | Bagian UI | File yang diubah |
|------------|-----------|------------------|
| 702-18983 | Kelola Pesanan (grid + Detail modal, status Processing) | `resources/views/kasir/kelola-pesanan.blade.php` |
| 648-10937 | Kelola Pesanan (grid + Detail modal, status Confirmed) | `resources/views/kasir/kelola-pesanan.blade.php` |
| 702-17840 | Kelola Pesanan (grid saja) | `resources/views/kasir/kelola-pesanan.blade.php` |
| 560-14648 | Popup "Berhasil Menerima Pesanan" | `resources/views/kasir/kelola-pesanan.blade.php` (`#kasir-order-success`) |
| 560-14682 | Popup "Berhasil Mencetak Struk" | `resources/views/kasir/kelola-pesanan.blade.php` (`#kasir-order-success`) |
| 560-14715 | Popup "Berhasil Menyelesaikan Pesanan" | `resources/views/kasir/kelola-pesanan.blade.php` (`#kasir-order-success`) |
| 554-8856  | Detail modal varian dim-backdrop (Processing) | `resources/views/kasir/kelola-pesanan.blade.php` (backdrop `bg-black/35`) |
| 554-8950  | Detail modal varian dim-backdrop (Confirmed) | `resources/views/kasir/kelola-pesanan.blade.php` |
| 560-13454 | Detail modal varian terang (Processing) | `resources/views/kasir/kelola-pesanan.blade.php` |
| 560-13455 | Detail modal varian terang (Confirmed) | `resources/views/kasir/kelola-pesanan.blade.php` |
| 597-27188 | Histori Pesanan (tabel + tombol Cetak Semua) | `resources/views/kasir/histori-pesanan.blade.php` |

## 3. Tahapan Implementasi

### Tahap 1 — Konsolidasi struktur (tanpa style)
- Pastikan `kelola-pesanan.blade.php` tetap memuat: grid kartu, modal detail (2 varian tombol), dan modal sukses (3 varian copy).
- Pastikan `histori-pesanan.blade.php` tetap memuat: tombol Cetak Semua Histori Pesanan, search bar, tabel histori, dan modal detail histori.
- File `kelola-pesanan-detail.blade.php` saat ini berisi placeholder UI lama. Karena flow utama sudah lewat modal, hapus konten placeholder dan arahkan kembali ke index modal pattern (atau biarkan minimal redirect).

### Tahap 2 — Naikkan font size & spacing (Kasir-wide)
Naikkan satu-dua step ukuran token Tailwind:

| Lama | Baru |
|------|------|
| `text-[10px]` | `text-[12px]` |
| `text-[11px]` | `text-[13px]` |
| `text-[12px]` | `text-[14px]` |
| `text-[14px]` | `text-[16px]` |
| `text-[18px]` | `text-[22px]` |
| `text-[20px]` | `text-[24px]` |
| `text-[22px]` | `text-[26px]` |
| `text-[24px]` | `text-[28px]` |
| `text-[26px]` | `text-[30px]` |
| `text-3xl` | `text-4xl` |
| `text-[36px]` | `text-[42px]` |

Padding/gap juga dinaikkan:

| Lama | Baru |
|------|------|
| `p-3` | `p-4` |
| `p-6` | `p-8` |
| `py-1.5` | `py-2.5` |
| `py-2` | `py-3` |
| `py-3` | `py-4` |
| `py-6` | `py-8` |
| `px-3` | `px-4` |
| `px-6` | `px-8` |
| `gap-2` | `gap-3` |
| `gap-5` | `gap-6` |
| `mb-4` | `mb-6` |
| `mb-5` | `mb-7` |

Berlaku untuk:
- `resources/views/kasir/beranda.blade.php`
- `resources/views/kasir/kelola-pesanan.blade.php`
- `resources/views/kasir/histori-pesanan.blade.php`
- `resources/views/kasir/histori-pesanan-detail.blade.php`
- `resources/views/components/layouts/kasir.blade.php` (header)

### Tahap 3 — Polish Detail Modal sesuai Figma 560-13454/13455
- Susun tiap baris item: gambar 60×60 rounded, judul + variant + extras dalam kolom kiri; harga utama + add-on `+2.000` dalam kolom kanan rata-kanan.
- "Scroll Untuk Melihat Menu Lainnya" tetap muncul jika `count() > 4`.

### Tahap 4 — Validasi terhadap Issues
- Issue #31 (Dashboard Kasir): cek `beranda.blade.php` memuat KPI pesanan dan link cepat (sidebar sudah menyediakan).
- Issue #33 (Kelola Pesanan): cek tombol Detail / Terima Pesanan / Selesai / Cetak Struk berfungsi (controller `KelolaPesananController::updateStatus`, `cetakPesanan`).

### Tahap 5 — Commit & PR
1. `git add` file Kasir terkait.
2. Commit message: `feat(kasir): bump typography & spacing, finalize Figma task2 designs`.
3. Push ke `feature/task2-cleanup`.
4. Buka PR dengan body yang mencantumkan `Closes #31`, `Closes #33`.

## 4. Kriteria Selesai
- 11 desain Figma tampak 1:1 di browser pada lebar viewport 1280px.
- Font dan spacing terasa lebih nyaman dibaca (tidak kecil).
- Tombol Terima Pesanan / Selesai / Cetak Struk memicu modal sukses yang tepat.
- Issue #31 dan #33 ter-close oleh PR.
