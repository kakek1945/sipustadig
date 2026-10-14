# PRD - Product Requirements Document
# Sipustadig / Sistem Informasi Perpustakaan Sekolah

**Nama Produk:** Sipustadig
**Versi PRD:** 2.0 (Revised)
**Tanggal:** 2026-05-22
**Target Tahap Awal:** MVP siap dipakai pustakawan sekolah
**Framework:** Laravel 13.x
**Database:** PostgreSQL di Neon
**Progress Aktual:** MVP lokal hampir siap rilis; deployment produksi dan acceptance masih pending
**Rencana Deploy Awal:** Vercel + Neon (dengan migrasi plan ke hosting Laravel-friendly)
**Status:** MVP lokal hampir siap rilis; deployment produksi dan acceptance masih pending

---

## Status Progress Saat Ini

**Update:** 2026-05-23

- Phase 0-5 MVP sudah diimplementasikan untuk alur utama: auth/RBAC, master data, eksemplar, transaksi pinjam-kembali, perpanjangan, denda, katalog, dashboard, laporan, pengaturan, audit log, import, dan polish UI.
- Branding aplikasi sudah diganti menjadi **Sipustadig**, logo sekolah sudah terpasang, sidebar dipadatkan, tombol keluar ditambahkan, dan modal kategori diperbaiki agar konsisten dengan tampilan lain.
- Alur scanner barcode saat ini memakai mode scanner fisik yang bekerja seperti input keyboard. Nilai scan diproses sebagai `copy_code` atau `barcode` pada aktivitas peminjaman, pengembalian, pencarian eksemplar, cetak label, dan import data.
- Verifikasi lokal terakhir: `npm run build`, `vendor/bin/pint --dirty`, dan syntax check file terkait berhasil.
- Full `php artisan test` belum bisa dijalankan di environment lokal karena PHP tersedia 8.2.28, sedangkan dependency Composer membutuhkan PHP `>= 8.4.0`.
- Masih pending: setup production environment, final deployment, DNS/SSL, backup verification, final test suite di PHP 8.4+, dan stakeholder acceptance.

---

## 1. Ringkasan Eksekutif

**Sipustadig** adalah sistem informasi perpustakaan sekolah berbasis web yang dirancang untuk menggantikan pencatatan manual menggunakan buku besar atau spreadsheet. Sistem ini menyediakan manajemen koleksi buku digital,Otomatisasi transaksi peminjaman/pengembalian, pemantauan keterlambatan dan denda, katalog online untuk siswa/guru, serta laporan otomatis untuk pustakawan dan manajemen sekolah.

**Tujuan Utama:**
- Menghilangkan pencatatan manual yang rawan error
- Mempercepat proses pinjam-kembali dari 5-10 menit menjadi <1 menit
- Menyediakan data real-time untuk pengambilan keputusan
- Mengurangi kehilangan buku dan keterlambatan pengembalian

**Target Pengguna:**
- Pustakawan (pengguna utama, ~100-500 transaksi/minggu)
- Siswa (pengguna katalog, ~200-1000 siswa)
- Guru (pengguna katalog + peminjaman, ~20-100 guru)
- Kepala Sekolah (pembaca laporan)

**Metrik Keberhasilan MVP:**
- Waktu transaksi pinjam-kembali < 60 detik
- 100% data anggota, buku, dan transaksi tersimpan digital
- Laporan bulanan siap dalam < 30 detik
- Uptime 99% (diæ‰˜ç®¡ yang sesuai)

---

## 2. Visi dan Prinsip Desain

### 2.1 Visi Produk

> *"perpustakaan digital yang membuat pustakawan bekerja lebih efisien, siswa lebih semangat membaca, dan kepala sekolah punya visibilitas penuh."*

### 2.2 Prinsip Desain

1. **Kesederhanaan** - UI yang intuitif, pustakawan non-IT bisa pakai tanpa pelatihan panjang
2. **Kecepatan** - Setiap aksi selesai < 3 detik
3. **Reliabilitas** - Data tidak boleh hilang atau corrupt
4. **Skalabilitas** - Arsitektur mendukung 10x pertumbuhan tanpa refactor besar
5. **Keamanan** - Data sensitif terlindungi, akses sesuai role

### 2.3 User Stories MVP

|Sebagai|Saya ingin|Benefit|
|-------|---------|-------|
| Pustakawan | Scan barcode buku untuk pinjam | Hemat waktu, kurangi human error |
| Pustakawan | Lihat semua buku telat dalam 1 klik | Prioritas kerja jelas |
| Siswa | Cari buku dari HP tanpa ke perpustakaan | Akses informasi lebih mudah |
| Siswa | Lihat buku yang saya pinjam sekarang | Tau kapan harus kembali |
| Kepala Sekolah | Lihat laporan bulanan otomatis | Tidak perlu minta pustakawan compile manual |
| Admin | Backup data 1 klik | Ketenangan pikiran |

---

## 3. Target Pengguna & Persona

