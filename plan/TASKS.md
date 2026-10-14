# Task Breakdown - Sipustadig MVP

**Project Duration:** 10 weeks (2.5 months)
**Team Assumption:** 1-2 developers (Backend + Frontend)

---

## 📊 Task Type Legend

| Type | Warna | Deskripsi |
|------|-------|-----------|
| **Frontend** | 🟦 | UI/UX, Views, Components, Styling, JS |
| **Backend** | 🟩 | API, Services, Models, Database, Logic |
| **Setup** | 🟪 | Initial configuration, tools |
| **DevOps** | 🟧 | Deployment, infrastructure |
| **Testing** | 🟥 | Unit tests, feature tests, QA |
| **Docs** | ⬜ | Documentation, manuals |
| **Bugfix** | 🔴 | Bug fixing |
| **Perf** | 🟨 | Performance optimization |
| **Security** | 🟫 | Security hardening |
| **Review** | ⬛ | Code review, refactor |

---

## 📈 Hours Summary by Type

| Type | Hours | Percentage |
|------|-------|------------|
| **Frontend** | ~120 | 38% |
| **Backend** | ~110 | 35% |
| **Testing** | ~35 | 11% |
| **DevOps** | ~20 | 6% |
| **Setup** | ~15 | 5% |
| **Docs** | ~11 | 3% |
| **Bugfix** | ~8 | 2% |
| **Total** | **~318** | 100% |

---

## 🎨 Frontend Tasks Summary

| # | Task | Week | Hours | Dependencies | Status |
|---|------|------|-------|--------------|--------|
| F01 | Modifikasi login/register view | 1 | 2 | 0.5 | [x] |
| F02 | Setup layout dashboard (sidebar, header, content) | 1 | 4 | 0.6 | [x] |
| F03 | Buat navigation sidebar per role | 1 | 3 | F02 | [x] |
| F04 | Buat error pages (404, 500) | 2 | 1 | F02 | [x] |
| F05 | Buat Member index view + datatable | 3 | 4 | Phase 0 | [x] |
| F06 | Buat Member create/edit view | 3 | 3 | F05 | [x] |
| F07 | Buat Member show view (with history) | 3 | 2 | F05 | [x] |
| F08 | Implementasi search & filter UI | 3 | 2 | F05 | [x] |
| F09 | Buat Category CRUD views | 3 | 2 | Phase 0 | [x] |
| F10 | Buat Book index view + datatable | 4 | 4 | Phase 0 | [x] |
| F11 | Buat Book create/edit view + cover upload | 4 | 4 | F10 | [x] |
| F12 | Buat Book show view | 4 | 2 | F10 | [x] |
| F13 | Buat BookCopy views (list, create, edit) | 4 | 4 | Phase 0 | [x] |
| F14 | Buat generate multiple eksemplar view | 4 | 2 | F13 | [x] |
| F15 | Buat label cetak QR | 4 | 2 | F13 | [x] |
| F16 | Buat loan form view (scan flow) | 5 | 5 | Phase 1 | [x] |
| F17 | Buat loan list view | 5 | 3 | Phase 1 | [x] |
| F18 | Buat loan detail view | 5 | 2 | F17 | [x] |
| F19 | Buat return form view | 6 | 4 | Phase 1 | [x] |
| F20 | Buat extend view | 6 | 2 | Phase 1 | [x] |
| F21 | Buat fine list view | 6 | 3 | Phase 1 | [x] |
| F22 | Buat fine detail + payment view | 6 | 2 | F21 | [x] |
| F23 | Buat catalog index view | 7 | 4 | Phase 1 | [x] |
| F24 | Buat catalog detail view (ketersediaan) | 7 | 3 | F23 | [x] |
| F25 | Buat librarian dashboard view | 7 | 5 | Phase 0 | [x] |
| F26 | Buat principal dashboard view | 7 | 4 | Phase 0 | [x] |
| F27 | Implementasi chart (Chart.js) | 7 | 4 | F25-F26 | [x] |
| F28 | Buat report views (all types) | 8 | 6 | Phase 1 | [x] |
| F29 | Buat settings view | 8 | 3 | Phase 0 | [x] |
| F30 | Buat audit log viewer | 8 | 2 | Phase 0 | [x] |
| F31 | Buat import views (members, books, copies) | 9 | 4 | Phase 1 | [x] |
| F32 | Responsive check all pages | 9 | 3 | All | [x] |
| F33 | UI polish & animations | 9 | 3 | All | [x] |
| F34 | Loading states, empty states, error states | 9 | 2 | All | [x] |
| F35 | Accessibility audit (WCAG 2.1 AA) | 10 | 2 | All | [x] |

**Total Frontend Hours: ~93 hours** (29% of total)

---

## Phase 0: Project Setup & Foundation (Week 1-2)

### Week 1: Environment & Auth

| # | Task | Type | Hours | Dependencies | Status |
|---|------|------|-------|--------------|--------|
| 0.1 | Buat repository GitHub | DevOps | 1 | - | [ ] |
| 0.2 | Install Laravel 13 fresh | Setup | 2 | - | [ ] |
| 0.3 | Setup PostgreSQL lokal | Setup | 1 | - | [ ] |
| 0.4 | Konfigurasi .env & .env.example | Setup | 0.5 | - | [ ] |
| 0.5 | Install Laravel Breeze + konfigurasi auth | Setup | 3 | 0.2 | [x] |
| 0.6 | Setup Tailwind CSS + konfigurasi | Setup | 2 | 0.2 | [x] |
| **F01** | **Modifikasi login/register view (styling, branding)** | **Frontend** | **2** | 0.5 | [x] |
| 0.7 | Buat User model & migration | Backend | 1 | 0.5 | [x] |
| 0.8 | Buat Role enum (admin, librarian, principal, teacher, student) | Backend | 0.5 | 0.7 | [x] |
| 0.9 | Buat RoleMiddleware | Backend | 1 | 0.8 | [x] |
| **F02** | **Setup layout dashboard (sidebar, header, content area)** | **Frontend** | **4** | 0.6 | [x] |
| **F03** | **Buat navigation sidebar per role** | **Frontend** | **3** | F02 | [x] |
| 0.10 | Buat UserSeeder (admin account) | Backend | 0.5 | 0.8 | [x] |
| 0.11 | Setup Vercel project + PHP runtime | DevOps | 3 | 0.1 | [ ] |
| 0.12 | Test deploy ke Vercel | DevOps | 2 | 0.11 | [ ] |
| 0.13 | Dokumentasi setup development | Docs | 1 | - | [ ] |

**Week 1 Subtotal: ~27 hours** (Frontend: ~9, Backend: ~3, Setup: ~8, DevOps: ~6, Docs: ~1)
**[Week 1 COMPLETED]**

### Week 2: Database & Core Models

| # | Task | Type | Hours | Dependencies | Status |
|---|------|------|-------|--------------|--------|
| 0.14 | Buat database migrations (all tables) | Backend | 4 | 0.7 | [x] |
| 0.15 | Buat semua Enums | Backend | 2 | 0.8 | [x] |
| 0.16 | Buat semua Models + relationships | Backend | 4 | 0.14 | [x] |
| 0.17 | Buat semua Factories | Backend | 2 | 0.16 | [x] |
| 0.18 | Buat semua Seeders (category, settings) | Backend | 2 | 0.16 | [x] |
| 0.19 | Run migration + seed | Backend | 0.5 | 0.14-0.18 | [ ] |
| 0.20 | Setup Laravel Policies | Backend | 2 | 0.16 | [x] |
| 0.21 | Setup Service layer structure | Backend | 1 | 0.16 | [x] |
| 0.22 | Buat custom config library.php | Backend | 1 | 0.16 | [x] |
| **F04** | **Buat error pages (404, 500, maintenance)** | **Frontend** | **1** | F02 | [x] |
| 0.23 | Setup Vercel environment variables | DevOps | 1 | 0.11 | [ ] |
| 0.24 | Test koneksi database Neon | DevOps | 1 | 0.23 | [ ] |
| 0.25 | Code review & refactor | Review | 3 | 0.14-0.22, F01-F04 | [ ] |

