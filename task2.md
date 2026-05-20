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

---

# Task 3 — Konsumen UI Polish (PM Plan)

Project Manager: AI Assistant
Audience: Senior Programmer / AI Implementer (model murah / junior dev)
Branch: `feature/task2-cleanup`
Figma references:
- `969-22654` — Beranda Konsumen (default)
- `969-22846` / `969-22865` — Beranda Konsumen (scrolled state, sticky header)
- `648-8923` + `792-11529` — Splash 1 & 2 (sudah diimplementasi sebagai overlay animasi)

File utama: `resources/views/konsumen/beranda.blade.php`
File pendukung: `public/images/icons/*`, `public/images/bg/kvt-banner.jpg`

---

## 1. Konteks

Iterasi pertama Beranda Konsumen sudah live (splash animasi, hero, search, kategori pills, kartu menu 2-kolom, footer, bottom navbar). Setelah QA visual user, ditemukan 5 gap dibanding Figma & 5 perbaikan kualitas:

| # | Gap saat ini | Ekspektasi |
|---|-------------|------------|
| A | Saat scroll, hanya search yang sticky. Kategori pills hilang ditarik ke atas. | Sticky bar harus berisi **search + category pills** sekaligus. |
| B | Banyak `text-[10px]`/`text-[12px]`/`text-[14px]`, gap padat. | Naikkan 1–2 step (1rem ≈ 16px, jadi shift 2–4px) agar nyaman dibaca di HP. Hero 36px **tetap**. |
| C | Bottom navbar masih pakai ikon lama (`menu icon.svg`, `pesanan icon white.svg`, `shopping-cart.svg`, `location-marker.svg`). | User sudah menambahkan ikon baru: `menu_konsumen.svg`, `pesanan_konsumen.svg`, `keranjang_konsumen.svg`, `lacak_konsumen.svg`. Wajib dipakai. |
| D | Footer "Lacak Pesanan" jadi `<span>` saat tidak ada session order — beda dari item lain yang `<a>`. | Selalu klikable / konsisten, atau berikan fallback link + state visual yang jelas. |
| E | `kvt-banner.jpg` ter-crop sehingga piring makanan tidak fokus. | Banner harus jelas menampilkan piring berisi makanan (lihat Figma `969-22654`: smoked beef pasta sebagai hero). |

## 2. Pemetaan Perubahan → Lokasi Kode

| Tahap | Section dalam `beranda.blade.php` | Baris (perkiraan saat ini) |
|-------|----------------------------------|----------------------------|
| 1 — Banner hero | `<header>` block, `<img src="...kvt-banner.jpg">` | ~114–123 |
| 2 — Sticky search + category | `#sticky-search` block + JS scroll handler | ~157–179 dan script bawah (`SCROLL_THRESHOLD`) |
| 3 — Typography & spacing | seluruh body (selain hero 36px) | global |
| 4 — Ikon navbar | `<nav>` di bawah; 4 `<img class="nav-icon">` | ~378–418 |
| 5 — Footer Lacak Pesanan | `<ul>` di footer kolom Navigation | ~317–323 |

---

## 3. Tahapan Detail (urut, paralel-safe)

### Tahap 1 — Ganti / Re-crop Banner Hero  *(target: piring jelas di tengah)*

**Masalah:** `kvt-banner.jpg` saat ini menampilkan area meja, bukan piring. Figma `969-22654` menunjukkan smoked-beef-pasta plate sebagai hero, dengan plating jelas memenuhi >60% area gambar.

**Pilihan implementasi (pilih SATU, urut prioritas):**

1. **Replace asset** (paling direkomendasikan):
   - Mintakan ke desain / siapkan file baru `public/images/bg/kvt-banner.jpg` (1080×800 minimal, JPEG ~150KB) yang menampilkan plate makanan center-focused. Referensi: smoked beef pasta, pasta carbonara, atau ayam plating dengan sambal.
   - Tidak perlu ubah kode — `<img src>` sudah benar.

