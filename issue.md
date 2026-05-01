# Project Initialization: Kohvito Simpen

## Deskripsi Singkat
Inisiasi awal (setup) untuk project "Kohvito Simpen". Project ini akan dibangun menggunakan ekosistem Laravel modern yang digabungkan dengan TailwindCSS untuk styling, dan MySQL sebagai database utama. Karena tugas ini ditugaskan kepada Senior Programmer, instruksi berfokus pada arsitektur, standar kode, dan *best practices*, bukan langkah instalasi dasar.

## Tech Stack
- **Backend Framework:** Laravel (Versi 13.0)
- **Frontend / Styling:** TailwindCSS (via Vite, Versi 4.2)
- **Database:** MySQL (Versi 8.x)
- **Bahasa:** PHP (Versi >= 8.5)

## Objektif Tugas (High-Level)

### 1. Inisiasi Repositori & Kerangka Kerja
- [ ] Buat project Laravel baru langsung di root folder ini. Pastikan tidak ada folder *nested* (semua file core Laravel ada di tingkat teratas folder ini).
- [ ] Tentukan dan terapkan arsitektur desain (misalnya *Action Classes*, *Service Classes*, atau *Repository Pattern*) yang paling sesuai untuk skalabilitas dan kemudahan *maintenance* project ini. Pisahkan *business logic* dari Controller.
- [ ] Rapikan file `.env.example` agar memuat seluruh variabel environment esensial yang akan digunakan (konfigurasi Database, URL, integrasi *third-party* jika ada).

### 2. Konfigurasi Frontend (TailwindCSS)
- [ ] Integrasikan TailwindCSS ke dalam project menggunakan Vite.
- [ ] Siapkan arsitektur struktur view/blade components (seperti *Base Layout*, *Header*, *Footer*, dll).
- [ ] Definisikan *design tokens* awal di `tailwind.config.js` (seperti *primary color*, *secondary color*, *font family* khusus jika diperlukan) agar struktur UI konsisten sejak awal.

### 3. Konfigurasi Database (MySQL)
- [ ] Atur koneksi database untuk menggunakan driver MySQL.
- [ ] Desain dan buat *migration* awal untuk tabel *users* dan tabel dasar lainnya (yang bersifat fundamental untuk project). Pastikan index diatur dengan benar untuk *performance optimization*.
- [ ] Buat *Factory* dan *Seeder* yang komprehensif agar environment lokal bisa langsung mensimulasikan data *real* dengan cepat.

### 4. Standarisasi, Quality Assurance & Keamanan
- [ ] Integrasikan alat *static analysis* atau *code formatter* seperti **Laravel Pint** atau **PHPStan**.
- [ ] Implementasikan fondasi *global exception handling* dan struktur API response standar (jika ada *endpoint* API).
- [ ] Pastikan konfigurasi keamanan bawaan Laravel (CSRF, Middleware untuk *Authentication*, *Rate Limiting*) diatur secara optimal.

### 5. Dokumentasi
- [ ] Tulis ulang file `README.md` utama dengan instruksi jelas untuk *local development setup* agar memudahkan *onboarding* developer lain.
- [ ] Tambahkan deskripsi singkat di `README.md` mengenai pola arsitektur yang dipilih sehingga tim memiliki standar panduan *coding*.

## Kriteria Penerimaan (Acceptance Criteria)
- Perintah kompilasi aset (`npm run build` / `npm run dev`) berjalan tanpa error dan ukuran *bundle* sudah optimal untuk tahap awal.
- Tidak ada isu *N+1 query* yang disisipkan secara tidak sengaja di tahap inisialisasi awal.
- Project lulus uji standar kode (*linting pass*).
- Dokumentasi `README.md` terisi lengkap dan aplikatif.

## Catatan Tambahan
Fokus pada membangun fondasi yang kokoh, *scalable*, dan mudah dibaca (*clean code*). Hindari *over-engineering* di tahap awal, namun pastikan struktur folder sudah disiapkan untuk mengantisipasi penambahan fitur yang kompleks.
