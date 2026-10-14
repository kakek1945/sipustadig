# Sipustadig

Sipustadig adalah aplikasi manajemen perpustakaan sekolah berbasis Laravel untuk mengelola anggota, buku, eksemplar, peminjaman, pengembalian, denda, katalog publik, laporan, pengaturan, audit log, dan import data.

## Fitur Utama

- Autentikasi dan role: admin, pustakawan, kepala sekolah, guru, siswa.
- Master data anggota, kategori, buku, dan eksemplar.
- Peminjaman multi-eksemplar dengan nomor pinjam otomatis dan due date otomatis.
- Pengembalian dengan kalkulasi keterlambatan dan denda otomatis.
- Pembayaran dan pembebasan denda dengan alasan wajib.
- OPAC/katalog publik dengan pencarian dan ketersediaan eksemplar.
- Dashboard pustakawan dan kepala sekolah.
- 11 jenis laporan dan export CSV.
- Pengaturan aturan peminjaman, denda, anggota, dan informasi perpustakaan.
- Audit log dan import CSV/XLSX untuk anggota, buku, dan eksemplar.

## Kebutuhan Sistem

- PHP sesuai lock dependency Composer. Saat ini `vendor` yang terpasang meminta PHP `>= 8.4.0`.
- Composer.
- Node.js dan npm.
- Database PostgreSQL/MySQL/SQLite sesuai konfigurasi `.env`.

## Setup Lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

Untuk development:

```bash
php artisan serve
npm run dev
```

Atau gunakan script gabungan:

```bash
composer run dev
```

## Testing

```bash
php artisan test
```

Catatan environment saat pengembangan ini: command `artisan test` belum bisa dieksekusi di mesin yang hanya memiliki PHP 8.2.28 karena dependency vendor meminta PHP `>= 8.4.0`. Verifikasi yang sudah berjalan: PHP lint, Laravel Pint, dan Vite build.

## Dokumentasi

- [User Manual](docs/USER_MANUAL.md)
- [Admin Guide](docs/ADMIN_GUIDE.md)
- [Accessibility Audit](docs/ACCESSIBILITY_AUDIT.md)
- [Production Checklist](docs/PRODUCTION_CHECKLIST.md)

## Deployment Ringkas

1. Siapkan PHP, ekstensi, Composer, Node.js, database, dan storage.
2. Set environment production di `.env`.
3. Jalankan `composer install --no-dev --optimize-autoloader`.
4. Jalankan `php artisan migrate --force`.
5. Jalankan `npm ci && npm run build`.
6. Jalankan cache production: `php artisan config:cache`, `route:cache`, `view:cache`.
7. Pastikan scheduler/queue berjalan jika fitur async mulai digunakan.
# pustaka-digital
# pustaka-digital