2. **Alternatif aset lokal** (kalau tidak bisa upload baru):
   - Pakai salah satu aset di `public/images/food/`. Kandidat terbaik untuk feel "pasta/plated dish": `ayam_duo_sambal.png`, `chicken_strip.png`, `chicken_wings.png`.
   - Edit `beranda.blade.php` baris ~116:
     ```blade
     <img src="{{ asset('images/food/ayam_duo_sambal.png') }}" alt=""
          class="absolute inset-0 w-full h-full object-cover object-center scale-110">
     ```
   - Tambah `object-position: center` + `scale-110` agar plate fill frame.

3. **Dynamic dari menu pertama** (fallback paling lemah):
   - Re-enable block `@if ($heroMenu)` (sudah ada di kode sebelumnya, tapi di-overwrite hard-coded oleh user).
   - Kelemahan: foto bisa berubah-ubah dan tidak selalu pasta.

**Acceptance Criteria:**
- Buka beranda di 390×844 viewport → piring/plating terlihat di area tengah hero (Y 100–230px), tidak hanya meja/background polos.
- Dark wash (`bg-brand-dark/55`) tetap menjamin teks "Pesan Menu / Anti Ribet" terbaca.
- Tidak ada distorsi aspect-ratio.

**File:** `resources/views/konsumen/beranda.blade.php` (~baris 114–123) ATAU `public/images/bg/kvt-banner.jpg`.

---

### Tahap 2 — Sticky Bar: Search **+** Category Pills

**Masalah:** Saat scroll, kategori pills (yang ada di-content) ikut tergulir ke atas, padahal user ingin tetap dapat memfilter kategori tanpa scroll balik.

**Definition of Done:**
- Saat `window.scrollY > 220px`, sticky bar `#sticky-search` muncul di atas dan berisi (top→bottom):
  1. Strip mini-header: `Selamat Datang!` | mascot | `Meja XX` *(boleh dihilangkan kalau ruang sempit; opsional)*
  2. Search input (full-width, glass-bg di atas dark-red)
  3. Horizontal-scrollable category pills row (semua kategori sama persis dengan yang in-content)
- Pill **aktif** di sticky bar selalu sinkron dengan pill aktif di in-content (klik salah satu → keduanya update).
- Search input value sinkron dua-arah (sudah ada via `syncSearch()` — jangan dirusak).
- Klik pill di sticky bar memanggil `filterCategory()` yang sama → grid menu langsung re-filter.

**Implementasi:**

A. **Mark-up perubahan** di `#sticky-search` (~baris 158–179):
```blade
<div id="sticky-search"
     class="hidden-sticky fixed top-0 inset-x-0 z-30 bg-brand-dark/95 backdrop-blur-md shadow-md">
    <div class="max-w-md mx-auto px-[18px] pt-3 pb-3 safe-top">
        {{-- (opsional) mini header strip --}}
        <div class="flex items-center gap-3 mb-2">
            <span class="flex-1 text-white text-[13px] font-bold tracking-wide capitalize">Selamat Datang!</span>
            <img src="{{ asset('images/icons/MASCOOT WHITE.svg') }}" class="w-7 h-7" alt="">
            <span class="flex-1 text-white text-[13px] font-bold tracking-wide capitalize text-right">Meja {{ $meja->no_meja }}</span>
        </div>

        {{-- search --}}
        <div class="relative mb-2">
            <input id="sticky-search-input" type="text" placeholder="Cari Menu"
                   class="w-full bg-white/15 border border-white/20 rounded-[9px] py-3 pl-3 pr-9 text-[14px] text-white placeholder-white/70 font-medium focus:outline-none focus:ring-2 focus:ring-white/40">
            {{-- icon kaca pembesar (existing svg) --}}
        </div>

        {{-- NEW: category pills row, mirror dari in-content --}}
        <div id="sticky-category-row" class="flex gap-3 overflow-x-auto no-scrollbar -mx-[18px] px-[18px]">
            <button data-kat="all"
                    class="sticky-cat-btn shrink-0 px-3 py-1.5 rounded-[9px] text-[14px] font-medium bg-white text-brand-dark shadow-[2px_4px_2px_rgba(0,0,0,0.25)] whitespace-nowrap">
                Semua
            </button>
            @foreach ($kategoris as $kategori)
                <button data-kat="{{ $kategori->id_kategori }}"
                        class="sticky-cat-btn shrink-0 px-3 py-1.5 rounded-[9px] text-[14px] font-medium bg-white text-brand-dark shadow-[2px_4px_2px_rgba(0,0,0,0.25)] whitespace-nowrap">
                    {{ $kategori->nama_kategori }}
                </button>
            @endforeach
        </div>
    </div>
</div>
```