**Week 2 Subtotal: ~25 hours** (Frontend: ~1, Backend: ~17, DevOps: ~2, Review: ~3)
**[Week 2 COMPLETED - Partial: migrations, models, enums, policies, services, seeders, factories done. Pending: migration run, Vercel setup, code review]**

---

## Phase 1: Master Data (Week 3-4)

### Week 3: Members & Categories

| # | Task | Type | Hours | Dependencies | Status |
|---|------|------|-------|--------------|--------|
| 1.1 | Buat MemberService | Backend | 2 | Phase 0 | [x] |
| 1.2 | Buat MemberController (CRUD) | Backend | 3 | 1.1 | [x] |
| 1.3 | Buat MemberRequest (validation) | Backend | 1 | 1.1 | [x] |
| 1.4 | Buat MemberPolicy | Backend | 1 | Phase 0 | [x] |
| 1.5 | Buat routes members | Backend | 0.5 | 1.2 | [x] |
| **F05** | **Buat Member index view + datatable + pagination** | **Frontend** | **4** | Phase 0 | [x] |
| **F06** | **Buat Member create/edit view + form validation UI** | **Frontend** | **3** | F05 | [x] |
| **F07** | **Buat Member show view (profile + loan history)** | **Frontend** | **2** | F05 | [x] |
| **F08** | **Implementasi search & filter UI (type, class, status)** | **Frontend** | **2** | F05 | [x] |
| 1.6 | Buat CategoryService + Controller | Backend | 2 | Phase 0 | [x] |
| 1.7 | Buat routes categories | Backend | 0.5 | 1.6 | [x] |
| **F09** | **Buat Category CRUD views (modal + list)** | **Frontend** | **2** | Phase 0 | [x] |
| 1.8 | Unit test MemberService | Testing | 3 | 1.1 | [ ] |
| 1.9 | Feature test Member CRUD | Testing | 2 | 1.2 | [ ] |
| 1.10 | Feature test Category CRUD | Testing | 1 | 1.6 | [ ] |

**Week 3 Subtotal: ~28 hours** (Frontend: ~11, Backend: ~10, Testing: ~6)

### Week 4: Books & Book Copies

| # | Task | Type | Hours | Dependencies | Status |
|---|------|------|-------|--------------|--------|
| 1.11 | Buat BookService | Backend | 2 | Phase 0 | [x] |
| 1.12 | Buat BookController (CRUD) | Backend | 4 | 1.11 | [x] |
| 1.13 | Buat BookRequest (validation) | Backend | 1 | 1.11 | [x] |
| 1.14 | Implementasi upload cover (R2/S3) | Backend | 3 | 1.12 | [x] |
| 1.15 | Buat routes books | Backend | 0.5 | 1.12 | [x] |
| **F10** | **Buat Book index view + datatable + cover thumbnail** | **Frontend** | **4** | Phase 0 | [x] |
| **F11** | **Buat Book create/edit view + drag-drop cover upload** | **Frontend** | **4** | F10 | [x] |
| **F12** | **Buat Book show view (detail + list eksemplar)** | **Frontend** | **2** | F10 | [x] |
| 1.16 | Buat BookCopyService | Backend | 2 | Phase 0 | [x] |
| 1.17 | Buat BookCopyController (CRUD) | Backend | 3 | 1.16 | [x] |
| 1.18 | Implementasi generate kode eksemplar auto | Backend | 2 | 1.16 | [x] |
| 1.19 | Implementasi generate QR/barcode | Backend | 3 | 1.16 | [x] |
| 1.20 | Buat routes book-copies | Backend | 0.5 | 1.17 | [x] |
| **F13** | **Buat BookCopy views (list, create, edit, detail)** | **Frontend** | **4** | Phase 0 | [x] |
| **F14** | **Buat generate multiple eksemplar view (bulk create)** | **Frontend** | **2** | F13 | [x] |
| **F15** | **Buat label cetak QR (printable template)** | **Frontend** | **2** | F13 | [x] |
| 1.21 | Unit test BookService | Testing | 3 | 1.11 | [x] |
| 1.22 | Feature test Book CRUD | Testing | 2 | 1.12 | [x] |
| 1.23 | Feature test BookCopy CRUD | Testing | 2 | 1.17 | [x] |

**Week 4 Subtotal: ~35 hours** (Frontend: ~12, Backend: ~17, Testing: ~7)

---

## Phase 2: Core Transactions (Week 5-6)

### Week 5: Loan System

| # | Task | Type | Hours | Dependencies | Status |
|---|------|------|-------|--------------|--------|
| 2.1 | Buat LoanService | Backend | 4 | Phase 1 | [x] |
| 2.2 | Implementasi validasi peminjaman | Backend | 4 | 2.1 | [x] |
| 2.3 | Buat CreateLoan action | Backend | 3 | 2.1 | [x] |
| 2.4 | Buat LoanController | Backend | 3 | 2.1 | [x] |
| 2.5 | Buat LoanRequest (validation) | Backend | 1 | 2.1 | [x] |
| 2.6 | Implementasi auto-generate nomor pinjam | Backend | 1 | 2.3 | [x] |
| 2.7 | Implementasi auto-hitung due date | Backend | 1 | 2.3 | [x] |
| 2.8 | Buat routes loans | Backend | 0.5 | 2.4 | [x] |
| **F16** | **Buat loan form view (step-by-step scan flow)** | **Frontend** | **5** | Phase 1 | [x] |
| **F17** | **Buat loan list view (active + history tabs)** | **Frontend** | **3** | Phase 1 | [x] |
| **F18** | **Buat loan detail view (items + status timeline)** | **Frontend** | **2** | F17 | [x] |
| 2.9 | Unit test LoanService (validasi) | Testing | 4 | 2.1 | [x] |
| 2.10 | Feature test loan process | Testing | 3 | 2.4 | [x] |

**Week 5 Subtotal: ~32 hours** (Frontend: ~10, Backend: ~17, Testing: ~7)

### Week 6: Return & Fine System

| # | Task | Type | Hours | Dependencies | Status |
|---|------|------|-------|--------------|--------|
| 2.11 | Buat ReturnService | Backend | 4 | 2.1 | [x] |
| 2.12 | Implementasi hitung keterlambatan | Backend | 3 | 2.11 | [x] |
| 2.13 | Implementasi hitung denda auto | Backend | 2 | 2.11 | [x] |
| 2.14 | Buat ExtendLoan action | Backend | 3 | 2.1 | [x] |
| 2.15 | Buat ReturnController | Backend | 2 | 2.11 | [x] |
| 2.16 | Buat routes returns | Backend | 0.5 | 2.15 | [x] |
| **F19** | **Buat return form view (scan + late days calculation UI)** | **Frontend** | **4** | Phase 1 | [x] |
| **F20** | **Buat extend view (extend confirmation modal)** | **Frontend** | **2** | Phase 1 | [x] |
| 2.17 | Buat FineService | Backend | 3 | Phase 1 | [x] |
| 2.18 | Buat FineController | Backend | 2 | 2.17 | [x] |
| 2.19 | Implementasi bayar denda | Backend | 2 | 2.17 | [x] |
| 2.20 | Implementasi bebaskan denda (wajib alasan) | Backend | 2 | 2.17 | [x] |
| 2.21 | Buat routes fines | Backend | 0.5 | 2.18 | [x] |
| **F21** | **Buat fine list view (filterable by status)** | **Frontend** | **3** | Phase 1 | [x] |
| **F22** | **Buat fine detail + payment modal view** | **Frontend** | **2** | F21 | [x] |
| 2.22 | Unit test ReturnService | Testing | 4 | 2.11 | [x] |
| 2.23 | Feature test return process | Testing | 3 | 2.15 | [x] |
| 2.24 | Feature test fine operations | Testing | 2 | 2.18 | [x] |