### 3.1 Pustakawan - "Rina" (35 tahun)
- **Kebutuhan:** Sistem yang cepat, error-tolerant, tidak perlu skill komputer tinggi
- **Pain Points:** Excel yang corrupt, buku yang "hilang" tanpa jejak, laporan bulanan yang makan waktu
- **Goal:** Selesaipin kerja 30 menit lebih cepat dari biasanya

### 3.2 Siswa - "Andi" (13 tahun)
- **Kebutuhan:** Cek katalog dari HP, tahu kapan harus balikin buku
- **Pain Points:** Tidak tahu buku tersedia atau tidak sebelum ke perpustakaan
- **Goal:** Dapat buku yang saya mau tanpa bertanya-tanya

### 3.3 Kepala Sekolah - "Pak Budi"
- **Kebutuhan:** Laporan singkat, angka-angka penting, tren baca siswa
- **Pain Points:** Harus minta laporan manual ke pustakawan
- **Goal:** Monitor budaya literasi sekolah dengan mudah

### 3.4 Admin Sistem - "IT Support"
- **Kebutuhan:** Mudah setup, mudah maintenance, dokumentasi lengkap
- **Pain Points:** Hosting yang ribet, error yang sulit debug
- **Goal:** Setup sekali, jalan sendiri

---

## 4. Fitur MVP

### 4.1 Fitur Wajib (Must Have)

#### A. Authentication & Authorization
- Login dengan email/password
- Logout otomatis setelah 2 jam tidak aktif
- Role-based access control (RBAC)
- Session management (database-driven untuk serverless)

#### B. Manajemen Anggota
- CRUD anggota (NIS/NIP, nama, tipe, kelas, kontak)
- Tipe anggota: Siswa, Guru, Staf
- Status aktif/nonaktif
- Filter dan pencarian
- Import dari Excel (template provided)
- Export ke Excel/CSV
- Nomor anggota auto-generate sesuai format

#### C. Manajemen Kategori Buku
- CRUD kategori
- Slug auto-generate
- Deskripsi opsional

#### D. Manajemen Buku
- CRUD bibliografi (ISBN, judul, penulis, penerbit, tahun, kategori)
- Upload cover (max 2MB, stored externally)
- Tags/keywords
- Soft delete (nonaktifkan, bukan hapus permanen)
- Filter kategori, penulis, tahun
- Pencarian judul/penulis/ISBN
- Import dari Excel
- Export ke Excel/CSV

#### E. Manajemen Eksemplar
- Generate kode eksemplar unik (format: [KODE_BUKU]-[NOMOR])
- Generate barcode/QR code
- Dukungan scanner barcode fisik berbasis input keyboard untuk membaca `copy_code` atau `barcode`
- Status eksemplar: Tersedia, Dipinjam, Rusak, Hilang, Perbaikan, Diarsipkan
- Kondisi: Baik, Cukup Baik, Rusak Ringan, Rusak Berat
- Lokasi rak
- Riwayat perubahan kondisi

#### F. Transaksi Peminjaman
- Scan/input anggota
- Scan/input eksemplar
- Scanner fisik dapat langsung mengisi kode eksemplar/barcode pada field transaksi
- Validasi otomatis:
  - Anggota aktif?
  - Denda lunas?
  - Boleh pinjam lebih?
  - Eksemplar tersedia?
- Generate nomor transaksi
- Tanggal jatuh tempo auto (berdasarkan tipe anggota)
- Tampilkan ringkasan sebelum konfirmasi
- Transaksi dalam database transaction

#### G. Transaksi Pengembalian
- Scan/input eksemplar
- Scanner fisik dapat dipakai untuk menemukan transaksi aktif berdasarkan eksemplar
- Cari transaksi aktif
- Hitung keterlambatan (kalender, bukan hari kerja)
- Hitung denda otomatis
- Input kondisi saat kembali
- Update status eksemplar
- Generate denda jika applicable

#### H. Perpanjangan
- Perpanjang jatuh tempo
- Maksimal 2x perpanjangan
- Tidak bisa perpanjang jika telat
- Catat setiap perpanjangan

#### I. Denda
- Denda keterlambatan otomatis
- Denda manual (rusak/hilang)
- Pembayaran denda
- Pembebasan denda (wajib alasan)
- Filter: lunas, belum lunas, dibebaskan
- Riwayat pembayaran

#### J. Katalog Online (OPAC)
- Akses publik (setelah login)
- Pencarian judul, penulis, ISBN
- Filter kategori
- Detail buku (cover, deskripsi, jumlah tersedia)
- Lokasi rak
- Tampilkan eksemplar yang tersedia

#### K. Dashboard
**Dashboard Pustakawan:**
- Total koleksi
- Total eksemplar
- Eksemplar tersedia vs dipinjam
- Buku terlambat (count + list)
- Denda belum lunas
- Transaksi hari ini
- Grafik peminjaman 7 hari terakhir