B. **Sinkronisasi JS** (tambahkan di blok `<script>` paling bawah):
```js
// Sticky category pills mirror
document.querySelectorAll('.sticky-cat-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const kat = btn.dataset.kat;
        const target = document.querySelector(`.category-btn[data-kat="${kat}"]`);
        if (target) target.click();
        document.querySelectorAll('.sticky-cat-btn').forEach(b => {
            b.classList.remove('bg-brand-dark', 'text-white');
            b.classList.add('bg-white', 'text-brand-dark');
        });
        btn.classList.remove('bg-white', 'text-brand-dark');
        btn.classList.add('bg-brand-dark', 'text-white');
    });
});
```

C. **Tambah `data-kat="..."` attribute** ke `.category-btn` in-content agar lookup mudah:
```blade
<button onclick="filterCategory('all', this)" data-kat="all" class="category-btn ...">Semua</button>
@foreach($kategoris as $kategori)
    <button onclick="filterCategory('{{ $kategori->id_kategori }}', this)"
            data-kat="{{ $kategori->id_kategori }}" class="category-btn ...">
        {{ $kategori->nama_kategori }}
    </button>
@endforeach
```

D. **Update `filterCategory()`** agar juga refresh state sticky-cat-btn:
```js
function filterCategory(id, btn) {
    activeKategori = id;
    document.querySelectorAll('.category-btn').forEach(b => {
        b.classList.remove('bg-brand-dark', 'text-white');
        b.classList.add('bg-white', 'text-brand-dark');
    });
    btn.classList.remove('bg-white', 'text-brand-dark');
    btn.classList.add('bg-brand-dark', 'text-white');
    document.querySelectorAll('.sticky-cat-btn').forEach(b => {
        const isActive = b.dataset.kat === String(id);
        b.classList.toggle('bg-brand-dark', isActive);
        b.classList.toggle('text-white', isActive);
        b.classList.toggle('bg-white', !isActive);
        b.classList.toggle('text-brand-dark', !isActive);
    });
    runFiltering();
}
```

**Smoke test:**
- Scroll ke 300px → sticky bar muncul dengan search + pills.
- Klik "Coffee" di sticky bar → grid filter ke kategori Coffee, pill Coffee aktif di sticky bar.
- Scroll balik ke 0 → sticky bar hilang; pill Coffee tetap aktif di in-content.

---

### Tahap 3 — Typography & Spacing Boost (1–2 rem step)

**Goal:** Naikkan ukuran teks dan spacing untuk seluruh `beranda.blade.php` **kecuali** hero headline 36px yang sudah sesuai Figma. Acuan: 1rem = 16px; "1–2 step" diartikan +2–4px untuk teks dan +4–8px (≈ 0.25–0.5rem) untuk spacing.

**Mapping teks:**

| Lama | Baru | Konteks |
|------|------|---------|
| `text-[10px]` | `text-[12px]` | `.nav-label`, badge mini |
| `text-[11px]` | `text-[13px]` | sticky mini-header (Selamat Datang/Meja) |
| `text-[12px]` | `text-[14px]` | badge kartu (`Pedas`/`Dingin`), footer body, footer copyright |
| `text-[14px]` | `text-[16px]` | search input, category pills, nama menu, harga, tombol "Tambah" |
| `text-xs` (~12px) | `text-sm` (~14px) | header bar `Selamat Datang/Meja` |
| `text-2xl` (24px) | `text-3xl` (30px) | heading `Category` |
| `text-[20px]` | `text-[24px]` | footer H3 (`Navigation`, `Visit us!`, `Reservation?`) |
| `text-lg` (18px) | `text-xl` (20px) | judul di modal detail |