**Week 6 Subtotal: ~35 hours** (Frontend: ~9, Backend: ~19, Testing: ~9)

---

## Phase 3: Catalog & Dashboard (Week 7)

### Week 7: OPAC & Dashboard

| # | Task | Type | Hours | Dependencies | Status |
|---|------|------|-------|--------------|--------|
| 3.1 | Buat CatalogController | Backend | 2 | Phase 1 | [x] |
| 3.2 | Implementasi search & filter catalog | Backend | 3 | 3.1 | [x] |
| 3.3 | Buat DashboardService | Backend | 4 | Phase 2 | [x] |
| 3.4 | Implementasi dashboard queries (stats, charts) | Backend | 4 | 3.3 | [x] |
| **F23** | **Buat catalog index view (public access, search)** | **Frontend** | **4** | Phase 1 | [x] |
| **F24** | **Buat catalog detail view (availability + location)** | **Frontend** | **3** | F23 | [x] |
| **F25** | **Buat librarian dashboard view (stats cards, widgets)** | **Frontend** | **5** | Phase 0 | [x] |
| **F26** | **Buat principal dashboard view (summary + trends)** | **Frontend** | **4** | Phase 0 | [x] |
| **F27** | **Implementasi chart (Chart.js - loan trends, popular books)** | **Frontend** | **4** | F25-F26 | [x] |
| 3.5 | Feature test catalog access | Testing | 2 | 3.1 | [x] |
| 3.6 | Feature test dashboard | Testing | 2 | 3.3 | [x] |

**Week 7 Subtotal: ~33 hours** (Frontend: ~16, Backend: ~13, Testing: ~4)

---

## Phase 4: Reports & Settings (Week 8)

### Week 8: Reports & Admin Features

| # | Task | Type | Hours | Dependencies | Status |
|---|------|------|-------|--------------|--------|
| 4.1 | Buat ReportService | Backend | 5 | Phase 3 | [x] |
| 4.2 | Implementasi semua laporan (11 types) | Backend | 8 | 4.1 | [x] |
| 4.3 | Buat ExportService (Excel/CSV) | Backend | 4 | 4.1 | [x] |
| 4.4 | Buat routes reports | Backend | 0.5 | 4.1 | [x] |
| **F28** | **Buat report views (filterable, printable)** | **Frontend** | **6** | Phase 1 | [x] |
| 4.5 | Buat SettingService | Backend | 2 | Phase 0 | [x] |
| 4.6 | Buat SettingController | Backend | 2 | 4.5 | [x] |
| **F29** | **Buat settings view (rules configuration UI)** | **Frontend** | **3** | Phase 0 | [x] |
| 4.7 | Buat AuditService | Backend | 3 | Phase 0 | [x] |
| 4.8 | Buat AuditLogController | Backend | 2 | 4.7 | [x] |
| **F30** | **Buat audit log viewer (filterable table)** | **Frontend** | **2** | Phase 0 | [x] |
| 4.9 | Unit test ReportService | Testing | 4 | 4.1 | [x] |
| 4.10 | Feature test reports | Testing | 3 | 4.2 | [x] |

**Week 8 Subtotal: ~42 hours** (Frontend: ~11, Backend: ~27, Testing: ~7)

---

## Phase 5: Polish & Launch (Week 9-10)

### Week 9: Import & Polish

| # | Task | Type | Hours | Dependencies | Status |
|---|------|------|-------|--------------|--------|
| 5.1 | Buat ImportService | Backend | 4 | Phase 1 | [x] |
| 5.2 | Implementasi import members Excel | Backend | 3 | 5.1 | [x] |
| 5.3 | Implementasi import books Excel | Backend | 3 | 5.1 | [x] |
| 5.4 | Implementasi import eksemplar Excel | Backend | 3 | 5.1 | [x] |
| 5.5 | Buat routes imports | Backend | 0.5 | 5.1 | [x] |
| **F31** | **Buat import views (upload + preview + error report)** | **Frontend** | **4** | Phase 1 | [x] |
| **F32** | **Responsive check all pages (mobile, tablet, desktop)** | **Frontend** | **3** | All | [x] |
| **F33** | **UI polish (animations, transitions, micro-interactions)** | **Frontend** | **3** | All | [x] |
| **F34** | **Loading states, empty states, error states** | **Frontend** | **2** | All | [x] |
| 5.6 | Review & fix bug | Bugfix | 8 | All phases | [x] |
| 5.7 | Performance optimization (queries, assets) | Perf | 4 | All phases | [x] |
| 5.8 | Security audit | Security | 3 | All phases | [x] |
| 5.9 | Cross-browser testing | Testing | 3 | F32 | [x] |

**Week 9 Subtotal: ~35 hours** (Frontend: ~12, Backend: ~14, Bugfix: ~8, Perf: ~4, Security: ~3, Testing: ~3)

### Week 10: Documentation & Launch

| # | Task | Type | Hours | Dependencies | Status |
|---|------|------|-------|--------------|--------|
| **F35** | **Accessibility audit (WCAG 2.1 AA)** | **Frontend** | **2** | All | [x] |
| 5.10 | Buat user manual | Docs | 4 | All phases | [x] |
| 5.11 | Buat admin guide | Docs | 3 | All phases | [x] |
| 5.12 | Update README.md | Docs | 1 | All | [x] |
| 5.13 | Setup production environment | DevOps | 3 | Phase 0 | [ ] |
| 5.14 | Final deployment to Vercel/production | DevOps | 2 | 5.13 | [ ] |
| 5.15 | DNS & SSL verification | DevOps | 1 | 5.14 | [ ] |
| 5.16 | Backup verification | DevOps | 1 | 5.14 | [ ] |
| 5.17 | Final code cleanup | Review | 2 | All | [x] |
| 5.18 | Final test suite run | Testing | 2 | All | [ ] |
| 5.19 | Stakeholder acceptance | Review | 2 | All | [ ] |

**Week 10 Subtotal: ~23 hours** (Frontend: ~2, Docs: ~8, DevOps: ~7, Review: ~4, Testing: ~2)

Verification:

- `npm run build` passed.
- `vendor/bin/pint --dirty` passed.
- `php artisan test` is blocked on local PHP 8.2.28 because Composer dependencies require PHP `>= 8.4.0`.

Post-Week 10 polish updates:

- [x] Branding aplikasi diganti menjadi Sipustadig di konfigurasi, layout utama, auth, welcome, katalog, dashboard, README, dan dokumentasi.
- [x] Logo sekolah dari ImgBB disimpan lokal dan dipakai lewat komponen brand logo.
- [x] Sidebar dipadatkan, bagian profil user dihapus, lebar sidebar dikurangi, dan tombol keluar ditambahkan.
- [x] Modal tambah kategori diperbaiki agar clickable, konsisten secara visual, dan tidak memunculkan overlay/notification yang mengganggu setelah refresh.
- [x] Base Controller diperbaiki agar `authorize()` tersedia di `FineController`.
- [x] Perilaku barcode scanner dikonfirmasi untuk scanner fisik mode keyboard pada alur peminjaman, pengembalian, pencarian eksemplar, cetak label, dan import.

