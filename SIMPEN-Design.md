---
version: 1.0
name: SIMPEN-Kohvito-Design-System
description: Sistem desain aplikasi SIMPEN (Sistem Pemesanan) Kafe Kohvito. Identitas visual berpusat pada maroon tua khas kedai kopi (#681F1F) di atas kanvas putih bersih, dengan tipografi Aileron (humanist sans, self-hosted) dan aksen bayangan offset keras (2px 4px) yang menjadi tanda tangan tombol/kartu di panel konsumen. Tiga "suasana" permukaan — konsumen (mobile-first, playful, shadow tegas), admin/kasir (dashboard bersih, border tipis + shadow lembut), dan dokumen PDF (hitam-putih fungsional dengan header maroon).

colors:
  brand-red: "#681F1F"
  brand-dark: "#460001"
  brand-light: "#FFF5F5"
  brand-red-muted: "#D9C7C7"
  state-red: "#E52E2D"
  state-green: "#58E52D"
  state-yellow: "#EFD935"
  brand-black: "#1A1A1A"
  brand-gray-dark: "#4D4D4D"
  brand-gray: "#808080"
  brand-gray-light: "#CCCCCC"
  brand-gray-extralight: "#E6E6E6"
  brand-white: "#FFFFFF"
  on-primary: "#FFFFFF"
  surface-soft: "#F6F6F6"
  danger-deep: "#9C2C2C"
  maroon-alt: "#380000"

typography:
  heading-page:
    fontFamily: "Aileron, ui-sans-serif, system-ui, sans-serif"
    fontSize: 20px
    fontWeight: 700
    lineHeight: 28px
    letterSpacing: 1px
  title-card:
    fontFamily: "Aileron, sans-serif"
    fontSize: 16px
    fontWeight: 700
    lineHeight: 24px
    letterSpacing: 0.7px
  title-item:
    fontFamily: "Aileron, sans-serif"
    fontSize: 15px
    fontWeight: 700
    lineHeight: 20px
    letterSpacing: 0.6px
  body-md:
    fontFamily: "Aileron, sans-serif"
    fontSize: 14px
    fontWeight: 400
    lineHeight: 20px
    letterSpacing: 0.7px
  body-sm:
    fontFamily: "Aileron, sans-serif"
    fontSize: 13px
    fontWeight: 400
    lineHeight: 20px
    letterSpacing: 0.6px
  label:
    fontFamily: "Aileron, sans-serif"
    fontSize: 12px
    fontWeight: 700
    lineHeight: 16px
    letterSpacing: 0.5px
  caption:
    fontFamily: "Aileron, sans-serif"
    fontSize: 11px
    fontWeight: 400
    lineHeight: 16px
    letterSpacing: 0.4px
  micro:
    fontFamily: "Aileron, sans-serif"
    fontSize: 10px
    fontWeight: 500
    lineHeight: 12px
    letterSpacing: 0.8px
  button:
    fontFamily: "Aileron, sans-serif"
    fontSize: 14px
    fontWeight: 400
    lineHeight: 24px
    letterSpacing: 0.7px
  table-header:
    fontFamily: "Aileron, sans-serif"
    fontSize: 11px
    fontWeight: 700
    lineHeight: 16px
    letterSpacing: 0
  pdf-body:
    fontFamily: "DejaVu Sans, sans-serif"
    fontSize: 11px
    fontWeight: 400
    lineHeight: 1.4
    letterSpacing: 0

rounded:
  sm: 6px
  signature: 9px
  md: 12px
  lg: 16px
  card: 12px
  card-lg: 16px
  pill: 9999px
  full: 9999px

spacing:
  xxs: 4px
  xs: 8px
  sm: 12px
  md: 16px
  lg: 24px
  xl: 32px
  card-padding: 16px
  card-padding-lg: 24px
  section-gap: 24px

components:
  button-primary:
    backgroundColor: "{colors.brand-red}"
    textColor: "{colors.on-primary}"
    typography: "{typography.button}"
    rounded: "{rounded.signature}"
    padding: 8px 16px
    shadow: "2px 4px 2px rgba(0,0,0,0.25)"
  button-primary-pressed:
    backgroundColor: "{colors.brand-red}"
    textColor: "{colors.on-primary}"
    transform: "scale(0.98) / brightness(110%)"
  button-success:
    backgroundColor: "{colors.state-green}"
    textColor: "{colors.on-primary}"
    typography: "{typography.button}"
    rounded: "{rounded.signature}"
    padding: 8px 16px
    shadow: "2px 4px 2px rgba(0,0,0,0.25)"
  button-neutral:
    backgroundColor: "{colors.brand-gray-light}"
    textColor: "{colors.brand-red}"
    typography: "{typography.button}"
    rounded: "{rounded.signature}"
    padding: 8px 16px
    shadow: "2px 4px 2px rgba(0,0,0,0.25)"
  button-danger:
    backgroundColor: "{colors.danger-deep}"
    textColor: "{colors.on-primary}"
    typography: "{typography.button}"
    rounded: "{rounded.signature}"
    padding: 12px 16px
  button-disabled:
    backgroundColor: "{colors.brand-gray-extralight}"
    textColor: "{colors.brand-red}"
    opacity: "60%"
    cursor: not-allowed
  badge-status-selesai:
    backgroundColor: "rgba(88,229,45,0.15)"
    textColor: "{colors.state-green}"
    typography: "{typography.label}"
    rounded: "{rounded.pill}"
    padding: 4px 10px
  badge-status-proses:
    backgroundColor: "rgba(239,217,53,0.20)"
    textColor: "{colors.state-yellow}"
    typography: "{typography.label}"
    rounded: "{rounded.pill}"
    padding: 4px 10px
  badge-status-gagal:
    backgroundColor: "rgba(229,46,45,0.15)"
    textColor: "{colors.state-red}"
    typography: "{typography.label}"
    rounded: "{rounded.pill}"
    padding: 4px 10px
  bottom-nav-konsumen:
    backgroundColor: "{colors.brand-white}"
    textColor: "{colors.brand-red}"
    typography: "{typography.micro}"
    position: "fixed bottom"
  sidebar-internal:
    backgroundColor: "{colors.brand-white}"
    textColor: "{colors.brand-black}"
    activeItemColor: "{colors.brand-red}"
    typography: "{typography.body-sm}"
  card-stat:
    backgroundColor: "{colors.brand-white}"
    border: "1px {colors.brand-gray-extralight}"
    rounded: "{rounded.card}"
    padding: "{spacing.card-padding-lg}"
    shadow: "shadow-sm (lembut)"
  card-order-konsumen:
    backgroundColor: "{colors.brand-white}"
    rounded: "{rounded.card-lg}"
    padding: "{spacing.card-padding}"
    shadow: "2px 4px 4px rgba(0,0,0,0.18)"
  card-notes:
    backgroundColor: "{colors.surface-soft}"
    textColor: "{colors.brand-black}"
    rounded: "{rounded.signature}"
    padding: 10px 16px
  table-admin:
    headerText: "{typography.table-header}"
    headerColor: "{colors.brand-dark}"
    rowDivider: "1px {colors.brand-gray-extralight}"
    rowHover: "{colors.brand-light} 40%"
  text-input:
    backgroundColor: "{colors.brand-white}"
    textColor: "{colors.maroon-alt}"
    border: "1px {colors.maroon-alt}"
    rounded: 8px
    padding: 8px 12px
  text-input-focused:
    ring: "2px {colors.maroon-alt}"
  modal-confirm:
    backgroundColor: "{colors.brand-white}"
    overlay: "rgba(0,0,0,0.35)"
    rounded: "{rounded.card-lg}"
    maxHeight: "calc(100dvh - 2rem)"
  popup-success:
    backgroundColor: "{colors.brand-white}"
    illustration: "Lottie (lottie-web)"
    rounded: "{rounded.card-lg}"
  loading-overlay:
    backgroundColor: "rgba(0,0,0,0.35)"
    position: fixed
  chart-card:
    backgroundColor: "{colors.brand-white}"
    border: "1px {colors.brand-gray-extralight}"
    rounded: "{rounded.card}"
    padding: "{spacing.card-padding-lg}"
    library: "Chart.js 4 (CDN)"
  pdf-report:
    fontFamily: "DejaVu Sans"
    headerBackground: "{colors.maroon-alt}"
    headerText: "{colors.brand-white}"
    bodyBorder: "1px #DDDDDD"
---

## Overview

SIMPEN (Kafe Kohvito) memakai identitas **maroon kedai kopi** — `{colors.brand-red}` (#681F1F)
sebagai warna primer dan `{colors.brand-dark}` (#460001) sebagai penekanan gelap — di atas kanvas
putih bersih. Kombinasi ini hangat dan "kopi banget", sengaja menjauh dari biru korporat aplikasi
kasir pada umumnya. Seluruh teks memakai **Aileron**, humanist sans self-hosted
(`public/fonts/aileron/`, weight 300/400/700), dengan ciri khas **letter-spacing positif**
(`tracking-[0.4px]` s.d. `tracking-[1px]`) di hampir semua teks.

Sistem ini punya **tiga suasana permukaan** yang berbeda per audiens:

1. **Panel Konsumen (mobile-first)** — kartu putih di atas latar lembut, tombol dengan
   **bayangan offset keras** `2px 4px 2px rgba(0,0,0,0.25)` dan radius tanda tangan
   `{rounded.signature}` (9px). Terasa playful dan "menekan tombol fisik" (`active:scale-[0.98]`).
2. **Panel Admin & Kasir (dashboard)** — kartu putih `rounded-xl` dengan border
   `{colors.brand-gray-extralight}` + `shadow-sm` lembut; tabel data, badge status pill,
   dan grafik Chart.js. Lebih tenang dan fungsional.
3. **Dokumen PDF (laporan/struk)** — DejaVu Sans, header tabel maroon `{colors.maroon-alt}`
   (#380000), garis tipis abu; dicetak lewat DomPDF.

**Karakteristik kunci:**

- Maroon `{colors.brand-red}` untuk seluruh aksi primer; `{colors.brand-dark}` untuk heading
  dan penekanan teks gelap.
- Bayangan offset keras (2px 4px) sebagai tanda tangan visual panel konsumen — bukan blur besar.
- Radius 9px (`rounded-[9px]`) untuk tombol — bukan 8px Tailwind standar; ini disengaja.
- Status selalu dikomunikasikan lewat **badge pill dua-warna**: latar warna-status 15–20% alpha +
  teks warna-status penuh (hijau = selesai/lunas, kuning = proses/menunggu, merah = gagal/batal).
- Ukuran teks memakai nilai px eksplisit (`text-[14px]`, `text-[11px]`) agar presisi dengan mockup;
  token global `--text-xxs` (10px) dan `--text-xxxs` (8px) tersedia di `@theme`.
- Token warna didefinisikan **CSS-first** di `resources/css/app.css` blok `@theme`
  (Tailwind CSS v4 — tidak ada `tailwind.config.js`). Class-nya: `bg-brand-red`,
  `text-state-green`, `border-brand-gray-extralight`, dst.

## Colors

### Brand
- **Brand Red / Primer** (`{colors.brand-red}` — #681F1F): Warna aksi utama. Tombol primer,
  link aktif bottom-nav, header sidebar, aksen wordmark. Kalau ragu warna tombol: pakai ini.
- **Brand Dark** (`{colors.brand-dark}` — #460001): Maroon paling gelap. Heading halaman,
  judul tabel, ikon aktif. Bukan untuk latar tombol.
- **Brand Light** (`{colors.brand-light}` — #FFF5F5): Rona merah-muda sangat lembut untuk
  hover baris tabel (`hover:bg-brand-light/40`) dan latar bagian tertentu.
- **Brand Red Muted** (`{colors.brand-red-muted}` — #D9C7C7): Aksen pasif bernuansa maroon
  (divider, elemen nonaktif bernuansa brand).

### Status (State)
- **State Green** (`{colors.state-green}` — #58E52D): Sukses — badge "Selesai"/"Lunas",
  tombol "Selesai" kasir.
- **State Yellow** (`{colors.state-yellow}` — #EFD935): Proses — badge "Diproses"/"Menunggu".
- **State Red** (`{colors.state-red}` — #E52E2D): Gagal/batal — badge "Gagal", pesan error.
- Aturan badge: latar = warna status pada 15–20% alpha (`bg-state-green/15`), teks = warna status penuh.

### Grayscale
- **Brand Black** (`{colors.brand-black}` — #1A1A1A): Teks utama.
- **Brand Gray Dark** (`{colors.brand-gray-dark}` — #4D4D4D): Teks sekunder kuat.
- **Brand Gray** (`{colors.brand-gray}` — #808080): Teks sekunder, placeholder, caption.
- **Brand Gray Light** (`{colors.brand-gray-light}` — #CCCCCC): Tombol netral ("Detail", "Tutup",
  "Unduh QR"), border input.
- **Brand Gray Extralight** (`{colors.brand-gray-extralight}` — #E6E6E6): Border kartu/tabel,
  divider, tombol disabled.
- **Brand White** (`{colors.brand-white}` — #FFFFFF): Kanvas kartu dan permukaan utama.

### Warna pendamping (belum jadi token `@theme`)
- **Maroon Alt** (`{colors.maroon-alt}` — #380000): dipakai input filter admin (border/teks/ring)
  dan header tabel PDF. Secara visual satu keluarga dengan brand-dark.
- **Danger Deep** (`{colors.danger-deep}` — #9C2C2C): tombol destruktif ("Tandai GAGAL").
- **Surface Soft** (`{colors.surface-soft}` — #F6F6F6): latar kotak catatan pesanan.

> Ketiganya masih ditulis sebagai hex literal di Blade. Bila menyentuh area tersebut,
> pertimbangkan mempromosikannya ke `@theme` (lihat *Known Gaps*).

## Typography

### Font Family
- **Aileron** (self-hosted, `public/fonts/aileron/`) — satu-satunya typeface UI. Weight yang
  tersedia: 300 (Light), 400 (Regular + Italic), 700 (Bold + Italic). Terdaftar sebagai
  `--font-sans` di `@theme`, jadi class `font-sans` (default) sudah Aileron.
- **Georgia** (self-hosted, `--font-serif`) — tersedia untuk aksen serif, jarang dipakai.
- **DejaVu Sans** — khusus template PDF (font bawaan DomPDF yang mendukung glyph luas).

### Hierarchy

| Token | Ukuran | Weight | Tracking | Pemakaian |
|---|---|---|---|---|
| `{typography.heading-page}` | 20px | 700 | 1px | Judul halaman konsumen ("Kembali", header) |
| `{typography.title-card}` | 16px | 700 | 0.7px | Judul kartu/section, heading tabel dashboard |
| `{typography.title-item}` | 15px | 700 | 0.6px | Nama menu/item di daftar |
| `{typography.body-md}` | 14px | 400 | 0.7px | Teks tombol, isi utama, harga |
| `{typography.body-sm}` | 13px | 400 | 0.6px | Isi sekunder, instruksi |
| `{typography.label}` | 12px | 700 | 0.5px | Label badge, label form, notes header |
| `{typography.caption}` | 11px | 400 | 0.4px | Caption, isi sel tabel, fine-print |
| `{typography.micro}` | 10px | 500 | 0.8px | Label bottom-nav, tag kecil (token `text-xxs`) |
| `{typography.table-header}` | 11px | 700 | 0 | `<th>` tabel admin (warna `{colors.brand-dark}`) |
| `{typography.pdf-body}` | 11px | 400 | 0 | Sel tabel PDF laporan/struk |

### Principles
- Ukuran ditulis **px eksplisit** (`text-[14px]`) demi presisi; jangan mencampur `text-sm`
  Tailwind dengan `text-[14px]` pada komponen yang sama.
- **Letter-spacing positif adalah suara brand** — hampir semua teks memakai `tracking-[0.4px]`
  hingga `tracking-[1px]`. Tanpa tracking, UI terasa "bukan Kohvito".
- Hierarki dibangun dari **weight 700 + ukuran**, bukan dari warna mencolok. Heading gelap
  (`{colors.brand-black}` / `{colors.brand-dark}`), pendukung abu (`{colors.brand-gray}`).
- Angka uang selalu diformat `Rp {{ number_format($nilai, 0, ',', '.') }}` — titik ribuan,
  tanpa desimal.

## Layout

### Spacing System
- Basis skala Tailwind default (4px). Pola yang dipakai konsisten:
  `gap-2` (8px) antar elemen kecil, `gap-3` (12px) antar tombol sebaris, `gap-4`/`gap-6`
  (16/24px) antar kartu, `p-4` (16px) padding kartu mobile, `p-6` (24px) padding kartu dashboard.
- Jarak antar section dashboard: `mb-6` (24px) — `{spacing.section-gap}`.

### Grid & Container
- **Panel konsumen:** satu kolom mobile-first; konten dibatasi lebar ponsel, komponen melebar
  `w-full`. Navigasi bawah `fixed` (`bottom-nav-konsumen`), konten diberi padding-bottom agar
  tidak tertutup.
- **Panel kasir (antrean):** grid kartu pesanan `grid-cols-1 md:grid-cols-2 xl:grid-cols-3`;
  saat panel detail terbuka, layout menyempit ke `2xl:grid-cols-[828px_420px]` (list + panel samping).
- **Panel admin:** shell sidebar kiri + konten; kartu statistik grid responsif; tabel data
  di dalam wrapper `.kvt-scroll-region` (`overflow-x-auto`) agar tabel lebar tidak merusak layout.
- **Form/modal:** modal terpusat dengan `max-height: calc(100dvh - 2rem)` (`.kvt-modal-panel`)
  dan scroll internal.

### Whitespace Philosophy
Kartu putih + jarak 16–24px membuat setiap layar terasa ringan meski padat data. Panel konsumen
sengaja lebih lega (elemen besar, mudah dijangkau ibu jari); panel internal lebih rapat karena
kasir/admin butuh kepadatan informasi.

## Elevation & Depth

| Level | Perlakuan | Pemakaian |
|---|---|---|
| Flat | Tanpa border/shadow | Latar halaman, section |
| Border tipis | `1px {colors.brand-gray-extralight}` + `shadow-sm` | Kartu dashboard admin/kasir, tabel, chart card |
| Shadow tanda tangan (tombol) | `2px 4px 2px rgba(0,0,0,0.25)` | Semua tombol panel konsumen & kasir |
| Shadow tanda tangan (kartu) | `2px 4px 4px rgba(0,0,0,0.18)` | Kartu konten konsumen (pembayaran, pesanan) |
| Overlay | `rgba(0,0,0,0.35)` fixed inset | Latar modal, loading overlay |

Filosofinya: **konsumen = depth dari bayangan offset keras** (terasa seperti kartu fisik menu),
**internal = depth dari border + shadow lembut** (terasa seperti dashboard profesional).
Jangan menukar keduanya.

### Decorative Depth
- Animasi masuk elemen memakai atribut `data-anim` (`fade-up`, `stagger`) yang digerakkan GSAP.
- Popup sukses memakai ilustrasi **Lottie** (`lottie-web`) + GIF/SVG di `public/images/`.
- Ikon UI berupa file SVG di `public/images/icons/` (dipanggil `asset('images/icons/…')`)
  dan sebagian inline `<svg>`; pewarnaan ikon putih memakai filter `brightness-0 invert`.

## Shapes

### Border Radius Scale

| Token | Nilai | Pemakaian |
|---|---|---|
| `{rounded.sm}` | 6px | Kotak kode/elemen inline kecil |
| `{rounded.signature}` | **9px** | **Tombol & kotak aksi — radius khas Kohvito** |
| `{rounded.md}` / `{rounded.card}` | 12px (`rounded-xl`) | Kartu dashboard, chart card, kotak alert |
| `{rounded.lg}` / `{rounded.card-lg}` | 16px (`rounded-2xl`) | Kartu besar konsumen, modal |
| `{rounded.pill}` | 9999px | Badge status, tag |
| `{rounded.full}` | 50% | Ikon bulat, avatar |

Catatan: 9px ditulis sebagai arbitrary value `rounded-[9px]` — ini disengaja dan konsisten;
jangan "merapikan" menjadi `rounded-lg` (8px).

### Photography & Illustrations
- Foto hanya untuk **gambar menu** — mendukung dua sumber: file storage lokal
  (`asset('storage/'.$menu->gambar_menu)`) atau URL http(s) absolut (CDN Unsplash untuk data demo).
  View selalu mengecek prefix `http` sebelum memilih sumber.
- Gambar menu dirender dalam kartu ber-radius dengan `object-cover`; `max-width:100%` global
  sudah diset di CSS dasar.
- Ilustrasi status (sukses/gagal/kosong) memakai Lottie/GIF/SVG, bukan foto.

## Components

### Tombol

**`button-primary`** — Aksi utama. Latar `{colors.brand-red}`, teks putih,
tipografi `{typography.button}`, radius `{rounded.signature}` (9px), shadow tanda tangan
`2px 4px 2px rgba(0,0,0,0.25)`. Interaksi tekan: `active:scale-[0.98]` (konsumen) atau
`hover:brightness-110` (kasir). Contoh: "Pesan Sekarang", "Terima Pesanan", "Cetak Struk".

**`button-success`** — Latar `{colors.state-green}`, teks putih; khusus aksi penyelesaian
("Selesai" di kasir, "Tandai LUNAS" di simulator). `hover:brightness-95`.

**`button-neutral`** — Latar `{colors.brand-gray-light}` (#CCCCCC), teks `{colors.brand-red}`.
Aksi sekunder: "Detail", "Tutup", "Kembali", "Unduh QR Code". `hover:bg-[#BEBEBE]`.

**`button-danger`** — Latar `{colors.danger-deep}` (#9C2C2C), teks putih. Aksi destruktif
("Tandai GAGAL", batal). `hover` menggelap.

**`button-disabled`** — Latar `{colors.brand-gray-extralight}`, teks brand-red pudar,
`cursor-not-allowed`. Contoh: "QR Belum Siap".

> **Aturan penting tombol sebaris:** semua tombol dalam satu baris aksi wajib memakai
> `whitespace-nowrap` + ukuran teks 14px + `px-2`, agar label tidak patah/terpotong pada
> kartu sempit (pelajaran dari bug "Cetak Struk" — lihat riwayat perbaikan).

### Navigasi

**`bottom-nav-konsumen`** — Bar navigasi bawah `fixed` di semua halaman konsumen. Latar putih,
ikon + label `{typography.micro}`; item aktif berwarna `{colors.brand-red}`, non-aktif abu.
Menerima props `active`, `mejaNo`, `cartCount` (badge jumlah item), dan `lacakHref`
(pakai `Pesanan::lacakUrl()` — jangan susun route lacak manual).

**`sidebar-internal`** — Sidebar panel admin/kasir/superadmin. Latar putih, item aktif
maroon dengan indikator; kolaps menjadi menu di layar sempit. Komponen: `components/sidebar.blade.php`.

### Kartu & Kontainer

**`card-stat`** — Kartu statistik dashboard. Putih, border `{colors.brand-gray-extralight}`,
`{rounded.card}` (rounded-xl), `shadow-sm`, padding `{spacing.card-padding-lg}`. Berisi label
kecil abu + angka besar tebal.

**`card-order-konsumen`** — Kartu konten utama konsumen (pembayaran, ringkasan pesanan).
Putih, `{rounded.card-lg}` (rounded-2xl), shadow kartu `2px 4px 4px rgba(0,0,0,0.18)`,
padding 16–24px.

**`card-order-kasir`** — Kartu antrean pesanan. Menampilkan no pesanan (mono), meja, daftar
item ringkas ("+N Lainnya"), kotak `card-notes` bila ada catatan, dan baris aksi grid
`grid-cols-2 gap-3 sm:flex` berisi tombol Detail/Terima/Selesai/Cetak Struk.

**`card-notes`** — Kotak catatan pesanan. Latar `{colors.surface-soft}` (#F6F6F6),
radius 9px, judul `{typography.label}` warna `{colors.brand-dark}`, isi `line-clamp-2`.

**`chart-card`** — Kartu grafik (Chart.js 4 via CDN). Judul `{typography.title-card}` warna
brand-dark, kanvas tinggi tetap `h-64`, wrapper `relative`.

### Tabel

**`table-admin`** — Tabel data dashboard/kelola. Header `{typography.table-header}` warna
`{colors.brand-dark}` dengan border bawah; baris dipisah `divide-y` warna
`{colors.brand-gray-extralight}`; hover `hover:bg-brand-light/40`; sel `{typography.caption}`–
`{typography.body-sm}`; no pesanan memakai `font-mono text-xs`. Selalu dibungkus
`.kvt-scroll-region overflow-x-auto` + `tabindex="0"` (aksesibilitas scroll keyboard).

### Badge

**`badge-status-*`** — Pill status universal. Pola: `bg-{warna}/15` (kuning pakai `/20`) +
`text-{warna}` + ikon SVG kecil + `{typography.label}` medium, `rounded-full`, padding 4×10px.
Pemetaan: hijau = Selesai/Lunas, kuning = Diproses/Menunggu, merah = Gagal/Batal.
Ini adalah bahasa status tunggal aplikasi — dipakai di dashboard admin, kasir, dan halaman lacak.

### Form & Modal

**`text-input`** — Input teks/tanggal panel admin. Border 1px `{colors.maroon-alt}`,
teks maroon, radius 8px (`rounded-lg`), fokus `focus:ring-2` maroon. Label kecil abu di atas.

**`modal-confirm`** (`components/confirm-modal.blade.php`, `konsumen-confirm-modal`) —
Dialog konfirmasi dua-tombol (aksi + batal) di atas overlay 35% hitam. Dibuka/ditutup via
helper JS global `openAppModal(id)` / `closeAppModal(id)`.

**`popup-success`** (`components/popup-success.blade.php`) — Popup keberhasilan dengan
animasi Lottie, judul tebal, dan tombol tutup. Varian error memakai GIF `failed.gif`.

**`loading-overlay`** — Overlay `fixed inset-0` dengan spinner; ditampilkan saat submit form
yang lambat (toggle toko, cetak).

### Dokumen PDF

**`pdf-report`** — Template `admin/laporan-kasir-pdf`, `kasir/cetak-pesanan-pdf`,
`kasir/cetak-histori-pdf`, `konsumen/kuitansi`. Font DejaVu Sans; header tabel latar
`{colors.maroon-alt}` teks putih 11px; border sel `#ddd`; ringkasan total tebal di bawah;
footer "Dicetak pada: …" memakai `now()->translatedFormat(…)` (locale id, timezone WIB).

## Do's and Don'ts

### Do
- Pakai **token** `@theme` (`bg-brand-red`, `text-state-green`, `border-brand-gray-extralight`)
  untuk semua warna yang sudah tersedia — jangan menulis hex baru di Blade.
- Pertahankan radius 9px (`rounded-[9px]`) + shadow offset `2px 4px` untuk tombol konsumen/kasir.
- Pakai pola badge dua-warna (`bg-x/15 text-x`) untuk SEMUA status baru.
- Tambahkan `whitespace-nowrap` pada tombol yang berbagi baris; uji pada lebar kartu tersempit
  (grid 3 kolom ≈ 350px).
- Bungkus tabel lebar dengan `.kvt-scroll-region overflow-x-auto`.
- Jalankan `npm run build` setelah menambah class Tailwind baru, lalu pastikan class-nya
  benar-benar muncul di `public/build/assets/app-*.css` (Tailwind v4 hanya meng-compile class
  yang terdeteksi di source).
- Format uang dengan `number_format($n, 0, ',', '.')` dan awalan `Rp `.
- Gambar menu: selalu dukung dua sumber (storage path & URL absolut) dengan pengecekan prefix `http`.

### Don't
- Jangan memakai biru/ungu sebagai aksen — maroon adalah identitasnya; warna selain trinitas
  (maroon + status + grayscale) butuh alasan kuat.
- Jangan mengganti `rounded-[9px]` menjadi `rounded-lg` demi "kerapian" — 9px adalah tanda tangan.
- Jangan memakai shadow blur besar di panel konsumen; bayangannya offset keras, bukan glow.
- Jangan memakai `text-sm`/`text-base` Tailwind bercampur dengan `text-[14px]` di komponen sama.
- Jangan menaruh status sebagai teks polos — selalu badge pill.
- Jangan hardcode timezone atau format tanggal non-lokal; `now()` sudah WIB, gunakan
  `translatedFormat` untuk teks berbahasa Indonesia.
- Jangan menambah font family baru; Aileron (dan Georgia untuk aksen) sudah final.

## Responsive Behavior

### Breakpoints
Memakai breakpoint Tailwind default:

| Nama | Lebar | Perubahan kunci |
|---|---|---|
| Mobile (default) | < 640px | Panel konsumen: layout utama. Baris aksi kasir jadi `grid-cols-2` (Cetak Struk `col-span-2`). Sidebar internal kolaps. Footer/tabel 1 kolom |
| `sm` | ≥ 640px | Baris aksi menjadi flex sebaris dengan tinggi tetap `sm:h-11` |
| `md` | ≥ 768px | Kartu antrean kasir 2-up; kartu statistik mulai berjajar |
| `xl` | ≥ 1280px | Kartu antrean 3-up; dashboard admin layout penuh |
| `2xl` | ≥ 1536px | Kasir: list + panel detail berdampingan (`828px + 420px`) |

### Touch Targets
- Tombol utama konsumen tinggi ≥ 40px (`h-10`), tombol sekunder ≥ 32px (`h-8`).
- Item bottom-nav dan seluruh area kartu menu dapat ditap penuh.
- Spinner angka native disembunyikan (CSS global) — jumlah item diubah lewat tombol +/− besar.

### Collapsing Strategy
- Panel konsumen didesain mobile-first; di desktop tetap kolom sempit terpusat (konteksnya
  memang ponsel di meja kafe).
- Grid kartu mengurangi jumlah kolom, bukan mengecilkan kartu; teks tombol tidak boleh wrap
  (lihat aturan `whitespace-nowrap`).
- Tabel admin tidak pernah memotong kolom — ia scroll horizontal di dalam kartunya.
- Modal membatasi tinggi ke `100dvh - 2rem` dan scroll internal, aman untuk keyboard ponsel.

### Image Behavior
- `img, canvas, video { max-width: 100% }` global; gambar menu `object-cover` dalam rasio kartu.
- QR pembayaran dirender persegi (`aspect-square`, max 213px) dengan fallback kotak
  "QR belum siap" bila `qr_url` kosong.

## Iteration Guide

1. Kerjakan SATU komponen per iterasi; rujuk kunci YAML-nya (`{component.button-primary}`,
   `{component.badge-status-selesai}`).
2. Varian state (`-pressed`, `-disabled`, `-focused`) ditulis sebagai entri terpisah di
   `components:` — jangan menimpa entri dasar.
3. Selalu rujuk `{token}` — hindari hex inline. Bila butuh warna baru, tambahkan dulu ke
   `@theme` di `resources/css/app.css`, baru pakai class-nya.
4. Ubah/uji tampilan → `npm run build` → verifikasi class ada di CSS build → cek di dua lebar
   layar minimal (ponsel ±390px, desktop ≥1280px).
5. Status/interaksi yang didokumentasikan hanya Default dan Active/Pressed; hover mengikuti
   pola yang sudah ada (`brightness`, `bg` menggelap) — jangan menambah efek hover baru.
6. Trinitas warna = maroon + warna status + grayscale. Jangan menambah keluarga warna keempat.
7. Ragu soal penekanan? Perbesar/pertebal Aileron, jangan tambah warna.

## Known Gaps

- **Hex literal masih tersebar di Blade** — banyak view lama menulis `#681F1F`, `#460001`,
  `#CCCCCC` langsung alih-alih `bg-brand-red` dkk. Fungsinya sama, tapi menyulitkan re-theming.
  Rapikan bertahap saat menyentuh file terkait (jangan big-bang refactor).
- **`#380000` (maroon-alt) dan `#9C2C2C` (danger-deep)** belum menjadi token `@theme` dan
  secara visual sangat dekat dengan `brand-dark`/`brand-red` — kandidat konsolidasi.
- **Tidak ada dark mode** — seluruh sistem dirancang light-only.
- **Ikon belum satu sistem** — campuran file SVG di `public/images/icons/` dan inline SVG;
  belum ada komponen ikon terpusat.
- **Timing animasi GSAP/Lottie tidak terdokumentasi** — atribut `data-anim` (fade-up, stagger)
  digerakkan `resources/js/app.js`; durasi/easing belum dispesifikasikan di dokumen ini.
- **Chart.js dimuat via CDN** (bukan bundel Vite) — perlu koneksi internet saat demo dashboard;
  kandidat untuk dipindah ke dependency npm.
- **Template PDF memakai gaya sendiri** (inline CSS, DejaVu Sans) — token dokumen ini hanya
  memetakan warnanya; perubahan besar pada PDF perlu uji render DomPDF tersendiri.
- **Aileron tidak menyediakan weight 500/600** — hierarki hanya bisa 300/400/700; desain baru
  jangan mengandalkan medium/semibold.
