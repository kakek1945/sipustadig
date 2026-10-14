# Backlog - Sirkulib MVP

**Project:** Sirkulib - Sistem Informasi Perpustakaan Sekolah
**Status:** Backlog Ready
**Last Updated:** 2026-05-22

---

## Prioritas Legend

| Label | Arti |
|-------|------|
| 🔴 Critical | Wajib ada, blokir release |
| 🟠 High | Penting, harus ada di MVP |
| 🟡 Medium | Bagus punya, bisa deferred |
| 🟢 Low | Nice-to-have, Phase 2+ |

---

## 🔴 Critical Tasks (MVP Wajib)

### Authentication & Authorization

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| AUTH-001 | Login dengan email/password | 2 | [ ] | |
| AUTH-002 | Logout dengan session destroy | 1 | [ ] | |
| AUTH-003 | Role-based middleware | 2 | [ ] | |
| AUTH-004 | Proteksi route per role | 1 | [ ] | |
| AUTH-005 | Session timeout 2 jam | 1 | [ ] | |

### Member Management

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| MEMB-001 | CRUD anggota | 5 | [ ] | |
| MEMB-002 | Generate nomor anggota auto | 2 | [ ] | |
| MEMB-003 | Validasi member aktif | 1 | [ ] | |
| MEMB-004 | Search & filter anggota | 3 | [ ] | |
| MEMB-005 | Soft delete (nonaktifkan) | 2 | [ ] | |

### Book Management

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| BOOK-001 | CRUD buku | 5 | [ ] | |
| BOOK-002 | Upload cover | 3 | [ ] | |
| BOOK-003 | Soft delete buku | 2 | [ ] | |
| BOOK-004 | Search judul/penulis/ISBN | 3 | [ ] | |
| BOOK-005 | Filter kategori | 2 | [ ] | |

### Book Copy Management

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| COPY-001 | CRUD eksemplar | 5 | [ ] | |
| COPY-002 | Generate kode unik | 2 | [ ] | |
| COPY-003 | Generate QR/barcode | 3 | [ ] | |
| COPY-004 | Update status eksemplar | 2 | [ ] | |
| COPY-005 | Generate banyak eksemplar | 3 | [ ] | |
| COPY-006 | Update kondisi & lokasi | 2 | [ ] | |

### Loan Transaction

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| LOAN-001 | Form pinjam (scan flow) | 5 | [ ] | |
| LOAN-002 | Validasi anggota aktif | 1 | [ ] | |
| LOAN-003 | Validasi batas pinjam | 2 | [ ] | |
| LOAN-004 | Validasi eksemplar tersedia | 2 | [ ] | |
| LOAN-005 | Generate nomor transaksi | 1 | [ ] | |
| LOAN-006 | Auto hitung due date | 2 | [ ] | |
| LOAN-007 | DB transaction untuk atomic | 3 | [ ] | |
| LOAN-008 | Update status eksemplar | 1 | [ ] | |

### Return Transaction

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| RETN-001 | Form pengembalian | 5 | [ ] | |
| RETN-002 | Hitung keterlambatan | 2 | [ ] | |
| RETN-003 | Hitung denda otomatis | 3 | [ ] | |
| RETN-004 | Input kondisi kembali | 2 | [ ] | |
| RETN-005 | Update status eksemplar | 2 | [ ] | |
| RETN-006 | Update status loan | 1 | [ ] | |

### Fine Management

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| FINE-001 | List denda | 2 | [ ] | |
| FINE-002 | Bayar denda | 3 | [ ] | |
| FINE-003 | Bebaskan denda (dengan alasan) | 3 | [ ] | |
| FINE-004 | Auto create fine dari return | 2 | [ ] | |

### Dashboard

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| DASH-001 | Stats cards librarian | 3 | [ ] | |
| DASH-002 | Overdue list widget | 3 | [ ] | |
| DASH-003 | Recent transactions | 2 | [ ] | |
| DASH-004 | Stats untuk principal | 3 | [ ] | |

---

## 🟠 High Priority Tasks

### Extension (Perpanjangan)

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| EXT-001 | Extend due date | 3 | [ ] | |
| EXT-002 | Validasi belum terlambat | 1 | [ ] | |
| EXT-003 | Validasi max perpanjangan | 1 | [ ] | |
| EXT-004 | Counter perpanjangan | 2 | [ ] | |

### Import/Export

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| IMP-001 | Import anggota Excel | 5 | [ ] | |
| IMP-002 | Import buku Excel | 5 | [ ] | |
| IMP-003 | Export Excel anggota | 3 | [ ] | |
| IMP-004 | Export Excel buku | 3 | [ ] | |
| IMP-005 | Template Excel | 2 | [ ] | |

### Reports

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| REP-001 | Laporan buku | 3 | [ ] | |
| REP-002 | Laporan peminjaman | 3 | [ ] | |
| REP-003 | Laporan keterlambatan | 3 | [ ] | |
| REP-004 | Laporan denda | 3 | [ ] | |
| REP-005 | Export laporan Excel | 3 | [ ] | |
| REP-006 | Laporan buku populer | 2 | [ ] | |
| REP-007 | Laporan anggota aktif | 2 | [ ] | |