---

### Week 11: QR, Barcode & Automation Modes

Scope:

- QR Code: kartu anggota, label eksemplar buku, peminjaman, pengembalian, inventaris buku fisik, dan mode self-service terbatas.
- Barcode ISBN bawaan buku: input katalog buku, pencarian ISBN, identifikasi judul/edisi buku, dan bantuan autofill katalog.
- Target scan: scanner fisik sebagai mode utama, kamera HP sebagai enhancement dengan fallback manual input.
- Otomasi dipakai bertahap untuk mode `auto-fill`, `auto-process`, dan `auto-review` sesuai alur bisnis masing-masing modul.

Automation modes:

- **Auto-fill**: hasil scan mengisi data anggota, eksemplar, ISBN, atau transaksi lalu petugas mengonfirmasi.
- **Auto-process**: hasil scan langsung mengeksekusi aksi seperti pengembalian jika semua syarat aman terpenuhi.
- **Auto-review**: hasil scan diproses sebagai draft, status pending, atau antrean verifikasi ketika butuh pengecekan petugas.

| # | Task | Type | Hours | Dependencies | Status |
|---|------|------|-------|--------------|--------|
| 6.1 | Buat QRCodeService terpusat untuk member, book copy, loan, return, dan inventory payload | Backend | 3 | Phase 1-2 | [x] |
| 6.2 | Standarisasi payload QR dan barcode lookup (`member_number`, `copy_code`, `barcode`, `isbn`) | Backend | 2 | 6.1 | [x] |
| 6.3 | Buat endpoint/handler scan resolver untuk membedakan QR anggota, QR eksemplar, QR transaksi, dan ISBN | Backend | 4 | 6.2 | [~] |
| 6.4 | Implementasi QR kartu anggota + template cetak kartu anggota | Backend/Frontend | 5 | Members module | [x] |
| 6.5 | Upgrade label eksemplar buku untuk QR, barcode text, print massal, dan reprint | Backend/Frontend | 4 | Book Copies module | [x] |
| 6.6 | Implementasi quick member lookup via scan kartu anggota untuk pencarian/profil/transaksi | Backend/Frontend | 3 | Members module, 6.3 | [x] |
| 6.7 | Implementasi quick copy lookup via scan kode eksemplar/barcode untuk detail, status, dan reprint label | Backend/Frontend | 3 | Book Copies module, 6.3 | [x] |
| 6.8 | Integrasi scan QR/barcode pada form peminjaman sebagai mode `auto-fill` anggota dan eksemplar | Frontend | 3 | Loans module, 6.3 | [ ] |
| 6.9 | Implementasi draft peminjaman semi-otomatis dari scan anggota + multi-scan eksemplar | Backend/Frontend | 5 | Loans module, 6.8 | [ ] |
| 6.10 | Integrasi scan QR/barcode pada form pengembalian sebagai mode `auto-fill` dan `auto-process` | Frontend | 3 | Returns module, 6.3 | [ ] |
| 6.11 | Implementasi pengembalian otomatis dari scan eksemplar dengan fallback `auto-review` jika perlu verifikasi | Backend/Frontend | 6 | Returns module, 6.10 | [ ] |
| 6.12 | Buat mode inventaris fisik: scan rak, cocokkan eksemplar, tandai ditemukan/hilang/perlu cek | Backend/Frontend | 7 | Book Copies module, 6.3 | [ ] |
| 6.13 | Implementasi input katalog buku via barcode ISBN bawaan buku sebagai mode `auto-fill` | Backend/Frontend | 4 | Books module, 6.3 | [ ] |
| 6.14 | Tambahkan pencarian ISBN dan identifikasi judul/edisi buku di katalog/admin | Backend/Frontend | 4 | Books/Catalog module | [ ] |
| 6.15 | Tambahkan kamera HP scanner UI dengan fallback manual input | Frontend | 5 | 6.3 | [ ] |
| 6.16 | Tambahkan pengaturan mode otomasi scan per alur (`manual`, `semi-auto`, `auto`, `review`) dan pembatasan self-service | Backend/Frontend | 5 | Settings module, 6.3 | [ ] |
| 6.17 | Tambahkan audit log / scan activity trail untuk aksi otomatis dan hasil verifikasi | Backend | 3 | Audit module, 6.9-6.16 | [ ] |
| 6.18 | Tambahkan mode kiosk / self-service return terbatas untuk siswa dengan guardrail dan antrean review | Backend/Frontend | 6 | Returns module, 6.11, 6.16 | [ ] |
| 6.19 | Testing QR/barcode & automation flows: member lookup, copy lookup, loan draft, auto return, review queue, inventory, ISBN | Testing | 6 | 6.4-6.18 | [ ] |
| 6.20 | Update user manual/admin guide untuk scanner fisik, kamera HP, mode otomatis, dan self-service terbatas | Docs | 3 | 6.4-6.19 | [ ] |

**Week 11 Subtotal: ~75 hours** (Backend: ~12, Frontend: ~11, Backend/Frontend: ~43, Testing: ~6, Docs: ~3)

Implementation priority:

- **Priority 1 - Scan foundation:** 6.1, 6.2, 6.3, 6.4, 6.5, 6.15. Fokus pada payload QR/barcode, resolver scan, kartu anggota, label eksemplar, dan scanner UI/fallback manual.
- **Priority 2 - Auto-fill operations:** 6.6, 6.7, 6.8, 6.10, 6.13, 6.14. Fokus pada scan untuk mengisi data anggota, eksemplar, peminjaman, pengembalian, dan ISBN tanpa langsung mengeksekusi aksi berisiko.
- **Priority 3 - Semi-auto and review flows:** 6.9, 6.11, 6.12, 6.16, 6.17. Fokus pada draft peminjaman, pengembalian otomatis dengan fallback review, inventaris scan, pengaturan mode, dan audit trail.
- **Priority 4 - Self-service enhancement:** 6.18, 6.19, 6.20. Fokus pada kiosk/self-service return terbatas, testing penuh, dan dokumentasi operasional setelah guardrail stabil.

Recommended MVP cut for Week 11:

- Minimal wajib: 6.1-6.8, 6.10, 6.13, 6.15, 6.19, 6.20.
- Sangat disarankan: 6.11, 6.16, 6.17 agar mode otomatis punya kontrol dan audit.
- Bisa fase lanjutan: 6.18 self-service kiosk jika sekolah belum siap menyediakan perangkat/tempat scan mandiri.

Acceptance scenarios:

- Scan QR kartu anggota mengisi atau mengenali anggota yang benar.
- Scan QR label eksemplar mengenali `copy_code` atau `barcode` yang benar.
- Quick lookup anggota membuka profil atau mengisi draft transaksi yang benar.
- Quick lookup eksemplar menampilkan status, lokasi, atau kebutuhan reprint label yang benar.
- Peminjaman berhasil dengan scanner fisik, kamera HP, dan input manual.
- Draft peminjaman dari hasil scan gagal dengan pesan jelas jika anggota diblokir, kuota habis, atau eksemplar tidak tersedia.
- Pengembalian menemukan transaksi aktif dari hasil scan eksemplar.
- Auto-return langsung berhasil jika item valid dan tidak memerlukan review tambahan.
- Auto-return masuk antrean review jika item tidak ditemukan, tidak punya loan aktif, telat ekstrem, atau perlu pengecekan kondisi buku.
- Inventaris fisik bisa menandai eksemplar ditemukan, tidak ditemukan, dan perlu pengecekan.
- Barcode ISBN bisa dipakai untuk input katalog, pencarian ISBN, dan identifikasi judul/edisi.
- Kamera HP punya fallback manual jika permission ditolak atau scan gagal.
- Audit log mencatat siapa yang scan, mode apa yang dipakai, hasilnya, dan apakah ada verifikasi lanjutan.
- Self-service return tidak membuka aksi admin lain dan tetap bisa dibatasi hanya untuk alur pengembalian.