**Mapping padding / gap / margin:**

| Lama | Baru | Konteks |
|------|------|---------|
| `py-1.5` | `py-2.5` | category pill, tombol "Tambah" |
| `py-2.5` | `py-3` | search input |
| `gap-1.5` | `gap-2.5` | card info column |
| `gap-4` (kategori row) | `gap-5` | category pills horizontal |
| `gap-[18px]` | `gap-5` (20px) | menu grid 2-col |
| `mb-3` (`Category` heading) | `mb-5` | heading `Category` |
| `mt-2 mb-3` | `mt-4 mb-5` | wrap `Category` |
| `px-2.5 pt-2.5 pb-2.5` | `px-3.5 pt-3.5 pb-3.5` | card info bawah |
| `pb-[120px]` (main) | `pb-[140px]` | main bottom (kasih ruang nav baru) |
| `pt-10 pb-[120px]` (footer) | `pt-12 pb-[140px]` | footer |
| `space-y-8` (footer cols) | `space-y-10` | jarak antar kolom footer |
| `min-h-[40px]` (nama menu) | `min-h-[48px]` | jaga 2-line balance |
| `mb-4` (kategori container) | `mb-6` | jarak ke grid |
| `-mt-[34px]` (card info) | tidak diubah | overlap effect harus tetap |

**JANGAN diubah:**
- Hero `text-[36px] leading-[40px]` ("Pesan Menu / Anti Ribet").
- `splash-content` `text-2xl`.
- Logo footer `h-[60px]`.

**Acceptance:**
- Layar 390×844 — nama menu (1–2 baris) terbaca tanpa zoom; harga `Rp 12.000` terlihat bold dan kuat.
- "Tambah" button tinggi ≥ 38px (tap target HIG minimum).
- Footer copy 14px tidak terlalu tipis.

**File:** `resources/views/konsumen/beranda.blade.php`.

---

### Tahap 4 — Ganti Ikon Bottom Navbar

**Assets baru** (sudah ada di `public/images/icons/`):
- `menu_konsumen.svg`
- `pesanan_konsumen.svg`
- `keranjang_konsumen.svg`
- `lacak_konsumen.svg`

**Pemetaan:**

| Posisi nav | Sebelum | Sesudah |
|------------|---------|---------|
| Menu (aktif) | `menu icon.svg` | `menu_konsumen.svg` |
| Pesanan | `pesanan icon white.svg` | `pesanan_konsumen.svg` |
| Keranjang | `shopping-cart.svg` | `keranjang_konsumen.svg` |
| Lacak | `location-marker.svg` | `lacak_konsumen.svg` |

**Validasi yang harus dilakukan (PENTING — visual quality gate):**

1. **Buka 4 SVG baru di browser** dan cek apakah:
   - SVG sudah berwarna putih native? (`fill="white"` atau `fill="#fff"`)
   - SVG monokrom dengan `currentColor`? → bagus, bisa di-tint via CSS.
   - SVG hitam? → butuh filter inverter.

2. **Jika SVG putih native:**
   - Hapus filter pada `.nav-icon` default state.
   - Active-state tetap menjadi dark-red:
     ```css
     .nav-item.is-active .nav-icon {
         filter: brightness(0) saturate(100%) invert(7%) sepia(86%) saturate(5485%) hue-rotate(348deg) brightness(64%) contrast(112%);
     }
     ```

3. **Jika SVG hitam/dark:**
   - Default state harus di-tint putih: `filter: brightness(0) invert(1);`
   - Active state tetap menggunakan filter ke dark-red (rule existing).

