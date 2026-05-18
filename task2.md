# Task2 Kohvito — Cleanup Laporan Keuangan + Wire Icon Kasir Baru + Refresh Dummy Pesanan

> **Dokumen**: Project Brief untuk Senior Programmer / AI Implementer
> **Author**: Project Manager (AI)
> **Status**: Siap implementasi
> **Estimasi total**: ±1 jam 30 menit Senior Programmer (atau ~2 jam untuk AI Model lebih murah dengan verifikasi extra)
> **Branch target**: buat branch baru `feature/task2-cleanup` dari `main`

---

## 0. Original Request (User)

1. **Hapus halaman `/admin/laporan-keuangan`** — fitur ini tidak terpakai. Hapus route, controller, view, dan link sidebar-nya.
2. **Pakai icon kasir baru** — user sudah taruh 3 icon SVG baru di `public/images/icons/`. Wire ke sidebar Kasir.
3. **Buat dummy data Pesanan** — supaya UI Beranda Admin / Kelola Pesanan / Histori bisa dilihat dengan jelas.

---

## 1. Audit Kondisi Saat Ini (PM sudah lakukan, jangan ulang)

### A. Inventaris file Laporan Keuangan (Task 1)

| # | File / lokasi                                                              | Aksi          |
|---|----------------------------------------------------------------------------|---------------|
| 1 | `app/Http/Controllers/LaporanKeuanganController.php`                       | **DELETE**    |
| 2 | `resources/views/admin/laporan-keuangan.blade.php`                         | **DELETE**    |
| 3 | `resources/views/admin/laporan-keuangan-pdf.blade.php`                     | **DELETE**    |
| 4 | `routes/web.php` line 13 — `use App\Http\Controllers\LaporanKeuanganController;` | EDIT (remove) |
| 5 | `routes/web.php` line 71–74 — 3 route admin laporan-keuangan               | EDIT (remove) |
| 6 | `resources/views/components/sidebar.blade.php` line 52–62 — anchor link admin | EDIT (remove) |
| 7 | `Penjelasan.md` (kalau ada — dokumentasi historis presentasi)              | LEAVE (docs)  |

### B. Icon kasir baru yang sudah ada di `public/images/icons/`

User sudah tambah **3 icon SVG baru**:

| Filename                    | Mapping                                                  |
|-----------------------------|----------------------------------------------------------|
| `KVT ICON USER.svg`         | Beranda Kasir (replace `template.svg` generik)           |
| `pesanan icon red.svg`      | Kelola Pesanan — **state active** (icon merah di bg putih) |
| `pesanan icon white.svg`    | Kelola Pesanan — **state inactive** (icon putih di bg dark) |

**Catatan**: belum ada icon khusus untuk **Histori Pesanan** → biarkan SVG clock inline (current state).

### C. PesananSeeder existing

File: `database/seeders/PesananSeeder.php` — **already exists**, generate 100 pesanan dummy tapi distribusinya:
- Tanggal **random 0–29 hari ke belakang** → seringkali tidak ada pesanan hari ini → kartu Beranda Admin & tabel "Data Pesanan Hari Ini" kelihatan kosong.
- Tidak ada truncate → rerun = data duplikat menumpuk.
- Catatan (`catatan`) selalu `null` → UI tidak ke-test rendering dengan/tanpa catatan.

**Yang perlu diperbaiki**: distribusi terkontrol (min hari ini, min minggu ini, sisanya retro) + truncate idempoten + notes pool acak.

---

## 2. Task 1 — Hapus Halaman Laporan Keuangan

### 2.1 Tujuan
Bersihkan modul Laporan Keuangan dari codebase. Setelah selesai, sidebar admin tidak punya link "Laporan Keuangan" dan request ke `/admin/laporan-keuangan` return 404.

### 2.2 Tahapan

#### Step 1.1 — Hapus controller

```bash
rm app/Http/Controllers/LaporanKeuanganController.php
```