Assumptions:

- Week 11 mulai diimplementasikan dari fondasi scan: `QRCodeService`, standar payload, endpoint resolver dashboard, kartu anggota QR, label eksemplar QR/barcode, dan halaman Scan Cepat untuk lookup anggota/eksemplar/ISBN.
- `QRCodeService` saat ini mencakup payload member dan book copy, plus resolver raw untuk `member_number`, `copy_code`, `barcode`, dan `isbn`; payload loan/return/inventory masih fase berikutnya.
- Endpoint resolver scan awal dibuat untuk staff/admin-pustakawan dan belum menjalankan aksi otomatis berisiko.
- Scanner fisik tetap prioritas operasional pustakawan; kamera HP ditambahkan sebagai opsi mobile.
- Tahapan implementasi mengikuti urutan: `auto-fill` dulu, lalu `semi-auto`, lalu `auto-review`, dan terakhir `self-service` terbatas.
- Tidak semua alur harus full otomatis; pengembalian dan inventaris lebih cocok diprioritaskan dibanding peminjaman penuh.
- Self-service untuk siswa diasumsikan dimulai dari pengembalian, bukan peminjaman penuh, agar risiko operasional lebih rendah.
- ISBN lookup eksternal belum diwajibkan; minimal sistem dapat membaca dan memakai ISBN untuk input, pencarian, dan identifikasi data yang tersedia di aplikasi.

---

### Week 12: Public Homepage & Library Profile

Scope:

- Beranda publik menampilkan profil pustaka sekolah yang informatif, rapi, dan tetap terhubung ke katalog.
- Konten utama: nama pustaka/perpustakaan, nama sekolah, NSP/NPSN, alamat, kontak, jam layanan, visi, misi, tujuan, SOP pustaka, foto kepala sekolah, dan sambutan singkat.
- Data profil pustaka dikelola dari menu Pengaturan agar tidak hardcoded di halaman Blade.

| # | Task | Type | Hours | Dependencies | Status |
|---|------|------|-------|--------------|--------|
| 7.1 | Desain struktur konten profil pustaka untuk beranda publik | Frontend | 2 | Welcome page | [x] |
| 7.2 | Tambahkan field pengaturan profil pustaka: nama pustaka, sekolah, NSP/NPSN, visi, misi, tujuan, SOP, jam layanan, kontak | Backend/Frontend | 5 | Settings module | [x] |
| 7.3 | Tambahkan upload foto kepala sekolah dan data sambutan kepala sekolah | Backend/Frontend | 4 | Settings module | [x] |
| 7.4 | Update halaman welcome menjadi beranda profil pustaka + akses katalog | Frontend | 6 | 7.1-7.3 | [x] |
| 7.5 | Tambahkan section SOP pustaka: ringkasan aturan, alur layanan, dan link/download dokumen SOP | Backend/Frontend | 4 | 7.2 | [x] |
| 7.6 | Tampilkan statistik publik ringan: total koleksi, eksemplar tersedia, kategori, dan buku terbaru | Backend/Frontend | 4 | Catalog/Dashboard module | [x] |
| 7.7 | Responsive polish beranda untuk mobile, tablet, dan desktop | Frontend | 3 | 7.4-7.6 | [x] |
| 7.8 | Testing beranda profil pustaka dan pengaturan kontennya | Testing | 3 | 7.2-7.7 | [~] |
| 7.9 | Update dokumentasi admin untuk mengelola profil pustaka beranda | Docs | 1 | 7.2-7.7 | [x] |

**Week 12 Subtotal: ~32 hours** (Frontend: ~11, Backend/Frontend: ~17, Testing: ~3, Docs: ~1)

Acceptance scenarios:

- Beranda menampilkan nama pustaka, NSP/NPSN, visi, misi, SOP, kontak, dan jam layanan.
- Foto dan sambutan kepala sekolah tampil baik di desktop dan mobile.
- Admin dapat mengubah konten profil pustaka dari pengaturan.
- Jika foto kepala sekolah atau SOP belum diisi, halaman tetap rapi dengan fallback.
- Tombol katalog dan login tetap mudah ditemukan.
- Statistik publik tidak menampilkan data sensitif.

Assumptions:

- Week 12 diimplementasikan melalui `HomeController`, Settings, halaman welcome, dan dokumentasi admin.
- Testing fitur sudah ditambahkan, tetapi eksekusi test PHP lokal masih perlu environment PHP yang kompatibel.
- Istilah `nsp` dicatat sebagai `NSP/NPSN` agar aman mengikuti istilah administrasi sekolah.
- Data profil pustaka dikelola lewat Settings, bukan teks statis di file Blade.

---

### Week 14: Integrasi Dapodik

Scope:

- Integrasi ke Web Service Dapodik lokal untuk menarik data sekolah, siswa, guru/PTK, dan rombel.
- Data hasil tarik dipreview dulu sebelum disinkronkan ke anggota perpustakaan.
- Modul ini difokuskan untuk update anggota perpustakaan dari sumber Dapodik, bukan menggantikan modul import manual yang sudah ada.

Design principles:

- Token disimpan aman dan hanya admin yang bisa mengelola.
- Sinkronisasi tidak langsung menimpa data tanpa preview.
- Konflik data ditampilkan jelas: data baru, data berubah, data cocok, data perlu dicek.
- Data siswa/guru yang ditarik dipetakan ke `members`.

| # | Task | Type | Hours | Dependencies | Status |
|---|------|------|-------|--------------|--------|
| 9.1 | Desain alur Integrasi Dapodik: konfigurasi, test koneksi, tarik data, preview, sinkronisasi, dan riwayat | Product/Backend | 2 | Settings/Members module | [ ] |
| 9.2 | Tambahkan pengaturan Dapodik: URL lokal, token Web Service, timeout, dan status koneksi terakhir | Backend/Frontend | 4 | Settings module | [ ] |
| 9.3 | Simpan token Dapodik secara aman dan batasi akses hanya untuk admin | Backend/Security | 3 | 9.2 | [ ] |
| 9.4 | Buat DapodikService untuk request Web Service, normalisasi response, retry ringan, dan error handling | Backend | 6 | 9.2-9.3 | [ ] |
| 9.5 | Buat fitur Test Koneksi Dapodik dengan pesan sukses/gagal yang mudah dipahami | Backend/Frontend | 3 | 9.4 | [ ] |
| 9.6 | Implementasi Tarik Data Sekolah untuk melengkapi profil sekolah/perpustakaan | Backend | 3 | 9.4, Week 12 | [ ] |
| 9.7 | Implementasi Tarik Data Siswa dan mapping ke anggota tipe `student` | Backend | 5 | 9.4, Members module | [ ] |
| 9.8 | Implementasi Tarik Data Guru/PTK dan mapping ke anggota tipe `teacher` atau `staff` | Backend | 5 | 9.4, Members module | [ ] |
| 9.9 | Implementasi Tarik Data Rombel untuk mapping kelas/rombel siswa | Backend | 4 | 9.4, 9.7 | [ ] |
| 9.10 | Buat halaman Preview Data Dapodik: data baru, berubah, cocok, duplikat, dan perlu dicek | Backend/Frontend | 6 | 9.7-9.9 | [ ] |
| 9.11 | Implementasi sinkronisasi ke anggota perpustakaan dengan mode dry-run dan konfirmasi final | Backend/Frontend | 6 | 9.10 | [ ] |
| 9.12 | Tambahkan aturan update anggota: cocokkan berdasarkan NISN untuk siswa, NIP/NUPTK/employee number untuk PTK, lalu email/nama sebagai fallback review | Backend | 4 | 9.11 | [ ] |
| 9.13 | Buat Riwayat Sinkronisasi: waktu, user, sumber data, total ditarik, dibuat, diperbarui, dilewati, error | Backend/Frontend | 5 | 9.11 | [ ] |
| 9.14 | Tambahkan audit log untuk konfigurasi, test koneksi, preview, dan sinkronisasi Dapodik | Backend | 3 | Audit module, 9.2-9.13 | [ ] |
| 9.15 | Tambahkan menu sidebar Integrasi Dapodik untuk admin/pustakawan berizin | Frontend | 2 | 9.2-9.13 | [ ] |
| 9.16 | Testing Integrasi Dapodik: koneksi, token salah, response kosong, preview, sync siswa, sync PTK, sync rombel, dan history | Testing | 6 | 9.2-9.15 | [ ] |
| 9.17 | Update dokumentasi admin untuk setup URL/token Dapodik dan prosedur sinkronisasi aman | Docs | 2 | 9.2-9.15 | [ ] |