**Markup change (~baris 378-418):**
```blade
<div class="nav-item is-active flex items-center justify-center w-[78px] h-[58px] p-1.5">
    <div class="flex flex-col items-center justify-center gap-px">
        <img src="{{ asset('images/icons/menu_konsumen.svg') }}" class="w-[30px] h-[30px] nav-icon" alt="">
        <span class="nav-label text-[12px] leading-3 font-bold tracking-wide capitalize">Menu</span>
    </div>
</div>

<a href="{{ route('konsumen.keranjang') }}" class="nav-item ...">
    <img src="{{ asset('images/icons/pesanan_konsumen.svg') }}" class="w-[30px] h-[30px] nav-icon" alt="">
    ...
</a>

<a href="{{ route('konsumen.keranjang') }}" class="nav-item ...">
    <img src="{{ asset('images/icons/keranjang_konsumen.svg') }}" class="w-[30px] h-[30px] nav-icon" alt="">
    ...
</a>

<a href="..." class="nav-item ...">
    <img src="{{ asset('images/icons/lacak_konsumen.svg') }}" class="w-[30px] h-[30px] nav-icon" alt="">
    ...
</a>
```

**Acceptance:**
- 4 ikon baru terlihat di navbar; ikon `Menu` (aktif) berwarna dark-red, sisanya putih.
- Tidak ada ikon `broken-img placeholder`.
- Tap target tetap 78×58.

**File:** `resources/views/konsumen/beranda.blade.php` (~378–418) + `<style>` block (~74–76).

---

### Tahap 5 — Standardisasi Footer "Lacak Pesanan"

**Masalah:** Saat `session('no_pesanan_baru')` kosong, item ditampilkan sebagai `<span class="opacity-60">` — beda struktur HTML dari item lain (`<li><a>`). Tidak ada visual feedback klik, tidak konsisten secara accessibility.