**Dashboard Kepala Sekolah:**
- Summary metrics
- Buku paling sering dipinjam (top 10)
- Siswa paling aktif
- Grafik peminjaman bulanan
- Trend keterlambatan

#### L. Laporan
- Laporan data buku
- Laporan data eksemplar
- Laporan anggota
- Laporan peminjaman (harian, mingguan, bulanan)
- Laporan pengembalian
- Laporan keterlambatan
- Laporan denda
- Laporan buku populer
- Laporan anggota aktif
- Export: Excel (prioritas), CSV, PDF (bila memungkinkan)

#### M. Pengaturan Perpustakaan
- Nama perpustakaan & sekolah
- Logo sekolah
- Aturan peminjaman:
  - Maksimal pinjam: Siswa=2, Guru=5, Staf=3
  - Durasi pinjam: Siswa=7 hari, Guru=14 hari, Staf=7 hari
  - Denda per hari: Rp1.000,- (default)
- Maksimal perpanjangan: 2x
- Format nomor anggota
- Format kode eksemplar

#### N. Audit Log
- Log create, update, delete
- Log transaksi penting
- User, timestamp, old/new values
- Filterable

### 4.2 Fitur Opsional (Nice to Have - Phase 2+)
- [ ] Cetak kartu anggota
- [ ] Scan barcode via kamera HP
- [ ] Reservasi buku
- [ ] Reminder otomatis (WhatsApp/email)
- [ ] Review dan rating buku
- [ ] Dashboard literasi sekolah
- [ ] Mobile app

---

## 5. Aturan Bisnis

### 5.1 Aturan Peminjaman

| Aturan | Siswa | Guru | Staf |
|--------|-------|------|------|
| Maksimal pinjam | 2 | 5 | 3 |
| Durasi pinjam | 7 hari | 14 hari | 7 hari |
| Maksimal perpanjangan | 2x | 2x | 2x |
| Denda per hari telat | Rp1.000 | Rp1.000 | Rp1.000 |

### 5.2 Kondisi Eksemplar Saat Pengembalian

| Kondisi | Aksi |
|---------|------|
| Baik | Tersedia kembali |
| Cukup Baik | Tetap tersedia, catatan "perhatikan" |
| Rusak Ringan | Tersedia, catat di history |
| Rusak Berat | Status = Rusak, denda manual |
| Hilang | Status = Hilang, denda Rp harga buku + 50% |

### 5.3 Status Anggota

| Status | Boleh Pinjam? | Boleh Login? |
|--------|--------------|--------------|
| Aktif | Ya | Ya |
| Nonaktif | Tidak | Tidak |

### 5.4 Validasi Sebelum Pinjam

```
1. Cek anggota aktif?
   â†’ Jika tidak: tolak dengan pesan "Anggota nonaktif"

2. Cek denda belum lunas?
   â†’ Jika ada: tolak dengan pesan "Ada denda belum lunas RpX"

3. Cek jumlah pinjam aktif?
   â†’ Jika >= batas maksimal: tolak dengan pesan "Sudah mencapai batas pinjam"

4. Cek eksemplar tersedia?
   â†’ Jika tidak: tolak dengan pesan "Buku sedang dipinjam"

5. Jika semua OK: proceed transaksi
```

---

## 6. Data Model

### 6.1 Entity Relationship Overview

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”     â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”     â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚   User   â”‚â”€â”€â”€â”€<â”‚  Member   â”‚â”€â”€â”€â”€<â”‚   Loan     â”‚
â”‚          â”‚     â”‚           â”‚     â”‚ Transactionâ”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜     â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜     â””â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”˜
                                         â”‚
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”     â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”     â”Œâ”€â”€â”€â”€â”€â”´â”€â”€â”€â”€â”€â”€â”
â”‚  Setting â”‚     â”‚   Book    â”‚â”€â”€â”€â”€<â”‚  Loan Item â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜     â”‚           â”‚     â”‚            â”‚
                â””â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜     â””â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”˜
                      â”‚                   â”‚
                â”Œâ”€â”€â”€â”€â”€â”´â”€â”€â”€â”€â”€â”        â”Œâ”€â”€â”€â”€â”´â”€â”€â”€â”€â”
                â”‚Book Copy  â”‚        â”‚   Fine  â”‚
                â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜        â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

### 6.2 User & Auth

**users**
| Field | Type | Description |
|-------|------|-------------|
| id | bigint PK | Auto increment |
| name | varchar(255) | Nama lengkap |
| email | varchar(255) | Unique, untuk login |
| password | varchar(255) | Hash bcrypt |
| role | enum | admin, librarian, principal, teacher, student |
| status | enum | active, inactive |
| last_login_at | timestamp |nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### 6.3 Member Management