#### Step 1.2 — Hapus 2 view file

```bash
rm resources/views/admin/laporan-keuangan.blade.php
rm resources/views/admin/laporan-keuangan-pdf.blade.php
```

#### Step 1.3 — Edit `routes/web.php`

**(a)** Hapus import (sekitar line 13):

```php
// HAPUS
use App\Http\Controllers\LaporanKeuanganController;
```

**(b)** Hapus 3 route + comment header dalam block `Route::prefix('admin')` (sekitar line 71–74):

```php
// HAPUS BLOK INI (termasuk baris kosong sebelumnya):

    // Laporan Keuangan
    Route::get('/laporan-keuangan', [LaporanKeuanganController::class, 'index'])->name('laporan-keuangan.index');
    Route::get('/laporan-keuangan/pdf', [LaporanKeuanganController::class, 'exportPdf'])->name('laporan-keuangan.pdf');
    Route::get('/laporan-keuangan/excel', [LaporanKeuanganController::class, 'exportExcel'])->name('laporan-keuangan.excel');
```

#### Step 1.4 — Edit `resources/views/components/sidebar.blade.php`

Hapus anchor block Laporan Keuangan (di section Admin Navigation, antara link "Pengguna Kasir" dan `@else`):

```blade
{{-- HAPUS BLOK INI: --}}
<a href="{{ route('admin.laporan-keuangan.index') }}"
   class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
          {{ request()->routeIs('admin.laporan-keuangan.*') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
    <svg class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
         fill="none" stroke="currentColor" viewBox="0 0 24 24"
         style="{{ request()->routeIs('admin.laporan-keuangan.*') ? '' : 'opacity:.8' }}">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Laporan Keuangan</span>
</a>
```

#### Step 1.5 — Sweep verifikasi (zero leftover reference)

```bash
# Git Bash / PowerShell
grep -r -l -i "laporan-keuangan\|LaporanKeuangan\|laporan_keuangan" routes/ app/ resources/
```

Expected output: **kosong** (nol file). Kalau ada match selain `Penjelasan.md` → ada reference tertinggal, fix dulu.

#### Step 1.6 — Cache cleanup

```bash
php artisan route:clear
php artisan view:clear
php artisan config:clear
```

#### Step 1.7 — Manual test

1. Login admin → buka `/admin` → cek sidebar: link "Laporan Keuangan" hilang.
2. GET `http://127.0.0.1:8000/admin/laporan-keuangan` → **404** expected.
3. GET `/admin/laporan-keuangan/pdf` → 404.
4. GET `/admin/laporan-keuangan/excel` → 404.

### 2.3 Pitfall

- **JANGAN uninstall** `barryvdh/laravel-dompdf` & `rap2hpoutre/fast-excel`. Walaupun Laporan Keuangan tidak lagi pakai, package ini masih dipakai oleh:
  - `BerandaAdminController` (cetak laporan kasir PDF)
  - `KelolaPesananController` (cetak pesanan)
  - `HistoriPesananController` (cetak histori + recap)
  - `PesananController` (kuitansi konsumen)

  Verifikasi dengan: `grep -r "Pdf::loadView\|FastExcel" app/` → harus masih ada 4 controllers di atas.

### 2.4 Acceptance Criteria

- [ ] File `LaporanKeuanganController.php` tidak ada.
- [ ] 2 view `laporan-keuangan*.blade.php` tidak ada.
- [ ] `routes/web.php` tidak punya `use ... LaporanKeuanganController` dan tidak punya route `laporan-keuangan.*`.
- [ ] Sidebar admin tidak punya link "Laporan Keuangan".
- [ ] Grep `laporan-keuangan` di `routes/`, `app/`, `resources/` = 0 hits.
- [ ] Hit URL `/admin/laporan-keuangan*` return 404.
- [ ] Tidak ada Laravel exception saat render sidebar.
- [ ] Package `dompdf` & `fast-excel` masih installed (composer.json tidak diubah).

