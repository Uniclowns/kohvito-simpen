# Kohvito Simpen

Inisiasi awal project Kohvito Simpen menggunakan Laravel, TailwindCSS, dan MySQL.

## Tech Stack
- **Backend Framework:** Laravel 13.x
- **Frontend / Styling:** TailwindCSS v4.x (via Vite)
- **Database:** MySQL 8.x
- **Bahasa:** PHP >= 8.5

## Local Development Setup

### Prasyarat
- PHP >= 8.5
- Composer
- Node.js & npm
- MySQL (Laragon direkomendasikan)

### Instalasi
1. Clone repositori ini.
2. Jalankan `composer install`.
3. Jalankan `npm install`.
4. Salin `.env.example` ke `.env` (jika belum ada).
5. Konfigurasi database di `.env`.
6. Jalankan `php artisan key:generate`.
7. Jalankan `php artisan migrate`.

### Menjalankan Aplikasi
- Untuk server backend: `php artisan serve`
- Untuk aset frontend: `npm run dev`

### Standar Kode
Project ini menggunakan **Laravel Pint** untuk menjaga standar kode. Jalankan:
```bash
./vendor/bin/pint
```