**members**
| Field | Type | Description |
|-------|------|-------------|
| id | bigint PK | |
| user_id | bigint FKâ†’users |nullable, untuk siswa/guru yang login |
| member_number | varchar(50) | Unique, format: [TAHUN]-[SEQUENCE] |
| type | enum | student, teacher, staff |
| name | varchar(255) | |
| identity_number | varchar(50) | NIS/NIP |
| class_name | varchar(50) |nullable, untuk siswa |
| major | varchar(100) |nullable, untuk siswa SMP/SMA |
| email | varchar(255) |nullable |
| phone | varchar(20) |nullable |
| address | text |nullable |
| photo_path | varchar(255) |nullable, stored externally |
| status | enum | active, inactive |
| notes | text |nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**book_categories**
| Field | Type | Description |
|-------|------|-------------|
| id | bigint PK | |
| name | varchar(100) | Unique |
| slug | varchar(100) | Unique, auto from name |
| description | text |nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### 6.4 Book Management

**books**
| Field | Type | Description |
|-------|------|-------------|
| id | bigint PK | |
| category_id | bigint FKâ†’book_categories |nullable |
| title | varchar(255) | |
| isbn | varchar(20) |nullable |
| author | varchar(255) | |
| publisher | varchar(255) |nullable |
| published_year | int |nullable |
| language | varchar(50) |nullable, default 'Indonesia' |
| pages | int |nullable |
| description | text |nullable |
| cover_path | varchar(255) |nullable, stored externally |
| tags | json |nullable, array of strings |
| status | enum | active, inactive |
| created_by | bigint FKâ†’users | |
| created_at | timestamp | |
| updated_at | timestamp | |

**book_copies**
| Field | Type | Description |
|-------|------|-------------|
| id | bigint PK | |
| book_id | bigint FKâ†’books | |
| copy_code | varchar(50) | Unique, format: [BOOK_CODE]-[NUM] |
| barcode_value | varchar(100) | Unique, untuk scan |
| shelf_location | varchar(100) |nullable, misal "Rak A-3" |
| condition | enum | good, fair, lightly_damaged, heavily_damaged, lost |
| status | enum | available, borrowed, damaged, lost, repair, archived |
| source | enum | purchase, grant, donation, government_aid, other |
| acquisition_date | date |nullable |
| price | decimal(10,2) |nullable |
| notes | text |nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### 6.5 Transactions

**loan_transactions**
| Field | Type | Description |
|-------|------|-------------|
| id | bigint PK | |
| loan_number | varchar(50) | Unique, format: TRX-[YYYYMMDD]-[SEQ] |
| member_id | bigint FKâ†’members | |
| borrowed_by | bigint FKâ†’users | Pustakawan yang memproses |
| borrowed_at | timestamp | |
| due_at | date | |
| returned_at | timestamp |nullable |
| status | enum | active, returned, overdue, lost, cancelled |
| notes | text |nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**loan_items**
| Field | Type | Description |
|-------|------|-------------|
| id | bigint PK | |
| loan_transaction_id | bigint FKâ†’loan_transactions | |
| book_copy_id | bigint FKâ†’book_copies | |
| borrowed_at | timestamp | |
| due_at | date | |
| returned_at | timestamp |nullable |
| return_condition | enum |nullable, kondisi saat kembali |
| extension_count | int | Default 0, max 2 |
| status | enum | borrowed, returned, overdue, lost, damaged |
| fine_id | bigint FKâ†’fines |nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### 6.6 Fines & Payments

**fines**
| Field | Type | Description |
|-------|------|-------------|
| id | bigint PK | |
| member_id | bigint FKâ†’members | |
| loan_item_id | bigint FKâ†’loan_items |nullable |
| type | enum | late, damaged, lost, manual |
| amount | decimal(10,2) | Total denda |
| paid_amount | decimal(10,2) | Default 0, jumlah yang sudah dibayar |
| status | enum | unpaid, partial, paid, waived |
| notes | text |nullable |
| waived_reason | text |nullable, wajib jika waived |
| created_by | bigint FKâ†’users | |
| paid_at | timestamp |nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**fine_payments**
| Field | Type | Description |
|-------|------|-------------|
| id | bigint PK | |
| fine_id | bigint FKâ†’fines | |
| amount | decimal(10,2) | |
| payment_method | enum |nullable, cash, transfer, other |
| paid_by | bigint FKâ†’users |nullable, pustakawan yang terima |
| paid_at | timestamp | |
| notes | text |nullable |
| created_at | timestamp | |

### 6.7 System

**library_settings**
| Field | Type | Description |
|-------|------|-------------|
| id | bigint PK | |
| key | varchar(100) | Unique |
| value | text | |
| type | enum | string, number, boolean, json |
| description | text |nullable |
| group | varchar(50) | Untuk grouping di UI |
| updated_by | bigint FKâ†’users |nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**imports**
| Field | Type | Description |
|-------|------|-------------|
| id | bigint PK | |
| type | varchar(50) | member, book, book_copy |
| file_path | varchar(255) | |
| status | enum | pending, processing, completed, failed |
| total_rows | int | |
| success_rows | int | |
| failed_rows | int | |
| error_report_path | varchar(255) |nullable |
| created_by | bigint FKâ†’users | |
| created_at | timestamp | |
| updated_at | timestamp | |