### 2.5 Estimasi: 15 menit

---

## 3. Task 2 — Wire Icon Kasir Baru

### 3.1 Tujuan
Ganti 2 icon sidebar Kasir yang masih pakai icon generik (`template.svg`, `menu icon.svg`) jadi icon custom Kohvito yang sudah user siapkan di `public/images/icons/`.

### 3.2 Strategi

Sidebar pakai pattern dual-state:
- **Single-tone icon** (mono-color file): pakai filter CSS `brightness(0)` saat aktif (bg putih), `brightness(0) invert(1)` saat inactive (bg dark).
- **Dual-color icon** (red ↔ white): swap file `*-red.svg` & `*-white.svg` tanpa filter CSS.

Mapping yang PM rekomendasikan:

| Sidebar Item     | Icon File                                       | Pattern         |
|------------------|-------------------------------------------------|-----------------|
| Beranda Kasir    | `KVT ICON USER.svg`                             | Filter brightness (mirror admin pattern) |
| Kelola Pesanan   | `pesanan icon red.svg` (active), `pesanan icon white.svg` (inactive) | Dual-file swap (no filter) |
| Histori Pesanan  | SVG clock inline (current)                      | LEAVE — tidak ada icon baru |

### 3.3 Tahapan

#### Step 2.1 — Edit Beranda Kasir di sidebar

File: `resources/views/components/sidebar.blade.php` (section Kasir Navigation, dalam `@else`)

```blade
{{-- BEFORE --}}
<img src="{{ asset('images/icons/template.svg') }}" alt=""
     class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
     style="{{ request()->routeIs('kasir.beranda') ? 'filter:brightness(0)' : 'filter:brightness(0) invert(1);opacity:.8' }}">

{{-- AFTER --}}
<img src="{{ asset('images/icons/KVT ICON USER.svg') }}" alt=""
     class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
     style="{{ request()->routeIs('kasir.beranda') ? 'filter:brightness(0)' : 'filter:brightness(0) invert(1);opacity:.8' }}">
```

**Hanya 1 atribut yang berubah:** value `src` dari `template.svg` → `KVT ICON USER.svg`. Filter brightness style **tetap**.

#### Step 2.2 — Edit Kelola Pesanan di sidebar (dual-file swap)

File: `resources/views/components/sidebar.blade.php` (section Kasir Navigation)

```blade
{{-- BEFORE --}}
<a href="{{ route('kasir.pesanan.index') }}"
   class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
          {{ request()->routeIs('kasir.pesanan.*') ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
    <img src="{{ asset('images/icons/menu icon.svg') }}" alt=""
         class="w-5 min-w-[1.25rem] h-5 flex-shrink-0"
         style="{{ request()->routeIs('kasir.pesanan.*') ? 'filter:brightness(0)' : 'filter:brightness(0) invert(1);opacity:.8' }}">
    <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Kelola Pesanan</span>
</a>

{{-- AFTER --}}
@php $isPesananActive = request()->routeIs('kasir.pesanan.*'); @endphp
<a href="{{ route('kasir.pesanan.index') }}"
   class="relative flex items-center h-12 px-3 rounded-xl transition-colors overflow-hidden
          {{ $isPesananActive ? 'bg-white text-brand-dark' : 'text-white hover:bg-white/10' }}">
    <img src="{{ asset('images/icons/' . ($isPesananActive ? 'pesanan icon red.svg' : 'pesanan icon white.svg')) }}"
         alt=""
         class="w-5 min-w-[1.25rem] h-5 flex-shrink-0">
    <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300">Kelola Pesanan</span>
</a>
```

**Perubahan:**
1. Tambah `@php $isPesananActive = ...; @endphp` untuk hindari evaluate `routeIs` 3x.
2. `<img src>` jadi expression yang pilih file `red` (active) atau `white` (inactive).
3. **Hapus** atribut `style="filter:..."` — karena icon sudah dual-color.

