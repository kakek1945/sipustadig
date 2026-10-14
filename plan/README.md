# Sirkulib - Sistem Informasi Perpustakaan Sekolah

**Versi:** 1.0 MVP
**Status:** Ready for Development
**Dokumentasi:** https://github.com/your-repo/sirkulib

---

## 🎯 Overview

Sirkulib adalah sistem informasi perpustakaan sekolah berbasis web yang dirancang untuk menggantikan pencatatan manual menggunakan buku besar atau spreadsheet.

**Tujuan:**
- Mempermudah pustakawan mengelola koleksi buku dan transaksi
- Mempercepat proses peminjaman dan pengembalian
- Menyediakan katalog online untuk siswa dan guru
- Automasi laporan dan denda

---

## 📁 Dokumen Perencanaan

| File | Deskripsi |
|------|-----------|
| [PRD.md](PRD.md) | Product Requirements Document - Visi, fitur, scope MVP |
| [DESIGN.md](DESIGN.md) | Design Document - Arsitektur, database, UI guidelines |
| [TASKS.md](TASKS.md) | Task Breakdown - Detail task per fase, estimasi waktu |
| [BACKLOG.md](BACKLOG.md) | Product Backlog - Prioritas fitur dan task |
| [TESTING.md](TESTING.md) | Testing Plan - Strategi testing, test cases |
| [DEPLOYMENT.md](DEPLOYMENT.md) | Deployment Guide - Cara deploy ke production |

---

## 🛠️ Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 13.x |
| Bahasa | PHP 8.3+ |
| Frontend | Blade + Livewire + Tailwind CSS |
| Database | PostgreSQL (Neon) |
| Storage | Cloudflare R2 / S3 |
| Auth | Laravel Breeze |
| Deployment | Vercel (MVP) → Laravel Cloud/Railway (Production) |

---

## 👥 Target Pengguna

| Role | Akses |
|------|-------|
| **Admin** | Sistem, user, backup, settings |
| **Pustakawan** | Anggota, buku, eksemplar, transaksi, laporan |
| **Kepala Sekolah** | Dashboard, laporan ringkas |
| **Guru** | Katalog, peminjaman, riwayat |
| **Siswa** | Katalog, riwayat pinjam |

---

## 📋 Fitur MVP

### Data Master
- [ ] CRUD Anggota (Siswa, Guru, Staf)
- [ ] CRUD Kategori Buku
- [ ] CRUD Buku (dengan cover upload)
- [ ] CRUD Eksemplar (dengan QR/Barcode)

### Transaksi
- [ ] Peminjaman buku (scan flow)
- [ ] Pengembalian buku (dengan denda auto)
- [ ] Perpanjangan masa pinjam
- [ ] Manajemen denda

### Lainnya
- [ ] Katalog Online (OPAC)
- [ ] Dashboard pustakawan & kepala sekolah
- [ ] Laporan (Excel, CSV)
- [ ] Import/Export Excel
- [ ] Pengaturan aturan pinjam
- [ ] Audit log

---

## 📅 Timeline Development

| Fase | Durasi | Focus |
|------|--------|-------|
| Phase 0 | 2 minggu | Setup & Auth |
| Phase 1 | 2 minggu | Master Data |
| Phase 2 | 2 minggu | Transaksi Core |
| Phase 3 | 1 minggu | Catalog & Dashboard |
| Phase 4 | 1 minggu | Reports & Settings |
| Phase 5 | 2 minggu | Polish & Launch |
| **Total** | **10 minggu** | **MVP Complete** |

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.3+
- PostgreSQL 14+
- Composer 2.x
- Node.js 20+

### Installation

```bash
# Clone repository
git clone https://github.com/your-repo/sirkulib.git
cd sirkulib

# Install dependencies
composer install
npm install

# Copy & configure environment
cp .env.example .env
php artisan key:generate

# Setup database
createdb sirkulib
php artisan migrate:fresh --seed

# Run development server
php artisan serve

# In another terminal, for frontend
npm run dev
```

### Access

| URL | Keterangan |
|-----|------------|
| http://localhost:8000 | Aplikasi |
| http://localhost:8000/login | Login |

**Default Credentials:**
- Admin: admin@sirkulib.test / password
- Pustakawan: librarian@sirkulib.test / password

---

## 📂 Project Structure

```
sirkulib/
├── app/
│   ├── Actions/           # Laravel Actions
│   ├── Enums/            # Enum definitions
│   ├── Http/
│   │   ├── Controllers/   # Controllers
│   │   ├── Middleware/    # Middleware
│   │   └── Requests/     # Form Requests
│   ├── Models/           # Eloquent Models
│   ├── Services/          # Business logic
│   └── Policies/          # Authorization
├── database/
│   ├── migrations/       # Database migrations
│   ├── seeders/          # Seed data
│   └── factories/        # Model factories
├── resources/
│   └── views/            # Blade templates
├── routes/
│   ├── web.php          # Web routes
│   └── api.php          # API routes
├── tests/
│   ├── Feature/          # Feature tests
│   └── Unit/            # Unit tests
└── docs/                # Documentation
```

---

## 🔐 Security

- Password hashed dengan bcrypt
- CSRF protection aktif
- Role-based access control
- Rate limiting pada login
- Audit log untuk aksi sensitive
- Input validation ketat

---

## 📊 Database Schema

```
users
  └── members
        └── loan_transactions
              └── loan_items → fines
                    └── fine_payments

book_categories
  └── books
        └── book_copies

library_settings
audit_logs
imports
```

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test --filter=LoanServiceTest
```

---

## 🚢 Deployment

**MVP (Development):** Vercel

**Production Recommended:**
- Laravel Cloud
- Railway
- VPS with Nginx + PHP-FPM

See [DEPLOYMENT.md](DEPLOYMENT.md) untuk panduan lengkap.

---

## 📈 Roadmap

### Phase 1 (MVP) ✅ Planning
- [x] PRD
- [x] Design
- [x] Task breakdown
- [ ] Development

### Phase 2 (Post-MVP)
- [ ] Reservasi buku
- [ ] Notifikasi WhatsApp
- [ ] Scan barcode via kamera
- [ ] Dashboard literasi

### Phase 3 (Future)
- [ ] Mobile app
- [ ] Multi-school/SaaS
- [ ] Digital library
- [ ] AI recommendations

---

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

---

## 📝 License

This project is proprietary software. All rights reserved.

---

## 📞 Support

- **Documentation:** `/docs` folder
- **Issues:** GitHub Issues
- **Email:** (your-email@domain.com)

---

## 📖 Referensi

- [Laravel Documentation](https://laravel.com/docs/13.x)
- [Tailwind CSS](https://tailwindcss.com)
- [Livewire](https://livewire.laravel.com)
- [Neon PostgreSQL](https://neon.tech)
- [Vercel PHP](https://github.com/vercel-community/php)