**audit_logs**
| Field | Type | Description |
|-------|------|-------------|
| id | bigint PK | |
| user_id | bigint FKâ†’users |nullable |
| action | varchar(50) | create, update, delete, login, logout, dll |
| auditable_type | varchar(255) | Model class name |
| auditable_id | bigint | |
| old_values | jsonb |nullable |
| new_values | jsonb |nullable |
| ip_address | varchar(45) |nullable |
| user_agent | text |nullable |
| created_at | timestamp | |

---

## 7. API Specification

### 7.1 Public Endpoints (Authenticated)

```
GET  /api/catalog                    # List buku dengan filter
GET  /api/catalog/{book}             # Detail buku
GET  /api/my/loans                   # Pinjaman aktif user login
GET  /api/my/history                 # Riwayat pinjam user login
GET  /api/my/fines                   # Denda user login
```

### 7.2 Dashboard API

```
GET  /api/dashboard/stats            # Stats untuk dashboard
GET  /api/dashboard/overdue           # List buku telat
GET  /api/dashboard/chart            # Data grafik
```

### 7.3 Member API (Admin/Librarian)

```
GET    /api/members                  # List dengan pagination
POST   /api/members                  # Create
GET    /api/members/{id}             # Detail
PUT    /api/members/{id}             # Update
DELETE /api/members/{id}             # Soft delete (nonaktifkan)
POST   /api/members/import           # Import Excel
GET    /api/members/export           # Export
```

### 7.4 Book API (Admin/Librarian)

```
GET    /api/books                    # List dengan pagination & filter
POST   /api/books                    # Create
GET    /api/books/{id}               # Detail
PUT    /api/books/{id}               # Update
DELETE /api/books/{id}               # Soft delete
POST   /api/books/{id}/covers        # Upload cover
POST   /api/books/import             # Import Excel
GET    /api/books/export             # Export
```

### 7.5 Book Copy API (Admin/Librarian)

```
GET    /api/book-copies              # List dengan pagination
POST   /api/book-copies              # Create single
POST   /api/book-copies/generate     # Generate multiple
GET    /api/book-copies/{id}         # Detail
PUT    /api/book-copies/{id}         # Update
GET    /api/book-copies/{id}/qr      # Generate QR code
GET    /api/book-copies/{id}/label   # Print label
```

### 7.6 Loan API (Admin/Librarian)

```
GET    /api/loans                    # List dengan filter
POST   /api/loans                    # Create (pinjam)
GET    /api/loans/{id}               # Detail
POST   /api/loans/{id}/return        # Pengembalian
POST   /api/loans/{id}/extend        # Perpanjangan
POST   /api/loans/{id}/cancel        # Batalkan
```

### 7.7 Fine API (Admin/Librarian)

```
GET    /api/fines                    # List dengan filter
GET    /api/fines/{id}               # Detail
POST   /api/fines/{id}/pay           # Bayar
POST   /api/fines/{id}/waive         # Bebaskan
POST   /api/fines                    # Create manual
```

### 7.8 Report API (Admin/Librarian, Principal)

```
GET    /api/reports/books            # Laporan buku
GET    /api/reports/loans            # Laporan peminjaman
GET    /api/reports/returns          # Laporan pengembalian
GET    /api/reports/overdue          # Laporan keterlambatan
GET    /api/reports/fines            # Laporan denda
GET    /api/reports/popular          # Laporan buku populer
GET    /api/reports/active-members   # Laporan anggota aktif
```

### 7.9 Settings API (Admin)

```
GET    /api/settings                 # List semua settings
PUT    /api/settings                 # Update multiple
GET    /api/settings/{key}          # Get single
PUT    /api/settings/{key}          # Update single
```

### 7.10 Audit Log API (Admin)

```
GET    /api/audit-logs               # List dengan filter
GET    /api/audit-logs/{id}         # Detail
```

---

## 8. Wireframe & UI Guidelines

### 8.1 Design System

**Color Palette:**
- Primary: `#2563EB` (Blue 600)
- Primary Dark: `#1D4ED8` (Blue 700)
- Secondary: `#059669` (Emerald 600)
- Accent: `#F59E0B` (Amber 500)
- Danger: `#DC2626` (Red 600)
- Background: `#F9FAFB` (Gray 50)
- Surface: `#FFFFFF`
- Text Primary: `#111827` (Gray 900)
- Text Secondary: `#6B7280` (Gray 500)
- Border: `#E5E7EB` (Gray 200)

**Typography:**
- Font Family: Inter (Google Fonts)
- Headings: 600-700 weight
- Body: 400-500 weight
- Size Scale: 12, 14, 16, 18, 20, 24, 30, 36, 48

**Spacing:**
- Base unit: 4px
- Common: 8px, 12px, 16px, 24px, 32px, 48px