#### Step 2.3 — Optional: Histori Pesanan icon

Skip. SVG clock inline tetap dipakai sampai user kasih icon spesifik. **Jangan ubah**.

#### Step 2.4 — Cache cleanup

```bash
php artisan view:clear
```

#### Step 2.5 — Manual test

1. Login sebagai user Kasir.
2. Buka `/kasir` → cek icon "Beranda Kasir":
   - Saat aktif (URL = `/kasir`): icon hitam di bg putih.
   - Saat inactive (URL lain): icon putih di bg maroon.
3. Klik "Kelola Pesanan" → icon merah di bg putih (active).
4. Pindah ke menu lain → icon "Kelola Pesanan" jadi putih di bg maroon.
5. Buka DevTools → Network: pastikan SVG load **200 OK** (bukan 404). Spasi di filename akan auto-encode ke `%20` lewat helper `asset()`.

### 3.4 Pitfall

- **Spasi di filename** (`KVT ICON USER.svg`, `pesanan icon red.svg`) sah via `asset()` (auto URL-encode). Optional rename ke kebab-case (`kvt-icon-user.svg`, dst) untuk konsistensi — **tidak wajib sekarang**, karena perlu update reference di seluruh kode.
- **Jangan apply filter CSS pada dual-color icon** — akan double-process warna & rusak.
- **Konsistensi size**: `w-5 h-5` (20×20px) di semua icon sidebar. Jangan ubah.

### 3.5 Acceptance Criteria

- [ ] Icon "Beranda Kasir" = `KVT ICON USER.svg` dengan filter brightness toggle saat active/inactive.
- [ ] Icon "Kelola Pesanan" = `pesanan icon red.svg` saat active, `pesanan icon white.svg` saat inactive, tanpa filter CSS.
- [ ] DevTools Network tab: semua 3 SVG return **200 OK**.
- [ ] Visual konsisten (size 20px, posisi rata dengan icon lain).
- [ ] Hover sidebar masih smooth (transition 300ms dipertahankan).
- [ ] Tidak ada XML parse warning di browser console.

### 3.6 Estimasi: 20 menit

---

## 4. Task 3 — Buat Dummy Data Pesanan (Refactor PesananSeeder)

### 4.1 Tujuan
Refactor `PesananSeeder` supaya:
1. Generate 100 pesanan dengan distribusi tanggal **terkontrol** — selalu ada data hari ini, minggu ini, dan retro.
2. **Idempoten** — bisa rerun tanpa duplikasi (truncate di awal).
3. **Realistis** — sebagian punya catatan, status mix, kasir bervariasi.
4. **Safe di production** — guard via env variable.

### 4.2 Strategi distribusi

| Bucket          | Jumlah | Rentang tanggal     | Status mix                                              |
|-----------------|--------|---------------------|---------------------------------------------------------|
| Hari ini        | 8      | hari ini, jam 08–22 | 5 selesai, 2 diproses, 1 menunggu konfirmasi (weighted) |
| Minggu ini      | 22     | 1–6 hari lalu       | 18 selesai, 2 diproses, 2 menunggu konfirmasi           |
| Retro           | 70     | 7–29 hari lalu      | 100% selesai+lunas                                      |
| **Total**       | 100    |                     |                                                         |

### 4.3 Tahapan

#### Step 3.1 — Replace `database/seeders/PesananSeeder.php`

Ganti seluruh isi file dengan:

```php
<?php

namespace Database\Seeders;

use App\Models\DetailPesanan;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PesananSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production') && !env('SEED_PESANAN_FORCE')) {
            $this->command->warn('PesananSeeder di-skip karena environment production. Set SEED_PESANAN_FORCE=true untuk override.');
            return;
        }

        $faker = \Faker\Factory::create('id_ID');

        $kasirIds = User::where('id_role', 2)->pluck('id_users')->toArray();
        $mejaIds  = Meja::pluck('id_meja')->toArray();
        $menus    = Menu::select('id_menu', 'harga')->get();

        if ($menus->isEmpty() || empty($mejaIds)) {
            $this->command->warn('Tidak ada data menu atau meja. Jalankan seeder lain terlebih dahulu.');
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DetailPesanan::truncate();
        Pesanan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $distributions = [];

        for ($i = 0; $i < 8; $i++) {
            $distributions[] = [
                'date'   => Carbon::now()->setTime(rand(8, 21), rand(0, 59), rand(0, 59)),
                'status' => $this->randomStatusBucket(['selesai' => 5, 'diproses' => 2, 'menunggu konfirmasi' => 1]),
            ];
        }

        for ($i = 0; $i < 22; $i++) {
            $daysAgo = rand(1, 6);
            $distributions[] = [
                'date'   => Carbon::now()->subDays($daysAgo)->setTime(rand(8, 21), rand(0, 59), rand(0, 59)),
                'status' => $this->randomStatusBucket(['selesai' => 18, 'diproses' => 2, 'menunggu konfirmasi' => 2]),
            ];
        }

        for ($i = 0; $i < 70; $i++) {
            $daysAgo = rand(7, 29);
            $distributions[] = [
                'date'   => Carbon::now()->subDays($daysAgo)->setTime(rand(8, 21), rand(0, 59), rand(0, 59)),
                'status' => ['status_pesanan' => 'selesai', 'status_pembayaran' => 'lunas'],
            ];
        }

        $notes = [
            null, null, null, null,
            'Tidak pedas',
            'Less sugar',
            'Extra cheese',
            'No onion',
            'Tambah es batu',
            'Take away',
        ];

        $usedNoPesanan = [];

        foreach ($distributions as $entry) {
            $date   = $entry['date'];
            $status = $entry['status'];

            do {
                $noPesanan = 'PS-' . $date->format('YmdHis') . '-' . strtoupper(Str::random(4));
            } while (in_array($noPesanan, $usedNoPesanan));

            $usedNoPesanan[] = $noPesanan;

            $selectedMenus = $menus->random(min(rand(1, 4), $menus->count()));
            $totalHarga    = 0;
            $detailItems   = [];

            foreach ($selectedMenus as $menu) {
                $jumlah        = rand(1, 3);
                $subtotal      = $menu->harga * $jumlah;
                $totalHarga   += $subtotal;
                $detailItems[] = [
                    'id_menu'  => $menu->id_menu,
                    'jumlah'   => $jumlah,
                    'catatan'  => $notes[array_rand($notes)],
                    'subtotal' => $subtotal,
                ];
            }

            Pesanan::create([
                'no_pesanan'        => $noPesanan,
                'id_user'           => $kasirIds ? $kasirIds[array_rand($kasirIds)] : null,
                'id_meja'           => $mejaIds[array_rand($mejaIds)],
                'nama_konsumen'     => $faker->name(),
                'total_harga'       => $totalHarga,
                'status_pembayaran' => $status['status_pembayaran'],
                'status_pesanan'    => $status['status_pesanan'],
                'tgl_pembayaran'    => $status['status_pesanan'] === 'selesai' ? $date : null,
            ]);

            foreach ($detailItems as $item) {
                DetailPesanan::create(array_merge(['no_pesanan' => $noPesanan], $item));
            }
        }

        $totalCreated = count($distributions);
        $this->command->info("✓ {$totalCreated} pesanan dummy dibuat (8 hari ini, 22 minggu ini, 70 retro).");
    }

    private function randomStatusBucket(array $weights): array
    {
        $bucket = [];
        foreach ($weights as $status => $weight) {
            for ($i = 0; $i < $weight; $i++) {
                $bucket[] = $status;
            }
        }

        $picked = $bucket[array_rand($bucket)];

        return match ($picked) {
            'selesai'             => ['status_pesanan' => 'selesai',             'status_pembayaran' => 'lunas'],
            'diproses'            => ['status_pesanan' => 'diproses',            'status_pembayaran' => 'menunggu'],
            'menunggu konfirmasi' => ['status_pesanan' => 'menunggu konfirmasi', 'status_pembayaran' => 'menunggu'],
        };
    }
}
```