**Week 14 Subtotal: ~69 hours**.

MVP scope:

- Wajib MVP:
  - Konfigurasi URL Dapodik lokal.
  - Token Web Service.
  - Test koneksi.
  - Tarik siswa.
  - Tarik guru/PTK.
  - Tarik rombel.
  - Preview data.
  - Sinkronkan/update anggota perpustakaan.
  - Riwayat sinkronisasi dasar.
- Bisa fase lanjutan:
  - Sinkron profil sekolah otomatis penuh.
  - Penjadwalan sinkronisasi otomatis.
  - Local connector/proxy jika Sipustadig berjalan di cloud dan tidak bisa mengakses Dapodik lokal.

Acceptance scenarios:

- Admin dapat menyimpan URL dan token Dapodik.
- Test koneksi menampilkan status jelas untuk sukses, token salah, URL salah, timeout, atau Dapodik offline.
- Preview menampilkan data siswa, guru/PTK, dan rombel sebelum sinkronisasi.
- Sinkronisasi membuat anggota baru dan memperbarui anggota lama tanpa duplikasi.
- Data rombel masuk ke field kelas anggota siswa.
- Riwayat sinkronisasi mencatat jumlah data ditarik, dibuat, diperbarui, dilewati, dan error.
- Token tidak tampil polos di UI setelah disimpan.
- User tanpa izin tidak bisa membuka konfigurasi atau menjalankan sync.

Assumptions:

- Perubahan yang diminta sekarang hanya update `TASKS.md`, bukan implementasi kode.
- Week 14 menjadi enhancement baru setelah Week 13.
- MVP mengasumsikan Sipustadig dapat menjangkau Dapodik lokal dari jaringan yang sama.
- Jika aplikasi production berjalan di cloud, integrasi Dapodik lokal perlu mekanisme tambahan seperti local connector/proxy dan dicatat sebagai fase lanjutan.
- Sinkronisasi Dapodik tidak menghapus anggota perpustakaan otomatis; data yang tidak ditemukan lagi ditandai untuk review manual.

---

### Week 13: Digital Books via SIBI/Kemendikdasmen Links

Scope:

- Aplikasi menampilkan metadata, cover/gambar, dan rincian buku digital tanpa menyimpan file PDF/audio/interaktif di server.
- Saat siswa membuka buku digital, aplikasi mengarahkan ke sumber resmi `https://buku.kemendikdasmen.go.id/`.
- Buku digital tampil berdampingan dengan katalog fisik, diberi badge `Digital`, dan tidak ikut alur stok eksemplar, peminjaman, pengembalian, atau denda.

Design principles:

- Server Sipustadig hanya menyimpan metadata/link, bukan file digital.
- Cover boleh berupa URL gambar eksternal atau upload thumbnail ringan jika dibutuhkan.
- Tombol siswa: `Buka di SIBI/Kemendikdasmen`, membuka tab baru ke sumber resmi.
- Versi awal memakai input/kurasi manual oleh admin melalui link resmi, belum membutuhkan API/scraping otomatis.

| # | Task | Type | Hours | Dependencies | Status |
|---|------|------|-------|--------------|--------|
| 8.1 | Desain konsep buku digital berbasis metadata + external link SIBI/Kemendikdasmen | Product/Frontend | 2 | Books/Catalog module | [x] |
| 8.2 | Tambahkan field metadata buku digital: tipe buku, cover URL, external URL, sumber, jenjang, kelas, mapel/kategori, dan status tampil | Backend | 4 | Books module | [x] |
| 8.3 | Update form create/edit buku untuk input link SIBI/Kemendikdasmen dan preview cover/rincian | Backend/Frontend | 5 | 8.2 | [x] |
| 8.4 | Tambahkan validasi external URL agar hanya menerima URL sumber resmi/aman dan tetap mendukung input manual admin | Backend | 3 | 8.2-8.3 | [x] |
| 8.5 | Update katalog publik untuk filter buku fisik/digital, badge digital, dan kartu buku digital | Backend/Frontend | 5 | Catalog module, 8.2 | [x] |
| 8.6 | Update halaman detail katalog untuk menampilkan rincian buku digital dan tombol `Buka di SIBI/Kemendikdasmen` | Frontend | 4 | 8.5 | [x] |
| 8.7 | Tambahkan halaman/list admin khusus untuk kurasi buku digital dari link resmi | Backend/Frontend | 5 | 8.2-8.4 | [~] |
| 8.8 | Tambahkan import buku digital dari Excel/CSV berisi metadata dan URL SIBI/Kemendikdasmen | Backend/Frontend | 5 | Import module, 8.2 | [x] |
| 8.9 | Tambahkan tracking ringan klik buka buku digital tanpa menyimpan konten buku | Backend | 3 | Audit/Catalog module | [ ] |
| 8.10 | Tambahkan empty state dan fallback cover untuk buku digital tanpa gambar | Frontend | 2 | 8.5-8.6 | [x] |
| 8.11 | Testing buku digital: input link, validasi URL, katalog, detail, filter, import, dan tracking klik | Testing | 5 | 8.3-8.10 | [~] |
| 8.12 | Update dokumentasi admin dan user tentang buku digital berbasis link resmi | Docs | 2 | 8.3-8.10 | [x] |

**Week 13 Subtotal: ~45 hours** (Product/Frontend: ~2, Backend: ~10, Backend/Frontend: ~20, Frontend: ~6, Testing: ~5, Docs: ~2)

Acceptance scenarios:

- Admin dapat menambahkan buku digital dengan cover, rincian, dan link SIBI/Kemendikdasmen.
- Link digital tidak mengunggah/menyimpan file PDF di server.
- Katalog dapat memfilter buku fisik, digital, atau semua.
- Detail buku digital menampilkan metadata dan tombol keluar ke situs resmi.
- URL tidak valid atau sumber tidak aman ditolak atau ditandai perlu verifikasi.
- Jika cover tidak tersedia, tampilan tetap rapi dengan fallback.
- Import buku digital dari Excel/CSV berhasil dan memberi laporan error yang jelas.
- Klik buku digital tercatat sebagai aktivitas ringan tanpa menyimpan konten buku.

Assumptions:

- Week 13 mulai diimplementasikan sebagai metadata/link-only: field digital di buku, validasi link resmi SIBI/Kemendikdasmen, form admin, filter/badge katalog publik, detail dengan tombol sumber resmi, import CSV/XLSX, fallback cover, test fitur awal, dan dokumentasi admin. Halaman kurasi khusus dan tracking klik masih belum dibuat.
- Nama sumber di UI menggunakan istilah **SIBI/Kemendikdasmen** agar jelas bagi siswa dan admin.
- Buku digital tidak ikut alur stok eksemplar, peminjaman, pengembalian, atau denda.
- Integrasi otomatis API/scraping SIBI belum diwajibkan; versi awal memakai input/kurasi manual oleh admin melalui link resmi.