**Border Radius:**
- Buttons: 6px
- Cards: 8px
- Modals: 12px
- Inputs: 6px

### 8.2 Layout Structure

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ Header: Logo | Search | User Menu                           â”‚
â”œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¤
â”‚          â”‚                                                  â”‚
â”‚ Sidebar  â”‚  Main Content Area                              â”‚
â”‚ (240px)  â”‚                                                  â”‚
â”‚          â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”    â”‚
â”‚ - Logo   â”‚  â”‚ Page Header: Title + Action Buttons    â”‚    â”‚
â”‚ - Nav    â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜    â”‚
â”‚   Items  â”‚                                                  â”‚
â”‚          â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”    â”‚
â”‚          â”‚  â”‚ Content: Table / Form / Cards          â”‚    â”‚
â”‚          â”‚  â”‚                                         â”‚    â”‚
â”‚          â”‚  â”‚                                         â”‚    â”‚
â”‚          â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜    â”‚
â”‚          â”‚                                                  â”‚
â”œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”´â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¤
â”‚ Footer: Version | Copyright                                 â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

### 8.3 Page Templates

**List Page:**
```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ Members                                          [+ Add]   â”‚
â”œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¤
â”‚ ðŸ” Search...                    [Filter â–¼] [Export â–¼]      â”‚
â”œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¤
â”‚ â˜ â”‚ Name          â”‚ Type    â”‚ Class   â”‚ Status â”‚ Actions â”‚
â”‚â”€â”€â”€â”‚â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”‚â”€â”€â”€â”€â”€â”€â”€â”€â”€â”‚â”€â”€â”€â”€â”€â”€â”€â”€â”€â”‚â”€â”€â”€â”€â”€â”€â”€â”€â”‚â”€â”€â”€â”€â”€â”€â”€â”€â”€â”‚
â”‚ â˜ â”‚ Andi Wijaya   â”‚ Student â”‚ VII-A   â”‚ Active â”‚ â‹®       â”‚
â”‚ â˜ â”‚ Budi Santoso  â”‚ Teacher â”‚ -       â”‚ Active â”‚ â‹®       â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
â”‚ â—€ 1 2 3 4 5 â–¶                    Showing 1-10 of 150     â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

**Transaction Page (Pinjam):**
```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ New Loan Transaction                                        â”‚
â”œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¤
â”‚                                                             â”‚
â”‚  1ï¸âƒ£ SCAN MEMBER                                             â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”   â”‚
â”‚  â”‚ [Scan/Input Member Code...]                    ðŸ”    â”‚   â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜   â”‚
â”‚                                                             â”‚
â”‚  Member Info (appears after scan):                          â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                                       â”‚
â”‚  â”‚ ðŸ‘¤ Andi Wijaya   â”‚                                       â”‚
â”‚  â”‚    Class: VII-A  â”‚                                       â”‚
â”‚  â”‚    Active Loans: 1/2 â”‚                                   â”‚
â”‚  â”‚    Fines: Rp0         â”‚                                   â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜                                       â”‚
â”‚                                                             â”‚
â”‚  2ï¸âƒ£ SCAN BOOKS (Max: 2)                                      â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”   â”‚
â”‚  â”‚ [Scan/Input Book Code...]                      ðŸ”    â”‚   â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜   â”‚
â”‚                                                             â”‚
â”‚  Selected Books:                                            â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”   â”‚
â”‚  â”‚ ðŸ“– Harry Potter - J.K. Rowling (Copy: HP-001)       â”‚   â”‚
â”‚  â”‚ ðŸ“– Lord of the Rings - Tolkien (Copy: LOT-002)      â”‚   â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜   â”‚
â”‚                                                             â”‚
â”‚  Due Date: 2026-05-29 (7 days from today)                  â”‚
â”‚                                                             â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”   â”‚
â”‚  â”‚              [ Cancel ]    [ Confirm Loan ]         â”‚   â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜   â”‚
â”‚                                                             â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

### 8.4 Responsive Breakpoints

- Mobile: < 640px (single column, hamburger menu)
- Tablet: 640px - 1024px (collapsed sidebar)
- Desktop: > 1024px (full sidebar)

---

## 9. Non-Functional Requirements

### 9.1 Performance

| Metric | Target |
|--------|--------|
| Time to First Byte (TTFB) | < 500ms |
| Largest Contentful Paint (LCP) | < 2.5s |
| Time to Interactive (TTI) | < 3.5s |
| Dashboard load | < 2s |
| Search response (10k books) | < 1s |
| Transaction commit | < 1s |
| Export 1000 rows Excel | < 10s |

### 9.2 Scalability

- Support 1.000 anggota
- Support 10.000 buku
- Support 15.000 eksemplar
- Support 100 transaksi/hari
- Database query optimized untuk pagination

### 9.3 Security