#### Step 3.2 — Run seeder

**Opsi A — seed pesanan saja (recommended):**

```bash
php artisan db:seed --class=PesananSeeder
```

**Opsi B — fresh database (kalau perlu reset semua):**

```bash
php artisan migrate:fresh --seed
```

⚠️ `migrate:fresh` akan DROP semua tabel (users, menus, kategori, dll). Pakai Opsi A kalau cuma butuh refresh pesanan.

#### Step 3.3 — Verifikasi data via UI

1. Login admin → `/admin`:
   - **Tabel "Data Pesanan Hari Ini"**: harus ada **±8 row** dengan jam variatif.
   - **Chart "Pesanan Hari Ini"**: ada bar/dot di beberapa jam (tidak flat).
   - **Chart "Pendapatan Minggu Ini"**: ada variasi bar (tidak semua 0).
   - **Kartu summary** (Total Menu, Kasir, Transaksi, Pendapatan): angka realistis.
2. Login kasir → `/kasir/pesanan`:
   - Tabel pesanan punya status mix (selesai, diproses, menunggu konfirmasi).
3. `/kasir/histori`:
   - Pesanan selesai dengan tanggal variatif 0–29 hari lalu.
4. Klik salah satu pesanan → cek detail menampilkan `catatan` untuk yang punya, dan section catatan disembunyikan/empty untuk yang null.

#### Step 3.4 — Verifikasi via SQL (opsional)

```sql
SELECT COUNT(*) FROM pesanan;  -- expected: 100

SELECT DATE(tgl_pembayaran) AS tgl, COUNT(*) AS jumlah
FROM pesanan WHERE tgl_pembayaran IS NOT NULL
GROUP BY DATE(tgl_pembayaran)
ORDER BY tgl DESC LIMIT 7;

SELECT status_pesanan, COUNT(*) FROM pesanan GROUP BY status_pesanan;

SELECT
  SUM(catatan IS NULL) AS null_catatan,
  SUM(catatan IS NOT NULL) AS ada_catatan
FROM detail_pesanan;
```

### 4.4 Pitfall

- **Truncate butuh `FOREIGN_KEY_CHECKS=0`** — sudah di-handle di seeder. Jangan hapus baris itu.
- **`migrate:fresh` destruktif** — pakai hanya kalau perlu reset full database.
- **Production guard** (`if (app()->environment('production') ...)` di awal `run()`) — **WAJIB pertahankan**. Override hanya via env `SEED_PESANAN_FORCE=true`.
- **Timezone**: `config/app.php` → `timezone` harus sesuai. Carbon `now()` ikut config.
- **Random nature** — rerun = distribusi beda. Kalau perlu reproducible: tambah `srand(12345);` di awal `run()`.
- **Multi-kategori menu** — seeder ini **tidak menyentuh** pivot `menu_kategori` (di-handle `MenuSeeder`). Aman.

### 4.5 Acceptance Criteria

- [ ] Tabel `pesanan` punya **100 row** setelah seed.
- [ ] Minimal **5 row** punya `tgl_pembayaran` = hari ini.
- [ ] Tabel `detail_pesanan` punya 1–4 row per pesanan.
- [ ] Beranda Admin: chart "Pesanan Hari Ini" tidak flat.
- [ ] Beranda Admin: chart "Pendapatan Minggu Ini" punya variasi.
- [ ] Kasir Kelola Pesanan: tampil mix status (selesai, diproses, menunggu konfirmasi).
- [ ] Kasir Histori: pesanan selesai dengan rentang 0–29 hari.
- [ ] Sebagian detail pesanan punya `catatan` (untuk test rendering UI).
- [ ] Seeder bisa di-rerun tanpa error / duplikasi (truncate idempoten).
- [ ] Seeder skip otomatis di environment production (kecuali env override).