---

## 📋 Detailed Frontend Tasks by Component

### 🟦 Layout & Components
| # | Task | Hours | Status |
|---|------|-------|--------|
| F-L01 | Layout dashboard (sidebar, header, content) | 4 | [x] |
| F-L02 | Navigation sidebar (role-based menu) | 3 | [x] |
| F-L03 | Header (user menu, notifications) | 2 | [x] |
| F-L04 | Responsive sidebar (collapse, mobile) | 2 | [x] |
| F-L05 | Error pages (404, 403, 500) | 1 | [x] |

### 🟦 Shared UI Components
| # | Task | Hours | Status |
|---|------|-------|--------|
| F-C01 | Button component (primary, secondary, danger, ghost) | 1 | [x] |
| F-C02 | Input components (text, select, textarea, checkbox) | 2 | [x] |
| F-C03 | Table component (sortable, paginated, with actions) | 3 | [x] |
| F-C04 | Modal component (confirm, form, fullscreen) | 2 | [x] |
| F-C05 | Toast/Alert components | 1 | [x] |
| F-C06 | Card component (stat, feature, book) | 2 | [x] |
| F-C07 | Badge/Status components | 1 | [x] |
| F-C08 | Loading states (skeleton, spinner) | 1 | [x] |
| F-C09 | Empty state component | 1 | [x] |
| F-C10 | Pagination component | 1 | [x] |
| F-C11 | Dropdown component | 1 | [x] |
| F-C12 | Search input with filter panel | 2 | [x] |

### 🟦 Page Views
| # | Task | Hours | Module | Status |
|---|------|-------|--------|--------|
| F-P01 | Login page | 1 | Auth | [x] |
| F-P02 | Forgot password page | 0.5 | Auth | [x] |
| F-P03 | Dashboard (librarian) | 5 | Dashboard | [x] |
| F-P04 | Dashboard (principal) | 4 | Dashboard | [x] |
| F-P05 | Members list | 2 | Members | [x] |
| F-P06 | Members create/edit | 2 | Members | [x] |
| F-P07 | Members show (profile + history) | 1 | Members | [x] |
| F-P08 | Categories list | 1 | Categories | [x] |
| F-P09 | Categories modal (create/edit) | 0.5 | Categories | [x] |
| F-P10 | Books list | 2 | Books | [x] |
| F-P11 | Books create/edit (cover upload) | 3 | Books | [x] |
| F-P12 | Books show (detail + copies) | 2 | Books | [x] |
| F-P13 | Book copies list | 2 | Copies | [x] |
| F-P14 | Book copies create/edit | 2 | Copies | [x] |
| F-P15 | Generate multiple copies | 2 | Copies | [x] |
| F-P16 | Print label (QR code) | 2 | Copies | [x] |
| F-P17 | Loan create (scan flow) | 5 | Loan | [x] |
| F-P18 | Loan list (active/history) | 2 | Loan | [x] |
| F-P19 | Loan detail | 1 | Loan | [x] |
| F-P20 | Return form | 3 | Return | [x] |
| F-P21 | Extend modal | 1 | Return | [x] |
| F-P22 | Fines list | 2 | Fines | [x] |
| F-P23 | Fine detail (payment modal) | 1 | Fines | [x] |
| F-P24 | Catalog index | 3 | Catalog | [x] |
| F-P25 | Catalog detail | 2 | Catalog | [x] |
| F-P26 | Reports (all types) | 6 | Reports | [x] |
| F-P27 | Settings | 2 | Settings | [x] |
| F-P28 | Audit log | 1 | Audit | [x] |
| F-P29 | Import members | 1.5 | Import | [x] |
| F-P30 | Import books | 1.5 | Import | [x] |
| F-P31 | Import copies | 1 | Import | [x] |

**Total: ~75 component/page tasks (~80 hours)**

---

## Summary

| Phase | Description | Weeks | Frontend Hours | Backend Hours | Total Hours |
|-------|-------------|-------|----------------|---------------|-------------|
| 0 | Project Setup & Foundation | 2 | ~10 | ~20 | ~52 |
| 1 | Master Data | 2 | ~23 | ~27 | ~63 |
| 2 | Core Transactions | 2 | ~19 | ~36 | ~67 |
| 3 | Catalog & Dashboard | 1 | ~16 | ~13 | ~33 |
| 4 | Reports & Settings | 1 | ~11 | ~27 | ~42 |
| 5 | Polish & Launch | 2 | ~14 | ~14 | ~58 |
| **Total** | **MVP Complete** | **10** | **~93** | **~137** | **~318** |

---

## Design System Implementation (Week 11 - Special Task)