- Password: min 8 char, hash bcrypt
- CSRF protection on all forms
- SQL injection prevention (Eloquent ORM)
- XSS prevention (Blade escaping)
- Rate limiting: 60 req/min untuk API, 5 login attempts/5 min
- Session timeout: 2 jam
- Audit log untuk action sensitive
- File upload: validate type (jpg, png, pdf), max 2MB

### 9.4 Reliability

- Database transaction untuk multi-step operations
- Soft delete untuk data penting
- Backup strategy (Neon automated backup)
- Graceful error handling
- User-friendly error messages

### 9.5 Compatibility

- Browser: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- Responsive: Mobile, tablet, desktop
- Screen reader accessible (WCAG 2.1 AA target)

---

## 10. Deployment Architecture

### 10.1 Current Architecture (MVP)

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                        USERS                                â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
                             â”‚
                             â–¼
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚  Vercel (PHP Community Runtime)                              â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚   Static    â”‚  â”‚   Laravel   â”‚  â”‚    API Routes       â”‚  â”‚
â”‚  â”‚   Assets    â”‚  â”‚   App       â”‚  â”‚                     â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
                             â”‚
         â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
         â”‚                   â”‚                   â”‚
         â–¼                   â–¼                   â–¼
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”    â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”   â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚    Neon     â”‚    â”‚  Cloudflare R2  â”‚   â”‚   External     â”‚
â”‚  Postgres   â”‚    â”‚  (File Storage) â”‚   â”‚   Services     â”‚
â”‚  (Database) â”‚    â”‚  - Book covers  â”‚   â”‚   - Email      â”‚
â”‚             â”‚    â”‚  - Imports      â”‚   â”‚   - SMS/WA     â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜    â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜   â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

### 10.2 Recommended Production Architecture

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                        USERS                                â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
                             â”‚
                             â–¼
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                      Cloudflare CDN/WAF                      â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
                             â”‚
                             â–¼
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚              VPS / Laravel Cloud / Railway                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”‚
â”‚  â”‚   Nginx     â”‚  â”‚   Laravel   â”‚  â”‚      Queue          â”‚ â”‚
â”‚  â”‚   (Proxy)   â”‚  â”‚   App       â”‚  â”‚    (Redis/Horizon)  â”‚ â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â”‚
â”‚                                                             â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”‚
â”‚  â”‚  Scheduler  â”‚  â”‚   Redis     â”‚  â”‚      Cron           â”‚ â”‚
â”‚  â”‚  (Laravel) â”‚  â”‚  (Cache)    â”‚  â”‚                     â”‚ â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
                             â”‚
         â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
         â”‚                   â”‚                   â”‚
         â–¼                   â–¼                   â–¼
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”    â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”   â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚    Neon     â”‚    â”‚  Cloudflare R2  â”‚   â”‚     Email      â”‚
â”‚  Postgres   â”‚    â”‚  (File Storage) â”‚   â”‚   (SES/Mail)   â”‚
â”‚             â”‚    â”‚                â”‚   â”‚                â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜    â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜   â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

### 10.3 Environment Variables

```env
# Application
APP_NAME="Sipustadig"
APP_ENV=local|production
APP_KEY=
APP_DEBUG=true|false
APP_URL=

# Database
DB_CONNECTION=pgsql
DB_HOST=
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
DB_SSLMODE=require

# Session & Cache (use database/redis for serverless)
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# File Storage
FILESYSTEM_DISK=local|s3|r2
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=auto
AWS_BUCKET=
AWS_ENDPOINT=

# Optional
MAIL_MAILER=log|smtp
```

---

## 11. Development Roadmap

### Phase 0: Foundation (Week 1-2)

**Setup & Auth**
- [ ] Laravel 13 project setup
- [ ] PostgreSQL configuration
- [ ] Laravel Breeze installation
- [ ] Tailwind CSS setup
- [ ] Layout component (sidebar, header)
- [ ] Role enum and middleware
- [ ] User seeder (admin account)
- [ ] Git repository setup

**Deliverable:** Clean project dengan auth dan role system

### Phase 1: Master Data (Week 3-4)

**Members Module**
- [ ] Migration, Model, Factory
- [ ] CRUD controller
- [ ] Request validation
- [ ] Blade views (list, create, edit, show)
- [ ] Search and filter
- [ ] Import/Export Excel
- [ ] Test coverage

**Categories Module**
- [ ] Migration, Model
- [ ] CRUD
- [ ] Seed default categories
- [ ] Test coverage

**Books Module**
- [ ] Migration, Model, Factory
- [ ] CRUD controller
- [ ] Cover upload
- [ ] Search and filter
- [ ] Import/Export Excel
- [ ] Test coverage

**Book Copies Module**
- [ ] Migration, Model
- [ ] CRUD
- [ ] Auto-generate code
- [ ] QR/Barcode generation
- [ ] Label printing
- [ ] Test coverage

**Deliverable:** Full master data management

### Phase 2: Core Transactions (Week 5-6)

