# Admin Guide Sipustadig

Panduan ini ditujukan untuk admin sistem dan pustakawan senior.

## Role dan Hak Akses

- Admin: mengelola seluruh konfigurasi dan data.
- Pustakawan: mengelola anggota, buku, eksemplar, peminjaman, pengembalian, denda, laporan, audit, dan import.
- Kepala Sekolah: melihat dashboard dan laporan.
- Guru/Siswa: akses katalog dan fungsi peminjaman terkait jika diaktifkan.

## Pengaturan Sistem

Buka menu Pengaturan untuk mengubah:

- Lama peminjaman.
- Maksimal buku aktif.
- Maksimal perpanjangan.
- Batas keterlambatan.
- Denda harian.
- Denda maksimal.
- Kewajiban alasan saat pembebasan denda.
- Batas denda belum lunas anggota.
- Masa berlaku kartu anggota.
- Informasi perpustakaan.
- Profil beranda publik: visi, misi, tujuan, SOP, sambutan kepala sekolah, dan foto kepala sekolah.
- Auto-generate barcode dan QR code.

Perubahan pengaturan langsung dipakai oleh service peminjaman, pengembalian, dan denda.

## Beranda Publik

Beranda publik mengambil konten dari menu Pengaturan, bukan dari teks statis di kode. Admin dapat mengelola:

- Nama perpustakaan, nama sekolah, NSP/NPSN, alamat, kontak, email, dan jam layanan.
- Visi, misi, dan tujuan perpustakaan.
- Ringkasan SOP, alur layanan, dan tautan dokumen SOP.
- Nama, jabatan, sambutan, dan foto kepala sekolah.

Jika sebagian data belum diisi, beranda tetap menampilkan teks fallback agar halaman publik tetap rapi. Statistik publik hanya menampilkan data aman seperti jumlah judul, eksemplar tersedia, kategori, dan buku baru; data anggota, denda, dan transaksi tidak ditampilkan di beranda.

Foto kepala sekolah disimpan di disk `public`, jadi pastikan storage link aktif di environment produksi:

```bash
php artisan storage:link
```

## Import Data

Format yang didukung:

- CSV.
- XLSX sheet pertama dengan header yang sama seperti template, jika ekstensi PHP ZipArchive tersedia.

Template:

- `members`: `name,email,type,nisn,employee_number,class,year_level,phone,address,gender`
- `books`: `isbn,title,author,publisher,publish_year,category_code,rack_location,price,description,is_digital,digital_url,digital_source,digital_format,digital_grade_level,digital_access_note`
- `copies`: `book_isbn,copy_code,barcode,status,condition,acquisition_date,purchase_price,notes`

Gunakan mode preview untuk validasi sebelum menyimpan.

## Buku Digital SIBI/Kemendikdasmen

Buku digital disimpan sebagai metadata dan tautan resmi, bukan sebagai file digital di server. Saat menambah atau mengedit buku, centang **Buku Digital**, lalu isi link resmi dari `https://buku.kemendikdasmen.go.id/`.

Dampak buku digital:

- Muncul berdampingan dengan buku fisik di katalog publik dengan badge `Digital`.
- Detail katalog menampilkan tombol `Buka di SIBI/Kemendikdasmen` yang membuka tab baru ke sumber resmi.
- Tidak masuk stok eksemplar, peminjaman, pengembalian, atau denda.
- Bisa diimport lewat template `books` dengan kolom `is_digital=1` dan `digital_url` resmi.

## Audit Log

Audit log mencatat aktivitas penting dari request POST, PUT, PATCH, dan DELETE untuk modul utama. Admin dapat memfilter berdasarkan:

- Action.
- Model.
- Tanggal.
- User atau IP.

Detail audit menampilkan old values dan new values dalam format JSON.

## Laporan

Sipustadig menyediakan 11 jenis laporan:

1. Ringkasan Peminjaman.
2. Riwayat Peminjaman.
3. Ketersediaan Buku.
4. Aktivitas Anggota.
5. Laporan Denda.
6. Laporan Terlambat.
7. Katalog Lengkap.
8. Laporan Bulanan.
9. Laporan Tahunan.
10. Laporan Kerusakan.
11. Buku Populer.

Export menggunakan CSV agar kompatibel dengan Excel/LibreOffice.

## Backup Data

Minimal backup yang disarankan:

- Database harian.
- Direktori storage/public jika cover buku dipakai.
- File `.env` production secara aman di password manager.

Pastikan backup diuji dengan restore berkala.

## Maintenance

Command rutin:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

Setelah update kode:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm ci
npm run build
```