| # | Task | Type | Hours | Dependencies | Status |
|---|------|------|-------|--------------|--------|
| DS-01 | Setup @theme custom colors di app.css | Frontend | 1 | - | [x] |
| DS-02 | Update layout dashboard.blade.php | Frontend | 0.5 | DS-01 | [x] |
| DS-03 | Update sidebar (topbar gelap #24292f) | Frontend | 1 | DS-02 | [x] |
| DS-04 | Update header | Frontend | 0.5 | DS-02 | [x] |
| DS-05 | Update Button component | Frontend | 0.5 | DS-01 | [x] |
| DS-06 | Update Badge component | Frontend | 0.5 | DS-01 | [x] |
| DS-07 | Update Card component | Frontend | 0.5 | DS-01 | [x] |
| DS-08 | Update Input & Select components | Frontend | 0.5 | DS-01 | [x] |
| DS-09 | Update Table component | Frontend | 0.5 | DS-01 | [x] |
| DS-10 | Update Modal, Empty State, Alert, Skeleton, Dropdown | Frontend | 1 | DS-01 | [x] |
| DS-11 | Update Dashboard index view | Frontend | 1 | DS-05-DS-10 | [x] |
| DS-12 | Update Login & Register views | Frontend | 1 | DS-05, DS-07 | [x] |
| DS-13 | Update Welcome page | Frontend | 0.5 | DS-01 | [x] |
| DS-14 | Update Error pages (404, 403, 500) | Frontend | 0.5 | DS-01 | [x] |
| DS-15 | Update Books index view | Frontend | 1 | DS-05-DS-10 | [x] |
| DS-16 | Update Members index view | Frontend | 1 | DS-05-DS-10 | [x] |
| DS-17 | Update remaining pages (create/edit/detail) | Frontend | 3 | All DS | [ ] |

**Design System Total: ~15 hours** (MVP complete; DS-17 optional refinement)
---

## Current Progress

**Update:** 2026-05-23

### MVP Implementation COMPLETED LOCALLY
- [x] Phase 0: Project setup, auth/RBAC, layout, core models, policies, seeders
- [x] Phase 1: Master data anggota, kategori, buku, eksemplar, label barcode/QR, import/export
- [x] Phase 2: Transaksi peminjaman, pengembalian, perpanjangan, denda, pembayaran, pembebasan
- [x] Phase 3: Katalog/OPAC, dashboard pustakawan, dashboard kepala sekolah, chart
- [x] Phase 4: Laporan, pengaturan perpustakaan, audit log
- [x] Phase 5: Import, responsive polish, accessibility pass, docs, cleanup, dan UI/branding fixes

### Next Enhancement Queue: Week 11 QR/Barcode & Automation planned
- [ ] QR Code: kartu anggota, label eksemplar, peminjaman, pengembalian, inventaris, dan self-service terbatas
- [ ] Automation modes: `auto-fill`, `auto-process`, dan `auto-review` untuk lookup, loan draft, return, dan inventory
- [ ] Scanner support: scanner fisik sebagai mode utama, kamera HP sebagai opsi mobile dengan fallback manual
- [ ] Settings, audit trail, dan guardrail self-service untuk mengontrol mode otomatis per alur

### Next Enhancement Queue: Week 12 Public Homepage planned
- [ ] Beranda profil pustaka: nama pustaka, sekolah, NSP/NPSN, alamat, kontak, jam layanan, visi, misi, tujuan, dan SOP
- [ ] Foto kepala sekolah, data jabatan, dan sambutan singkat tampil di halaman depan
- [ ] Konten profil pustaka dikelola dari Settings dengan fallback rapi jika data belum lengkap

### Next Enhancement Queue: Week 13 Digital Books planned
- [ ] Buku digital berbasis metadata/link resmi SIBI/Kemendikdasmen tanpa menyimpan file digital di server
- [ ] Katalog menampilkan badge dan filter buku digital serta tombol `Buka di SIBI/Kemendikdasmen`
- [ ] Admin dapat kurasi/input/import buku digital dengan URL resmi, cover, rincian, dan fallback tampilan

### Next Enhancement Queue: Week 14 Dapodik Integration planned
- [ ] Integrasi Dapodik untuk tarik siswa, guru/PTK, rombel, preview data, dan sinkron anggota perpustakaan
- [ ] Konfigurasi URL lokal, token Web Service, test koneksi, dan riwayat sinkronisasi
- [ ] Mapping Dapodik ke `members` dengan preview dan aturan update yang aman

### Design System Implementation COMPLETED FOR MVP
- [x] DS-01 to DS-16: Core CSS, layout, components, dashboard, auth, welcome, error pages, main index views
- [x] UI polish lanjutan: modal kategori, sidebar compact, brand logo, logout button, Alpine/x-cloak behavior
- [ ] DS-17: Remaining create/edit/detail pages can still be refined after stakeholder review

### Remaining Launch Blockers
- [ ] Setup production environment
- [ ] Final deployment to production
- [ ] DNS & SSL verification
- [ ] Backup verification
- [ ] Final test suite run on PHP 8.4+
- [ ] Stakeholder acceptance/sign-off

### Historical Snapshot Before Week 10 Polish

### Phase 0: Project Setup & Foundation ✅ COMPLETED (90%)
- [x] Week 1: Environment & Auth
- [x] Week 2: Database & Core Models (except migration run, Vercel setup)

### Phase 1: Master Data ✅ COMPLETED
- [x] Members: Service, Controller, Request, Views (F05-F08), Tests (1.8-1.9)
- [x] Categories: Service, Controller, Views (F09), Tests (1.10)
- [x] Books: Service, Controller, Request, Cover Upload, Views (F10-F12), Tests (1.21-1.22)
- [x] Book Copies: Service, Controller, Auto-generate codes, Print labels, Views (F13-F15), Tests (1.23)

### Design System Implementation 🚧 IN PROGRESS (80%)
- [x] DS-01 to DS-16: Core CSS, Layout, Components, Dashboard, Auth, Welcome, Error pages, Main index views
- [ ] DS-17: Remaining create/edit/detail pages

### Phase 2-5: Superseded by current progress above

---

## Task List by Module

### Members Module
- [x] F05-F08, 1.1-1.5
- [ ] 1.8-1.10 (Testing)
- Frontend: ~11 hours ✅ DONE
- Backend: ~10 hours ✅ DONE
- Testing: ~6 hours pending

### Categories Module
- [x] F09, 1.6-1.7
- [ ] 1.10 (Testing)
- Frontend: ~2 hours ✅ DONE
- Backend: ~3 hours ✅ DONE
- Testing: ~1 hour pending

### Books Module
- [x] F10-F12, 1.11-1.15, 1.21-1.22
- Frontend: ~10 hours ✅ DONE
- Backend: ~10.5 hours ✅ DONE
- Testing: ~5 hours ✅ DONE

### Book Copies Module
- [x] F13-F15, 1.16-1.20, 1.23
- Frontend: ~8 hours ✅ DONE
- Backend: ~10.5 hours ✅ DONE
- Testing: ~2 hours ✅ DONE

### Loan Module
- [x] F16-F18, 2.1-2.8, 2.9-2.10
- Frontend: ~10 hours âœ… DONE
- Backend: ~17 hours âœ… DONE
- Testing: ~7 hours âœ… DONE

### Return & Fine Module
- [x] F19-F22, 2.11-2.24
- Frontend: ~9 hours âœ… DONE
- Backend: ~19 hours âœ… DONE
- Testing: ~9 hours âœ… DONE

### Catalog Module
- [x] F23-F24, 3.1-3.2, 3.5
- Frontend: ~7 hours âœ… DONE
- Backend: ~5 hours âœ… DONE
- Testing: ~2 hours âœ… DONE

### Dashboard Module
- [x] F25-F27, 3.3-3.4, 3.6
- Frontend: ~13 hours âœ… DONE
- Backend: ~8 hours âœ… DONE
- Testing: ~2 hours âœ… DONE

### Reports Module
- [x] F28, 4.1-4.4, 4.9-4.10
- Frontend: ~6 hours âœ… DONE
- Backend: ~18 hours âœ… DONE
- Testing: ~7 hours âœ… DONE

### Settings Module
- [x] F29, 4.5-4.6
- Frontend: ~3 hours âœ… DONE
- Backend: ~4 hours âœ… DONE

### Audit Module
- [x] F30, 4.7-4.8
- Frontend: ~2 hours âœ… DONE
- Backend: ~5 hours âœ… DONE

### Import Module
- [x] F31, 5.1-5.5
- Frontend: ~4 hours âœ… DONE
- Backend: ~14 hours âœ… DONE

### Polish & Launch
- [x] F32-F34, 5.6-5.9
- [x] F35, 5.10-5.12, 5.17
- [ ] 5.13-5.16, 5.18-5.19 pending production access/PHP 8.4/stakeholder sign-off
- Frontend: ~8 hours âœ… DONE
- Backend: ~8 hours âœ… DONE
- Testing: ~3 hours âœ… DONE
- Docs: ~8 hours
- DevOps: ~7 hours

---

## Critical Path

```
Setup → Auth → DB Models → Members CRUD → Books CRUD → Book Copies → Loan → Return → Dashboard → Reports → Launch
```

**Longest chain:** ~50 tasks sequential
**Parallel opportunities:** Members & Categories (Week 3), Books & Copies (Week 4), Return & Fine (Week 6)

---

## Frontend Development Notes

### Tech Stack
- **Framework:** Blade + Livewire (for dynamic components)
- **CSS:** Tailwind CSS
- **JavaScript:** Alpine.js (for simple interactions) + Livewire
- **Charts:** Chart.js
- **Icons:** Heroicons atau Lucide
- **Fonts:** Inter (Google Fonts)

### Key Frontend Decisions
1. **Server-side rendering with Livewire** - Simpler than SPA for MVP
2. **Tailwind for styling** - Fast development, consistent design
3. **Datatables via Livewire** - Server-side pagination for large datasets
4. **Alpine.js for simple JS** - Lightweight, no build step

### Frontend Milestones
1. **Week 1:** Base layout, auth pages
2. **Week 3:** CRUD views (Members, Categories)
3. **Week 4:** CRUD views (Books, Copies)
4. **Week 5-6:** Transaction views (Loan, Return, Fine)
5. **Week 7:** Dashboard & Catalog
6. **Week 8:** Reports & Admin views
7. **Week 9-10:** Polish & responsive