**Loan System**
- [ ] Loan transaction CRUD
- [ ] Loan item management
- [ ] Member validation
- [ ] Book copy availability check
- [ ] Auto-generate loan number
- [ ] Auto-calculate due date
- [ ] Database transaction
- [ ] Test coverage

**Return System**
- [ ] Return form
- [ ] Late days calculation
- [ ] Auto-calculate fine
- [ ] Condition input
- [ ] Update book copy status
- [ ] Test coverage

**Extension System**
- [ ] Extend action
- [ ] Validation (not overdue, within limit)
- [ ] Update due date
- [ ] Extension count tracking
- [ ] Test coverage

**Fine System**
- [ ] Fine model and CRUD
- [ ] Auto-create from return
- [ ] Manual fine
- [ ] Payment recording
- [ ] Waive with reason
- [ ] Test coverage

**Deliverable:** Complete loan-return cycle

### Phase 3: Catalog & Dashboard (Week 7)

**OPAC**
- [ ] Public catalog page
- [ ] Search functionality
- [ ] Filter by category
- [ ] Book detail page
- [ ] Availability display

**Dashboard**
- [ ] Librarian dashboard
  - [ ] Stats cards
  - [ ] Overdue list
  - [ ] Recent transactions
  - [ ] Quick actions
- [ ] Principal dashboard
  - [ ] Summary metrics
  - [ ] Charts
  - [ ] Top books/members

**Deliverable:** User-facing features complete

### Phase 4: Reports & Settings (Week 8)

**Reports**
- [ ] Book report
- [ ] Loan report
- [ ] Return report
- [ ] Overdue report
- [ ] Fine report
- [ ] Popular books report
- [ ] Active members report
- [ ] Excel export
- [ ] CSV export

**Settings**
- [ ] Settings CRUD
- [ ] Library rules configuration
- [ ] Setting cache

**Audit Log**
- [ ] Audit log model
- [ ] Logging service
- [ ] Audit log viewer
- [ ] Filter by user/date/action

**Deliverable:** Administrative features complete

### Phase 5: Polish & Launch (Week 9-10)

**Quality Assurance**
- [ ] Bug fixing
- [ ] Performance optimization
- [ ] Security audit
- [ ] Cross-browser testing
- [ ] Mobile responsiveness check

**Documentation**
- [ ] User manual
- [ ] Admin guide
- [ ] API documentation
- [ ] Deployment guide

**Launch**
- [ ] Production deployment
- [ ] DNS configuration
- [ ] SSL verification
- [ ] Monitoring setup
- [ ] Backup verification

**Deliverable:** MVP ready for production use

---

## 12. Definition of Done

### Code Level
- [ ] Code follows PSR-12 standards
- [ ] All business logic has tests
- [ ] No TODO comments in production code
- [ ] No hardcoded values
- [ ] Proper error handling
- [ ] Comments for complex logic

### Feature Level
- [ ] Feature is functional per acceptance criteria
- [ ] Edge cases handled
- [ ] Error states designed
- [ ] Loading states implemented
- [ ] Responsive on target devices

### Product Level
- [ ] All must-have features implemented
- [ ] No critical bugs
- [ ] Performance targets met
- [ ] Security audit passed
- [ ] Documentation complete
- [ ] Stakeholder acceptance

---

## 13. Risks & Mitigation

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Vercel serverless limitations | High | High | Design for stateless, migrate to VPS if needed |
| Neon cold start latency | Medium | Medium | Connection pooling, query optimization |
| Large import timeout | Medium | Medium | Chunked processing, queue job |
| Data loss from corruption | High | Low | Soft delete, regular backups, transactions |
| User adoption resistance | High | Medium | Training sessions, simple UI, help docs |
| Scope creep | Medium | Medium | Clear MVP scope, defer to phase 2 |

---

## 14. Success Metrics

### Technical Metrics
- Page load < 3s on 3G
- API response < 500ms p95
- Zero critical bugs at launch
- 99% automated test coverage on core features

### Business Metrics
- Time to complete loan transaction < 60s (from ~5min manual)
- 100% of manual records digitized
- Monthly report generation < 30s
- User satisfaction > 80%

### Adoption Metrics
- Daily active pustakawan users > 80%
- Student catalog access > 50% of students
- Loan transaction digital > 95%

---

## 15. Appendices

### A. Glossary

| Term | Definition |
|------|-----------|
| Eksemplar | Individual copy of a book |
| OPAC | Online Public Access Catalog |
| SRP | School Resource Planner (alternative term) |
| ISBN | International Standard Book Number |
| NIS/NIP | Nomor Induk Siswa / Nomor Induk Pegawai |

### B. References

- Laravel 13 Documentation: https://laravel.com/docs/13.x
- Tailwind CSS: https://tailwindcss.com
- Vercel PHP Runtime: https://github.com/vercel-community/php
- Neon Postgres: https://neon.tech

### C. Change Log

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2026-05-22 | Initial PRD |
| 2.0 | 2026-05-22 | Enhanced with best practices, detailed specs, wireframes |