### 4.6 Estimasi: 45 menit

---

## 5. Estimasi Effort Total

| Task                              | Estimasi   |
|-----------------------------------|------------|
| Task 1 — Hapus Laporan Keuangan   | 15 menit   |
| Task 2 — Wire icon kasir baru     | 20 menit   |
| Task 3 — Refresh PesananSeeder    | 45 menit   |
| Testing end-to-end + commit       | 10 menit   |
| **Total**                         | **±1 jam 30 menit** |

---

## 6. Urutan Eksekusi (PM Rekomendasi)

```
1. Task 1 (hapus halaman)      ◄── paling cepat, langsung terlihat hasilnya
2. Task 2 (icon kasir)          ◄── pure UI, low risk
3. Task 3 (seeder pesanan)      ◄── paling impactful untuk demo, last karena perlu DB
4. Verify + commit              ◄── end-to-end check
```

Semua 3 task **independen** (tidak ada dependency antar task), jadi bisa juga dikerjakan paralel kalau ada multiple implementer.

---

## 7. Definition of Done

- [ ] **Task 1 ✓**: URL `/admin/laporan-keuangan*` return 404, sidebar admin clean dari link Laporan Keuangan.
- [ ] **Task 2 ✓**: Sidebar kasir pakai 3 icon baru (`KVT ICON USER` + `pesanan icon red/white`).
- [ ] **Task 3 ✓**: Database punya 100 pesanan dummy dengan distribusi terkontrol; UI Beranda Admin & halaman Kasir tampil data realistis.
- [ ] Tidak ada Laravel error log di `storage/logs/laravel.log`.
- [ ] Tidak ada JS console error.
- [ ] Visual spot-check 3 halaman: Beranda Admin, Kasir Kelola Pesanan, Kasir Histori.
- [ ] Branch `feature/task2-cleanup` di-commit dengan pesan jelas (mis. `feat: cleanup laporan-keuangan, wire kasir icons, refresh pesanan dummy`).
- [ ] (Opsional) Push branch dan buat PR ke `main`.

---

## 8. Catatan Implementer

### 8.1 Untuk AI Model lebih murah

- **Selalu Read file dulu** sebelum Edit. Jangan asumsi struktur.
- **Pakai Edit tool dengan unique old_string** (sertakan context 2–3 baris di atas/bawah).
- **Verify after each step** dengan grep / git diff sebelum lanjut.
- **Kalau bingung dengan filter brightness CSS**, baca pattern di section Admin sidebar yang sudah ada (Beranda Admin, Menu, Kategori, Pengguna Kasir).

### 8.2 File yang TIDAK BOLEH disentuh

- `Penjelasan.md` — dokumentasi historis, biarkan apa adanya walaupun ada string `laporan-keuangan`.
- `composer.json` — jangan uninstall package PDF/Excel (lihat Task 1 Pitfall).
- `BerandaAdminController`, `KelolaPesananController`, `HistoriPesananController`, `PesananController` — masih pakai `Pdf::loadView`, jangan ubah.
- Migration files — schema sudah final, jangan tambah migration baru untuk task ini.

### 8.3 Git workflow

```bash
git checkout main
git pull origin main
git checkout -b feature/task2-cleanup

# ... do work ...

git add -A
git commit -m "feat: cleanup laporan-keuangan, wire kasir icons, refresh pesanan dummy"
git push -u origin feature/task2-cleanup
# Lalu buka PR di GitHub
```

---

*Brief siap dieksekusi. Estimasi 1.5 jam untuk Senior, 2 jam untuk AI Model dengan verifikasi.*
