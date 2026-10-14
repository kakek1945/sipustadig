# Testing Deployment Vercel

## 1. Testing Lokal sebelum Deploy

### Setup environment testing

```bash
# Copy template production
cp .env.production.example .env.testing

# Isi nilai testing (gunakan database staging & S3 dev)
```

### Test S3 storage lokal

```bash
# Set filesystem ke S3 di .env.testing
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-test-key
AWS_SECRET_ACCESS_KEY=your-test-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-test-bucket
AWS_ENDPOINT=  # kosong jika AWS S3 asli, isi jika Cloudflare R2

# Build asset
npm run build

# Jalankan aplikasi dengan env testing
php artisan serve --env=testing
```

### Checklist test lokal

- [ ] Homepage `/` tampil
- [ ] Katalog `/catalog` tampil, pencarian bekerja
- [ ] Register user baru berhasil
- [ ] Login berhasil, session bertahan setelah refresh
- [ ] Logout berhasil
- [ ] CRUD anggota: create, read, update, delete
- [ ] Upload foto anggota → verifikasi di S3 bucket
- [ ] Hapus anggota → verifikasi foto hilang dari S3
- [ ] CRUD kategori
- [ ] CRUD buku, upload cover → verifikasi di S3
- [ ] CRUD eksemplar buku
- [ ] Peminjaman baru
- [ ] Pengembalian dengan denda otomatis
- [ ] Pembayaran denda
- [ ] Perpanjangan pinjaman
- [ ] Export CSV laporan
- [ ] Import CSV/XLSX (preview & simpan)
- [ ] Audit log mencatat aktivitas
- [ ] Ganti foto kepala sekolah di Settings → verifikasi S3
- [ ] Semua gambar tampil dengan URL S3, bukan `/storage/`

### Test build Vercel lokal (optional)

Jika punya Docker terinstal:

```bash
# Build image lokal
docker build -f Dockerfile.vercel -t sipustadig-test .

# Run container
docker run -p 8080:80 \
  -e APP_KEY=base64:your-key \
  -e DB_CONNECTION=sqlite \
  -e DB_DATABASE=/tmp/database.sqlite \
  sipustadig-test

# Test di browser
# http://localhost:8080
```

## 2. Testing Preview Vercel

Setelah push ke branch non-main, Vercel akan deploy **Preview** otomatis.

### Setup environment Preview

Di Vercel dashboard, set environment variables untuk **Preview**:
- Gunakan database staging (bukan production)
- Gunakan S3 bucket staging/dev (atau tetap main bucket dengan folder berbeda)

### Test di Preview URL

Akses URL preview (contoh: `https://sipustadig-abc123-username.vercel.app`).

#### Checklist Preview

- [ ] Halaman load tanpa 500 error
- [ ] Asset CSS/JS termuat (check Network tab, tidak ada 404 `/build/...`)
- [ ] Login berhasil
- [ ] Session bertahan antar page
- [ ] Database terkoneksi (data tampil, CRUD bekerja)
- [ ] Upload file berhasil, file tersimpan di S3 staging
- [ ] Gambar tampil dengan URL S3
- [ ] Export CSV berhasil
- [ ] Import CSV/XLSX berhasil
- [ ] Log error muncul di Vercel Functions log (jika ada error)

### Monitor log Preview

Di Vercel dashboard:
1. Pilih deployment Preview.
2. Klik **Functions** tab.
3. Lihat log stdout/stderr.

Jika ada error Laravel, akan muncul di log.

## 3. Testing Production

Setelah Promote ke Production atau merge ke `main`:

### Checklist Production

- [ ] Akses domain production
- [ ] SSL/HTTPS aktif
- [ ] Login admin berhasil
- [ ] Data production tampil (jika sudah migrasi)
- [ ] Upload file ke S3 production bucket
- [ ] Semua gambar tampil
- [ ] Export data berhasil
- [ ] Import data berhasil
- [ ] Tidak ada error di log
- [ ] Performance acceptable (response time < 2 detik untuk halaman berat)

### Performance test sederhana

```bash
# Test response time
curl -w "@curl-format.txt" -o /dev/null -s https://your-domain.com/

# Atau dengan ab (Apache Bench)
ab -n 100 -c 10 https://your-domain.com/
```

### Monitoring error

Setup Vercel Integrations untuk alert:
- **Sentry** untuk error tracking
- **Logflare/Datadog** untuk log aggregation
- **Slack webhook** untuk deployment notification

## 4. Rollback jika gagal

Jika deployment gagal atau ada bug kritis:

1. Di Vercel dashboard → Deployments.
2. Pilih deployment sebelumnya yang stabil.
3. Klik **...** → **Promote to Production**.
4. Atau revert commit di Git, push ulang.

## 5. Common issues & solusi

| Issue | Solusi |
|-------|--------|
| 500 error di semua halaman | Cek `APP_KEY` sudah set. Cek log Vercel Functions. Cek DB credentials. |
| Asset 404 | `npm run build` gagal? Cek build log. Pastikan `public/build/` ada. |
| Upload gagal | Cek S3 credentials, bucket permission, CORS config di S3. |
| Session hilang | `SESSION_DRIVER=database`, pastikan table `sessions` ada. |
| Database connection failed | Cek `DB_HOST`, `DB_PORT`, firewall, security group. Cek SSL mode. |
| Log tidak muncul | Pastikan `LOG_CHANNEL=stderr`. Vercel capture stdout/stderr. |
| Queue tidak jalan | `QUEUE_CONNECTION=sync` untuk Vercel (tidak ada worker). Untuk async, gunakan service eksternal. |
| Migration error | Jalankan migration manual dari lokal dengan koneksi DB production. |

## 6. Smoke test script otomatis (optional)

Buat script sederhana untuk test endpoint:

```bash
#!/bin/bash
# smoke-test.sh

DOMAIN=${1:-https://your-domain.com}

echo "Testing $DOMAIN..."

# Test homepage
curl -f -s -o /dev/null "$DOMAIN" && echo "✓ Homepage OK" || echo "✗ Homepage FAIL"

# Test catalog
curl -f -s -o /dev/null "$DOMAIN/catalog" && echo "✓ Catalog OK" || echo "✗ Catalog FAIL"

# Test login page
curl -f -s -o /dev/null "$DOMAIN/login" && echo "✓ Login page OK" || echo "✗ Login page FAIL"

# Test static asset (adjust manifest hash)
ASSET=$(curl -s "$DOMAIN/build/manifest.json" | grep -o '"[^"]*\.css"' | head -1 | tr -d '"')
curl -f -s -o /dev/null "$DOMAIN/build/$ASSET" && echo "✓ Asset OK" || echo "✗ Asset FAIL"

echo "Smoke test complete."
```

Jalankan:

```bash
chmod +x smoke-test.sh
./smoke-test.sh https://your-preview-url.vercel.app
```