### OPAC (Katalog Online)

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| OPAC-001 | Halaman list katalog | 3 | [ ] | |
| OPAC-002 | Search & filter | 3 | [ ] | |
| OPAC-003 | Detail buku | 2 | [ ] | |
| OPAC-004 | Tampilkan ketersediaan | 2 | [ ] | |

### Settings

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| SETT-001 | CRUD pengaturan | 3 | [ ] | |
| SETT-002 | Aturan peminjaman | 2 | [ ] | |
| SETT-003 | Denda per hari | 1 | [ ] | |
| SETT-004 | Format kode/nomor | 2 | [ ] | |

### Audit Log

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| AUD-001 | Log create/update/delete | 3 | [ ] | |
| AUD-002 | Log transaksi pinjam/kembali | 2 | [ ] | |
| AUD-003 | Audit log viewer | 2 | [ ] | |
| AUD-004 | Filter by user/date | 2 | [ ] | |

---

## 🟡 Medium Priority Tasks

### Category Management

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| CAT-001 | CRUD kategori | 2 | [ ] | |
| CAT-002 | Seed kategori default | 1 | [ ] | |

### Member History

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| HIST-001 | Riwayat peminjaman anggota | 3 | [ ] | |
| HIST-002 | Detail transaksi | 2 | [ ] | |

### Label Printing

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| LBL-001 | Cetak label QR eksemplar | 3 | [ ] | |
| LBL-002 | Cetak kartu anggota | 5 | [ ] | |

### Charts & Analytics

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| CHT-001 | Grafik peminjaman bulanan | 3 | [ ] | |
| CHT-002 | Top 10 buku populer | 2 | [ ] | |
| CHT-003 | Trend keterlambatan | 2 | [ ] | |

---

## 🟢 Low Priority (Phase 2+)

### Communication

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| MSG-001 | Notifikasi email jatuh tempo | 5 | [ ] | |
| MSG-002 | Notifikasi WhatsApp (Phase 3) | 8 | [ ] | |
| MSG-003 | Reminder otomatis | 5 | [ ] | |

### Advanced Features

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| ADV-001 | Reservasi buku | 8 | [ ] | |
| ADV-002 | Review & rating buku | 5 | [ ] | |
| ADV-003 | Rekomendasi buku | 8 | [ ] | |
| ADV-004 | Kunjungan perpustakaan | 5 | [ ] | |
| ADV-005 | Integrasi ISBN external | 8 | [ ] | |

### User Experience

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| UX-001 | Scan barcode via kamera HP | 8 | [ ] | |
| UX-002 | Mobile responsive optimization | 5 | [ ] | |
| UX-003 | Dark mode | 3 | [ ] | |

### Scaling

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| SCL-001 | Multi-sekolah/SaaS | 13 | [ ] | |
| SCL-002 | Mobile app | 13 | [ ] | |
| SCL-003 | Digital library/e-book | 13 | [ ] | |

### Integration

| ID | Task | Story Points | Status | Notes |
|----|------|--------------|--------|-------|
| INT-001 | Portal orang tua | 8 | [ ] | |
| INT-002 | SIAS/SISFO integration | 13 | [ ] | |

---

## Bug Tracking

| ID | Bug Description | Severity | Status | Reported | Fixed |
|----|-----------------|----------|--------|----------|--------|
| BUG-001 | - | - | - | - | - |

### Severity Legend

| Level | Arti |
|-------|------|
| 🔴 Critical | Data loss, security breach |
| 🟠 High | Major feature broken |
| 🟡 Medium | Feature degraded, workaround exists |
| 🟢 Low | Minor UI/UX issue |

---

## Technical Debt

| ID | Description | Effort | Priority | Notes |
|----|-------------|--------|----------|-------|
| TD-001 | Gunakan queue untuk import besar | 8 | 🟡 | Batched processing |
| TD-002 | Refactor controller jadi lebih thin | 5 | 🟢 | Move logic to services |
| TD-003 | Tambahkan rate limiting ketat | 3 | 🟠 | Security hardening |
| TD-004 | Setup monitoring (Sentry/New Relic) | 5 | 🟡 | Production observability |
| TD-005 | Optimize dashboard queries | 5 | 🟡 | Add caching |

---

## Ideas & Inspiration

| ID | Idea | Votes | Status | Notes |
|----|------|-------|--------|-------|
| IDEA-001 | Gamification: badge baca | 5 | 🟢 | Phase 3+ |
| IDEA-002 | Reading challenge tracker | 3 | 🟢 | Phase 3+ |
| IDEA-003 | Book wishlist untuk siswa | 3 | 🟢 | Phase 2 |
| IDEA-004 | Dashboard literasi visual | 8 | 🟢 | Phase 3 |
| IDEA-005 | AI book recommendation | 13 | 🟢 | Phase 3 |

---

## Sprint Planning Template

### Sprint N: [Nama Sprint]
**Tanggal:** [Start - End]
**Goal:** [Sprint Goal]

| ID | Task | Assigned To | Status | Notes |
|----|------|------------|--------|-------|

**Review:**
- What went well?
- What didn't go well?
- Action items for next sprint?

**Retrospective:**
- Keep:
- Change:
- Stop: