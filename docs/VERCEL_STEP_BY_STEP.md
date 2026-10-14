# Panduan Step-by-Step Deploy Sipustadig ke Vercel

Dokumen ini panduan eksekusi end-to-end deploy aplikasi Sipustadig (Laravel 13 monolith) ke Vercel Container Runtime.

---

## Prasyarat Wajib Sebelum Deploy

Siapkan 3 resource eksternal ini lebih dulu:

1. **Akun Vercel** (hobby atau pro tier).
2. **Database PostgreSQL Hosted**:
   - Pilihan: [Neon](https://neon.tech) (recommended, serverless Postgres), [Supabase](https://supabase.com), atau [Railway](https://railway.app).
   - Catat: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
3. **Object Storage S3-Compatible**:
   - Pilihan: [Cloudflare R2](https://developers.cloudflare.com/r2/) (free egress), [AWS S3](https://aws.amazon.com/s3/), atau [Backblaze B2](https://www.backblaze.com/b2/).
   - Catat: `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_BUCKET`, `AWS_DEFAULT_REGION`, `AWS_ENDPOINT` (jika R2/B2), `AWS_URL` (public domain bucket).
4. **Git Repository** (GitHub / GitLab / Bitbucket) untuk integrasi CI/CD Vercel.

---

## Langkah 1: Inisialisasi Git dan Push ke Remote

Jalankan perintah ini di PowerShell / Git Bash dari root project:

```bash
# 1. Inisialisasi repo lokal (jika belum)
git init

# 2. Pastikan file rahasia tidak masuk staging
git status
# Periksa: .env tidak boleh muncul di untracked files

# 3. Stage semua file
git add .

# 4. Commit awal
git commit -m "feat: prepare Laravel Sipustadig for Vercel Container deployment"

# 5. Buat repository baru di GitHub/GitLab, lalu hubungkan:
git remote add origin https://github.com/<username>/<repo-name>.git
git branch -M main
git push -u origin main
```

---

## Langkah 2: Setup Database Production & Jalankan Migrasi

Jalankan migrasi database **sekali dari mesin lokal Anda** ke target database PostgreSQL remote.

```bash
# Generate APP_KEY production jika belum punya
php artisan key:generate --show

# Jalankan migrasi ke remote PostgreSQL
DB_CONNECTION=pgsql \
DB_HOST=<remote-host> \
DB_PORT=5432 \
DB_DATABASE=<remote-db-name> \
DB_USERNAME=<remote-user> \
DB_PASSWORD='<remote-password>' \
DB_SSLMODE=require \
php artisan migrate --force

# (Opsional) Jalankan seeder admin awal bila diperlukan
DB_CONNECTION=pgsql \
DB_HOST=<remote-host> \
DB_PORT=5432 \
DB_DATABASE=<remote-db-name> \
DB_USERNAME=<remote-user> \
DB_PASSWORD='<remote-password>' \
DB_SSLMODE=require \
php artisan db:seed --force
```

---

## Langkah 3: Import Project di Vercel Dashboard

1. Buka [https://vercel.com/new](https://vercel.com/new).
2. Pilih repository Git Sipustadig yang baru di-push.
3. **Project Name**: `sipustadig` (atau sesuai preferensi).
4. **Framework Preset**: Pilih **Other** (Vercel akan otomatis mendeteksi `Dockerfile.vercel` dan `vercel.json`).
5. **Root Directory**: `./` (default).

---

## Langkah 4: Masukkan Environment Variables di Vercel

Pada bagian **Environment Variables** di Vercel, tambahkan key-value berikut untuk environment **Production** dan **Preview**:

| Variable Name | Contoh / Value | Keterangan |
|---|---|---|
| `APP_NAME` | `Sipustadig` | Nama aplikasi |
| `APP_ENV` | `production` | Environment runtime |
| `APP_KEY` | `base64:...` | Hasil `php artisan key:generate --show` |
| `APP_DEBUG` | `false` | Wajib `false` di production |
| `APP_URL` | `https://<nama-project>.vercel.app` | Ganti dengan domain custom jika ada |
| `APP_LOCALE` | `id` | Bahasa aplikasi |
| `LOG_CHANNEL` | `stderr` | Menangkap log Laravel di Vercel Function logs |
| `LOG_LEVEL` | `warning` | Level logging |
| `DB_CONNECTION` | `pgsql` | Driver database |
| `DB_HOST` | `<host-postgresql>` | Domain DB provider |
| `DB_PORT` | `5432` | Port Postgres |
| `DB_DATABASE` | `<nama-database>` | Database name |
| `DB_USERNAME` | `<username>` | DB user |
| `DB_PASSWORD` | `<password>` | DB password |
| `DB_SSLMODE` | `require` | Enkripsi SSL Postgres |
| `SESSION_DRIVER` | `database` | Session disimpan di database Postgres |
| `SESSION_SECURE_COOKIE` | `true` | Cookie hanya via HTTPS |
| `CACHE_STORE` | `database` | Cache di table Postgres `cache` |
| `QUEUE_CONNECTION` | `sync` | Antrian dieksekusi sinkron (tanpa background daemon) |
| `FILESYSTEM_DISK` | `s3` | Driver storage foto & cover |
| `AWS_ACCESS_KEY_ID` | `<access-key>` | Kredensial S3/R2 |
| `AWS_SECRET_ACCESS_KEY` | `<secret-key>` | Kredensial S3/R2 |
| `AWS_DEFAULT_REGION` | `us-east-1` | Region bucket (R2 pakai `auto` atau `us-east-1`) |
| `AWS_BUCKET` | `<nama-bucket>` | Bucket name |
| `AWS_ENDPOINT` | `<endpoint-r2>` | Hanya jika pakai Cloudflare R2 / MinIO (kosongkan jika S3 native) |
| `AWS_URL` | `https://<public-bucket-domain>` | URL publik untuk load foto langsung dari CDN S3 |
| `MAIL_MAILER` | `log` | Ubah ke `smtp` jika konfigurasi email aktif |

---

## Langkah 5: Deploy

1. Klik tombol **Deploy** di Vercel.
2. Vercel akan:
   - Build OCI Container image via `Dockerfile.vercel`
   - Install dependency PHP via Composer
   - Build asset frontend (Tailwind v4, Alpine.js) via Vite
   - Cache config & routes Laravel
   - Deploy container ke Vercel Function
3. Tunggu hingga proses build selesai (biasanya 2-4 menit pada first build).

---

## Langkah 6: Verifikasi Pasca Deploy

Buka URL hasil deploy (misal `https://sipustadig.vercel.app`):

1. **Katalog Publik**: Buka `/catalog` — pastikan data buku tampil dan asset CSS/JS termuat sempurna.
2. **Autentikasi**: Buka `/login` — login dengan akun admin hasil seeding.
3. **Session Test**: Pindah antar tab/menu dashboard — pastikan login tidak ter-logout sendiri.
4. **Upload File**:
   - Tambah buku baru dengan cover image.
   - Tambah anggota baru dengan foto.
   - Cek apakah file masuk ke bucket S3/R2 dan gambar tampil di detail buku/anggota.
5. **Hapus File**:
   - Hapus buku yang baru dibuat — pastikan file di S3 otomatis terhapus.
6. **Sirkulasi**:
   - Coba transaksi peminjaman baru dan pengembalian.
7. **Laporan**:
   - Buka menu laporan dan coba export CSV.

---

## Checklist Pemeliharaan & Troubleshooting

- **Setiap ada update kode**: Cukup `git push origin main`. Vercel otomatis redeploy.
- **Setiap ada migration baru**: Jalankan `php artisan migrate --force` dari lokal/CI ke DB target **sebelum** atau **sesaat setelah** push.
- **Log Error**: Buka tab **Deployments** → Klik deployment aktif → Tab **Functions** → Monitor stream log `stderr`.
