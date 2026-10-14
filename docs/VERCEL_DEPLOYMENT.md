# Deployment ke Vercel — Setup Database

## Persiapan Database Production

Sebelum deploy Laravel Sipustadig ke Vercel, siapkan database PostgreSQL eksternal. Pilih salah satu:

- **Neon** (https://neon.tech) — PostgreSQL managed, free tier available
- **Supabase** (https://supabase.com) — PostgreSQL + auth + storage
- **AWS RDS** — PostgreSQL managed AWS
- **Railway** (https://railway.app) — PostgreSQL managed, mudah setup

Setelah database siap, akan ada:
- `DB_HOST` (domain/IP)
- `DB_PORT` (default 5432)
- `DB_DATABASE` (nama database)
- `DB_USERNAME` (user)
- `DB_PASSWORD` (password)
- Connection string format: `postgresql://user:password@host:5432/database?sslmode=require`

## Migration Database Production Pertama Kali

Migration TIDAK otomatis saat build di Vercel. Jalankan secara manual dan terukur.

### Step 1: Backup database lokal (jika ada)

```bash
# SQLite lokal ke backup
cp database/database.sqlite database/database.sqlite.backup.$(date +%Y%m%d_%H%M%S)
```

### Step 2: Jalankan migration ke production database

**Opsi A: Via local machine (recommended dulu)**

```bash
# Isi .env production credentials lokal
APP_ENV=production \
DB_CONNECTION=pgsql \
DB_HOST=your-host.example.com \
DB_PORT=5432 \
DB_DATABASE=sipustadig \
DB_USERNAME=user \
DB_PASSWORD=your-password \
php artisan migrate --force
```

**Opsi B: Via Vercel CLI (setelah deploy)**

```bash
vercel env pull  # Pull env vars dari Vercel
php artisan migrate --force  # Jalankan lokal dengan env Vercel
```

### Step 3: Seed admin user awal (optional)

Jika ada seeder untuk role/admin awal:

```bash
php artisan db:seed --class=AdminSeeder --force
```

### Step 4: Verifikasi

```bash
# Connect ke database production
psql -h your-host.example.com -U user -d sipustadig

# List tables
\dt

# Check users table
SELECT * FROM users LIMIT 1;

# Exit
\q
```

### Step 5: Backup setelah migrasi

```bash
# PostgreSQL dump (dari production)
pg_dump -h your-host.example.com -U user -d sipustadig > backup_production_$(date +%Y%m%d).sql
```

Simpan backup di lokasi aman (Google Drive, AWS S3, atau NAS lokal).

## Cache Warmup (optional)

Untuk production, cache config dan route:

```bash
DB_CONNECTION=pgsql php artisan config:cache
DB_CONNECTION=pgsql php artisan route:cache
```

Catatan: Ini sudah otomatis di `Dockerfile.vercel` pada build.

## Rollback jika ada error

Jika migration gagal atau ada error:

1. Snapshot/backup database production.
2. Jalankan rollback:
   ```bash
   php artisan migrate:rollback --force
   ```
3. Fix kode/migration.
4. Re-migrate:
   ```bash
   php artisan migrate --force
   ```

## Environment Variables di Vercel

Set di Vercel dashboard **Production** environment:

```
DB_CONNECTION=pgsql
DB_HOST=<host>
DB_PORT=5432
DB_DATABASE=<database>
DB_USERNAME=<username>
DB_PASSWORD=<password>
DB_SSLMODE=require

AWS_ACCESS_KEY_ID=<key>
AWS_SECRET_ACCESS_KEY=<secret>
AWS_DEFAULT_REGION=<region>
AWS_BUCKET=<bucket>
AWS_ENDPOINT=<optional-for-r2-b2>
AWS_URL=<public-url>
```

Jangan gunakan `.env` file — hanya environment variables di Vercel.

## Backup Harian Production

Setup backup otomatis via cron atau native database backup:

**PostgreSQL via pg_dump (recommended)**

```bash
#!/bin/bash
BACKUP_DIR="/backup/db"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
pg_dump -h $DB_HOST -U $DB_USER -d $DB_NAME > $BACKUP_DIR/backup_$TIMESTAMP.sql
gzip $BACKUP_DIR/backup_$TIMESTAMP.sql
# Upload ke S3 atau cloud storage
```

Jalankan setiap hari via cron job di server admin atau database provider native backup.

## Troubleshooting

**Migration timeout?**
- Tingkatkan timeout di `.env`: `DB_TIMEOUT=60`
- Jalankan per batch: `php artisan migrate --step`

**Connection refused?**
- Verifikasi credentials di `.env`.
- Cek firewall/security group database allow akses dari IP Vercel (jika perlu, buat whitelist).
- Test koneksi: `psql -h host -U user -d database`

**Foreign key constraint error?**
- Urutan migrasi penting. Cek `database/migrations/` order.
- Jika sudah, gunakan migration fresh (hati-hati, menghapus data):
  ```bash
  php artisan migrate:fresh --force
  ```

**Data tidak tampil setelah migrasi?**
- Cek apakah user admin sudah ada.
- Import data via fitur import Sipustadig atau SQL script.