**Solusi (pilih SALAH SATU; rekomen #1):**

**Opsi 1 — Fallback link ke Keranjang** (recommended):
```blade
<li>
    @php($noPesanan = session('no_pesanan_baru'))
    <a href="{{ $noPesanan ? route('konsumen.pesanan', $noPesanan) : route('konsumen.keranjang') }}"
       class="hover:underline {{ $noPesanan ? '' : 'opacity-70' }}"
       @if(!$noPesanan) title="Pesanan belum dibuat — buka keranjang dulu" @endif>
        Lacak Pesanan
    </a>
</li>
```

**Opsi 2 — Button disabled aksesibel** (jika tidak ingin redirect ke /keranjang):
```blade
<li>
    @php($noPesanan = session('no_pesanan_baru'))
    @if ($noPesanan)
        <a href="{{ route('konsumen.pesanan', $noPesanan) }}" class="hover:underline">Lacak Pesanan</a>
    @else
        <button type="button" disabled aria-disabled="true"
                class="opacity-60 cursor-not-allowed text-left"
                title="Pesanan belum dibuat">
            Lacak Pesanan
        </button>
    @endif
</li>
```

**Konsistensi tambahan:**
- Audit 4 item Navigation footer agar **semuanya** memakai `<a class="hover:underline">` (atau button disabled), tanpa `<span>` setengah jalan.
- "Pesanan" dan "Keranjang" saat ini sama-sama menuju `konsumen.keranjang` — redundant. Tambahkan komentar `{{-- TODO: pisahkan "Pesanan" jika histori konsumen diimplementasi --}}`.

**Acceptance:**
- Semua item Navigation footer adalah `<a>` (atau `<button disabled>` yang aksesibel).
- Hover state seragam (`hover:underline`).
- Screen-reader friendly.

**File:** `resources/views/konsumen/beranda.blade.php` (~313–325).

---

## 4. Urutan Eksekusi (Saran)

| Step | Tahap | Estimasi | Bisa paralel? |
|------|-------|----------|---------------|
| 1 | Tahap 1 — Banner | 10 menit | ✅ |
| 2 | Tahap 4 — Ikon navbar + validasi color | 20 menit | ✅ |
| 3 | Tahap 5 — Footer Lacak Pesanan | 10 menit | ✅ |
| 4 | Tahap 2 — Sticky search + category | 40 menit | ⚠️ ubah JS, hati-hati dengan Tahap 3 |
| 5 | Tahap 3 — Typography & spacing | 30 menit | ⚠️ banyak file-wide search-replace, lakukan terakhir |

**Total estimasi:** ~1,5–2 jam, termasuk smoke test.

**Strategi commit (atomic):**
- `feat(konsumen): swap kvt-banner to plated food per Figma 969-22654`
- `feat(konsumen): use new bottom-nav icons (menu_konsumen, pesanan_konsumen, dst)`
- `fix(konsumen): standardize footer "Lacak Pesanan" link state`
- `feat(konsumen): sticky search + category pills on scroll (Figma 969-22846)`
- `style(konsumen): bump typography & spacing on beranda`

---

## 5. Smoke Test Checklist (wajib sebelum PR)

Buka Chrome DevTools → device emulator iPhone 13 (390×844). Clear sessionStorage sebelum mulai.

**Splash & Hero**
- [ ] Refresh halaman → splash overlay tampil lalu hilang ~2,4s.
- [ ] Refresh kedua → splash **tidak** muncul lagi (sessionStorage).
- [ ] Hero banner menampilkan piring makanan jelas.

**Sticky bar (Tahap 2)**
- [ ] Scroll ke posisi 300px → sticky bar muncul dengan mini-header (opsional) + search + category pills.
- [ ] Klik pill "Coffee" di sticky bar → grid memfilter kategori Coffee.
- [ ] Pill aktif sync antara sticky dan in-content.
- [ ] Ketik di sticky search → in-content search ikut + grid filter.

**Typography (Tahap 3)**
- [ ] Tidak ada teks `< 12px` (kecuali `.nav-label` yang 12px).
- [ ] Tombol "Tambah" tinggi ≥ 38px.
- [ ] Footer body terbaca tanpa zoom.

**Bottom navbar (Tahap 4)**
- [ ] 4 ikon baru tampil (tidak ada `broken-img`).
- [ ] Menu aktif → ikon dark-red.
- [ ] Pesanan/Keranjang/Lacak → ikon putih.
- [ ] Badge angka Keranjang muncul saat `cartCount > 0`.

**Footer (Tahap 5)**
- [ ] "Lacak Pesanan" tanpa order → redirect ke `/keranjang` (Opsi 1).
- [ ] "Lacak Pesanan" dengan order → redirect ke `/pesanan/{no}`.
- [ ] Tidak ada item Navigation yang `<span>`.

**Regression**
- [ ] Modal Detail menu tetap berfungsi.
- [ ] "Tambah ke Keranjang" POST `konsumen.keranjang.tambah`.
- [ ] CSRF token meta tersedia.
- [ ] Tidak ada error JS console.

---

## 6. Risiko & Catatan Teknis

- **Sticky bar tinggi** — kalau strip mini-header + search + pills > 130px, kartu di-content bisa "jump" saat sticky aktif. Solusi: tambah `padding-top: 130px` sementara di `body.is-scrolled main`.
- **Filter SVG di iOS Safari** — `filter: brightness(0) invert(...)` kadang inkonsisten di Safari < 15. Test di iPhone fisik.
- **Banner asset > 300KB** — kompres dulu (TinyPNG/Squoosh) ke ~150KB JPG quality 80 agar LCP < 2.5s di 4G.
- **GateGuard / pre-commit hook** — repo punya pre-commit hook. Jangan pakai `--no-verify`.
- **`feature/task2-cleanup` branch** — branch yang sama dengan Task 2 Kasir. Pastikan commit baru tidak mencampur file kasir.
- **Reviewer hint** — saat PR, sertakan screenshot before/after untuk: (a) hero, (b) sticky bar saat scroll, (c) navbar baru.

---

## 7. Definition of Done (Task 3)

- [ ] Banner hero menampilkan piring makanan yang jelas.
- [ ] Sticky bar memuat search + category pills dan keduanya fully functional.
- [ ] Semua teks ≥ 12px, spacing tidak crowded di 390px viewport.
- [ ] 4 ikon navbar baru terpakai dan styling Menu (aktif) sesuai Figma.
- [ ] Footer "Lacak Pesanan" konsisten (tidak `<span>`).
- [ ] Smoke test checklist 100% pass.
- [ ] PR dibuka dengan deskripsi rinci + 3 screenshot before/after.
