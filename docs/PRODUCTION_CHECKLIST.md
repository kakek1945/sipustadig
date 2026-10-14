# Production Checklist Sipustadig

## Environment

- PHP runtime sesuai `composer.lock` dan `composer.json`.
- Ekstensi PHP umum aktif: mbstring, openssl, pdo, tokenizer, xml, ctype, fileinfo, json, zip jika import XLSX dipakai.
- Database production tersedia dan kredensial valid.
- `APP_ENV=production`.
- `APP_DEBUG=false`.
- `APP_KEY` sudah dibuat.
- `APP_URL` sesuai domain production.
- Mailer dikonfigurasi jika notifikasi dipakai.
- Storage link dibuat jika cover buku disimpan di disk public.

## Build dan Cache

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm ci
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Security

- Pastikan `.env` tidak dapat diakses publik.
- Pastikan directory permissions hanya writable untuk storage/cache.
- Gunakan HTTPS.
- Batasi akses panel hosting/database.
- Rotasi password akun admin default setelah deploy.
- Pastikan upload import dibatasi ukuran dan tipe file.

## Data

- Seed role/user awal jika diperlukan.
- Validasi pengaturan peminjaman dan denda.
- Import data master melalui mode preview.
- Verifikasi beberapa transaksi peminjaman/pengembalian dummy sebelum go-live.

## Backup

- Backup database sebelum dan sesudah migrasi.
- Siapkan jadwal backup harian.
- Uji restore minimal satu kali.
- Simpan backup di lokasi terpisah.

## Acceptance

- Login admin dan pustakawan berhasil.
- Katalog publik dapat dibuka.
- CRUD anggota, buku, dan eksemplar berjalan.
- Peminjaman, pengembalian, denda, dan perpanjangan berjalan.
- Laporan dan export CSV berjalan.
- Import preview dan import simpan berjalan.
- Audit log mencatat perubahan.
