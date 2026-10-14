# Design System — Aplikasi Perpustakaan Sekolah

## Ringkasan

Desain aplikasi memakai gaya dashboard sekolah yang bersih, compact, ringan, dan mudah dipakai pustakawan. Aplikasi tidak menyediakan mode terang/gelap. Seluruh halaman menggunakan satu tema terang lembut dengan latar abu-biru muda agar nyaman digunakan lama di ruang perpustakaan.

Fokus utama desain adalah mempercepat pekerjaan pustakawan saat mengelola katalog buku, eksemplar, anggota, peminjaman, pengembalian, denda, laporan, dan scan barcode/QR.

## Prinsip Desain

- Prioritaskan keterbacaan data buku, anggota, dan transaksi.
- Gunakan layout compact agar pustakawan dapat melihat banyak data sekaligus.
- Gunakan warna status secara konsisten untuk ketersediaan buku, pinjaman, denda, dan kondisi eksemplar.
- Hindari visual terlalu ramai, gradient, shadow besar, dan animasi berlebihan.
- Gunakan card putih, border halus, radius kecil-menengah, dan kontrol sederhana.
- Optimalkan alur transaksi peminjaman dan pengembalian agar cepat digunakan dengan scanner barcode.
- Pastikan tampilan nyaman di desktop/laptop pustakawan dan tetap responsif di mobile untuk katalog siswa/guru.

---

## Design Tokens

```yaml
version: 1
name: Soft School Library Dashboard
description: Compact school library dashboard with soft light background, clean cards, searchable catalog, barcode-friendly transaction forms, and clear loan/fine statuses.

colors:
  background: "#eef2f6"
  surface: "#ffffff"
  surfaceSubtle: "#f6f8fa"
  border: "#d0d7de"
  borderMuted: "#d8dee4"
  text: "#24292f"
  muted: "#57606a"
  mutedLight: "#8c959f"

  primary: "#0969da"
  primaryHover: "#0757b5"

  success: "#1f883d"
  successHover: "#1a7f37"
  successSurface: "#dafbe1"

  danger: "#cf222e"
  dangerHover: "#a40e26"
  dangerSurface: "#ffebe9"

  warning: "#9a6700"
  warningSurface: "#fff8c5"

  info: "#0969da"
  infoSurface: "#ddf4ff"

  neutral: "#57606a"
  neutralSurface: "#f6f8fa"

  topbar: "#24292f"
  topbarText: "#ffffff"
  topbarMuted: "#d0d7de"

  bookAvailable: "#1f883d"
  bookBorrowed: "#0969da"
  bookOverdue: "#cf222e"
  bookDamaged: "#9a6700"
  bookLost: "#6e7781"

typography:
  fontFamily: "-apple-system, BlinkMacSystemFont, Segoe UI, Helvetica, Arial, sans-serif"
  body:
    fontSize: "14px"
    lineHeight: 1.5
    fontWeight: 400
  pageTitle:
    fontSize: "24px"
    lineHeight: 1.25
    fontWeight: 600
    letterSpacing: "-0.01em"
  sectionTitle:
    fontSize: "18px"
    lineHeight: 1.3
    fontWeight: 600
  cardTitle:
    fontSize: "16px"
    lineHeight: 1.25
    fontWeight: 600
  compactTitle:
    fontSize: "14px"
    lineHeight: 1.25
    fontWeight: 700
  label:
    fontSize: "14px"
    fontWeight: 600
  tableHeader:
    fontSize: "12px"
    fontWeight: 600
    textTransform: uppercase
  helper:
    fontSize: "12px"
    lineHeight: 1.4
    fontWeight: 400

radius:
  sm: "6px"
  md: "8px"
  lg: "12px"
  pill: "999px"

spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"

shadow:
  card: "0 1px 0 rgba(27, 31, 36, .04)"
```

---

## Theme Policy

- Tidak ada toggle mode terang/gelap.
- Tidak ada script `localStorage` untuk theme.
- Tidak ada class `dark:` pada source aplikasi.
- Semua halaman menggunakan theme terang lembut.
- Background utama: `#eef2f6`.
- Card, form, modal, dan tabel memakai surface putih.
- Warna aksen dipakai seperlunya untuk aksi utama dan status penting.

---

## Layout Utama

- Body memakai background `#eef2f6` dan teks `#24292f`.
- Konten utama memakai container maksimal `1180px`.
- Halaman transaksi peminjaman/pengembalian boleh memakai layout lebih lebar hingga `1360px`.
- Halaman katalog publik/internal memakai grid buku responsif.
- Padding utama desktop sekitar `24px` sampai `32px`.
- Pada mobile, spacing dikurangi dan tombol utama dapat menjadi full width.
- Data tabel tetap dibungkus horizontal scroll pada layar kecil.

```css
body {
  background: #eef2f6;
  color: #24292f;
  font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Helvetica, Arial, sans-serif;
  font-size: 14px;
  line-height: 1.5;
}

.app-container {
  margin: 0 auto;
  max-width: 1180px;
  padding: 32px 20px;
}

.app-container-wide {
  margin: 0 auto;
  max-width: 1360px;
  padding: 32px 20px;
}
```

---

## Topbar

Topbar digunakan untuk navigasi utama pustakawan, admin, kepala sekolah, guru, dan siswa.

- Topbar memakai background gelap `#24292f`.
- Logo sekolah/perpustakaan ditampilkan dalam kotak putih kecil agar tetap terlihat.
- Nama sekolah memakai teks muted `#d0d7de`.
- Nama aplikasi dapat ditampilkan sebagai `Perpustakaan Sekolah`.
- Navigasi aktif memakai background putih dan teks gelap.
- Navigasi nonaktif memakai teks muted dan hover putih transparan.
- Tombol logout memakai border putih transparan.
- Pada mobile, topbar berubah menjadi stack vertikal.

Menu pustakawan yang direkomendasikan:

```text
Dashboard
Anggota
Buku
Eksemplar
Peminjaman
Pengembalian
Denda
Katalog
Laporan
Pengaturan
```

```css
.topbar {
  background: #24292f;
  color: #ffffff;
}

.topbar-inner {
  align-items: center;
  display: flex;
  gap: 16px;
  justify-content: space-between;
  margin: 0 auto;
  max-width: 1180px;
  padding: 12px 20px;
}

.nav-link {
  border-radius: 6px;
  color: #d0d7de;
  font-size: 14px;
  font-weight: 600;
  padding: 6px 10px;
  text-decoration: none;
}

.nav-link:hover {
  background: rgba(255, 255, 255, .12);
  color: #ffffff;
}

.nav-link-active {
  background: #ffffff;
  color: #24292f;
}
```

---

## Sidebar Opsional

Untuk aplikasi yang menunya mulai banyak, sidebar dapat digunakan pada dashboard admin/pustakawan.

- Sidebar hanya direkomendasikan untuk desktop.
- Pada mobile, sidebar berubah menjadi menu dropdown/topbar.
- Sidebar memakai surface putih dengan border kanan halus.
- Menu aktif memakai background `#ddf4ff` dan teks primary.

```text
Dashboard
Master Data
- Anggota
- Kategori Buku
- Buku
- Eksemplar

Transaksi
- Peminjaman
- Pengembalian
- Perpanjangan
- Denda

Laporan
- Peminjaman
- Keterlambatan
- Buku Populer
- Anggota Aktif

Pengaturan
```

---

## Page Header

Setiap halaman utama memiliki header ringkas:

- Judul halaman.
- Deskripsi singkat.
- Aksi utama di kanan.
- Breadcrumb opsional.

Contoh:

```text
Buku
Kelola katalog buku, penulis, penerbit, kategori, dan ketersediaan koleksi.

[Import Buku] [Export] [Tambah Buku]
```

```css
.page-header {
  align-items: flex-start;
  display: flex;
  gap: 16px;
  justify-content: space-between;
  margin-bottom: 16px;
}

.page-title {
  font-size: 24px;
  font-weight: 600;
  letter-spacing: -0.01em;
  line-height: 1.25;
  margin: 0;
}

.page-description {
  color: #57606a;
  margin: 4px 0 0;
}
```

---

## Cards

Card standar digunakan pada dashboard, form, tabel ringkas, detail buku, detail anggota, panel laporan, dan panel transaksi.

```css
.card {
  background: #ffffff;
  border: 1px solid #d0d7de;
  border-radius: 6px;
  box-shadow: 0 1px 0 rgba(27, 31, 36, .04);
}

.card-body {
  padding: 16px;
}

.card-header {
  align-items: center;
  border-bottom: 1px solid #d8dee4;
  display: flex;
  justify-content: space-between;
  padding: 12px 16px;
}

.card-title {
  font-size: 16px;
  font-weight: 600;
  margin: 0;
}
```

---

## Statistic Cards

Statistic card dipakai di dashboard pustakawan dan kepala sekolah.

Contoh data:

```text
Total Buku
Total Eksemplar
Tersedia
Dipinjam
Terlambat
Denda Belum Lunas
Anggota Aktif
Transaksi Hari Ini
```

Aturan desain:

- Gunakan 4 kolom pada desktop.
- Gunakan 2 kolom pada tablet.
- Gunakan 1 kolom pada mobile.
- Angka utama besar tetapi tidak berlebihan.
- Gunakan icon kecil sebagai penanda visual.
- Gunakan warna status hanya pada angka atau badge, bukan seluruh card.

```css
.stats-grid {
  display: grid;
  gap: 12px;
  grid-template-columns: repeat(4, minmax(0, 1fr));
}

.stat-card {
  background: #ffffff;
  border: 1px solid #d0d7de;
  border-radius: 6px;
  padding: 16px;
}

.stat-label {
  color: #57606a;
  font-size: 13px;
  font-weight: 600;
}

.stat-value {
  color: #24292f;
  font-size: 28px;
  font-weight: 700;
  letter-spacing: -0.02em;
  line-height: 1.1;
  margin-top: 8px;
}
```

---

## Book Cards

Book card digunakan pada katalog online dan halaman katalog internal.

Isi card:

- Cover buku.
- Judul buku.
- Penulis.
- Kategori.
- Status ketersediaan.
- Jumlah tersedia/total.
- Lokasi rak, jika ada.

Aturan desain:

- Cover menggunakan rasio konsisten.
- Judul truncate maksimal 2 baris.
- Penulis truncate 1 baris.
- Badge ketersediaan wajib terlihat.
- Card tidak terlalu tinggi agar katalog tetap compact.

```css
.book-grid {
  display: grid;
  gap: 14px;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
}

.book-card {
  background: #ffffff;
  border: 1px solid #d0d7de;
  border-radius: 8px;
  overflow: hidden;
}

.book-cover {
  align-items: center;
  aspect-ratio: 3 / 4;
  background: #f6f8fa;
  border-bottom: 1px solid #d8dee4;
  display: flex;
  justify-content: center;
  overflow: hidden;
}

.book-cover img {
  height: 100%;
  object-fit: cover;
  width: 100%;
}

.book-card-body {
  padding: 12px;
}

.book-title {
  color: #24292f;
  display: -webkit-box;
  font-size: 14px;
  font-weight: 700;
  line-height: 1.3;
  margin: 0;
  overflow: hidden;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
}

.book-meta {
  color: #57606a;
  font-size: 12px;
  margin-top: 4px;
}
```

---

## Compact Data Cards

Compact card dapat digunakan untuk daftar eksemplar, daftar pinjaman aktif, atau daftar denda pada dashboard.

- Grid default gap `12px`.
- `md`: 2 kolom.
- `xl`: 3 kolom.
- Padding kartu: `14px`.
- Judul utama: `14px`, truncate satu baris.
- Metadata: `12px`.
- Badge status: `11px`.
- Tombol aksi: `12px`, padding compact.

Contoh penggunaan:

```text
BK-2026-000001
Matematika Kelas X
Rak A-02 · Baik
[Dipinjam] [Detail]
```

---

## Buttons

Gunakan tombol compact, jelas, dan tidak terlalu besar pada desktop.

Tombol utama:

- `Tambah Buku`
- `Tambah Anggota`
- `Simpan`
- `Pinjam Buku`
- `Proses Pengembalian`
- `Bayar Denda`
- `Export Excel`
- `Cetak Label`
- `Scan Barcode`

```css
.btn {
  align-items: center;
  border: 1px solid rgba(27, 31, 36, .15);
  border-radius: 6px;
  cursor: pointer;
  display: inline-flex;
  font-size: 14px;
  font-weight: 600;
  gap: 6px;
  justify-content: center;
  min-height: 32px;
  padding: 5px 12px;
  text-decoration: none;
}

.btn-primary {
  background: #0969da;
  color: #ffffff;
}

.btn-primary:hover {
  background: #0757b5;
}

.btn-success {
  background: #1f883d;
  color: #ffffff;
}

.btn-success:hover {
  background: #1a7f37;
}

.btn-danger {
  background: #cf222e;
  color: #ffffff;
}

.btn-danger:hover {
  background: #a40e26;
}

.btn-secondary {
  background: #ffffff;
  color: #24292f;
}

.btn-warning {
  background: #9a6700;
  color: #ffffff;
}

.btn-full {
  width: 100%;
}
```

---

## Forms

Form digunakan untuk anggota, buku, eksemplar, peminjaman, pengembalian, denda, dan pengaturan.

Aturan:

- Label pendek dan jelas.
- Input minimal tinggi `36px`.
- Background input putih.
- Border `#d0d7de`.
- Focus memakai border biru dan ring lembut.
- Helper text kecil digunakan untuk field teknis seperti `Kode Eksemplar`, `ISBN`, atau `Nomor Anggota`.
- Field penting diberi tanda wajib.
- Form transaksi harus mendukung scanner barcode yang mengisi input secara otomatis.

```css
.form-grid {
  display: grid;
  gap: 16px;
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

label {
  color: #24292f;
  font-size: 14px;
  font-weight: 600;
}

input,
select,
textarea {
  background: #ffffff;
  border: 1px solid #d0d7de;
  border-radius: 6px;
  color: #24292f;
  font-size: 14px;
  min-height: 36px;
  padding: 7px 10px;
}

input:focus,
select:focus,
textarea:focus {
  border-color: #0969da;
  box-shadow: 0 0 0 3px rgba(9, 105, 218, .15);
  outline: none;
}

.helper-text {
  color: #57606a;
  font-size: 12px;
  line-height: 1.4;
}
```

---

## Barcode/QR Transaction UI

Halaman peminjaman dan pengembalian harus sangat cepat digunakan dengan scanner USB/Bluetooth.

Prinsip:

- Input kode anggota dan kode eksemplar harus terlihat jelas.
- Fokus input otomatis pada field yang sedang dibutuhkan.
- Setelah scan, sistem langsung mencari data.
- Scanner dianggap sebagai keyboard input.
- Tombol Enter dari scanner dapat memicu pencarian.
- Tampilkan hasil scan secara langsung dalam panel ringkas.
- Hindari terlalu banyak modal pada transaksi.

### Layout Peminjaman

```text
[Scan / Input Nomor Anggota]
↓
Panel Anggota
- Nama
- Tipe
- Kelas
- Pinjaman Aktif
- Denda Belum Lunas

[Scan / Input Kode Eksemplar]
↓
Daftar Buku yang Akan Dipinjam

[Proses Peminjaman]
```

### Layout Pengembalian

```text
[Scan / Input Kode Eksemplar]
↓
Panel Pinjaman Aktif
- Nama Anggota
- Judul Buku
- Tanggal Pinjam
- Jatuh Tempo
- Status Terlambat
- Estimasi Denda

[Kondisi Saat Kembali]
[Proses Pengembalian]
```

```css
.scan-input {
  font-size: 18px;
  font-weight: 700;
  min-height: 44px;
  padding: 9px 12px;
}

.scan-panel {
  background: #ddf4ff;
  border: 1px solid rgba(9, 105, 218, .25);
  border-radius: 6px;
  padding: 14px;
}

.transaction-layout {
  display: grid;
  gap: 16px;
  grid-template-columns: minmax(0, 420px) minmax(0, 1fr);
}
```

---

## Tables

Tabel digunakan untuk data yang perlu dibandingkan: anggota, buku, eksemplar, peminjaman, pengembalian, denda, laporan, dan audit log.

Aturan:

- Header tabel memakai background `#eef2f6`.
- Header uppercase kecil.
- Row memakai border bawah halus.
- Pada mobile, tabel dibungkus horizontal scroll.
- Kolom aksi ditempatkan paling kanan.
- Status ditampilkan sebagai badge, bukan teks polos.
- Kolom kode seperti nomor anggota/kode eksemplar memakai font monospace.

```css
.table-wrap {
  border: 1px solid #d0d7de;
  border-radius: 6px;
  overflow-x: auto;
}

.data-table {
  border-collapse: collapse;
  min-width: 860px;
  width: 100%;
}

.data-table th {
  background: #eef2f6;
  border-bottom: 1px solid #d0d7de;
  color: #57606a;
  font-size: 12px;
  font-weight: 600;
  padding: 10px 12px;
  text-align: left;
  text-transform: uppercase;
  white-space: nowrap;
}

.data-table td {
  border-bottom: 1px solid #d8dee4;
  padding: 12px;
  vertical-align: top;
}

.data-table tr:hover td {
  background: #f6f8fa;
}

.code-cell {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  font-size: 13px;
  font-weight: 600;
}
```

---

## Badges

Badge digunakan untuk status buku, eksemplar, pinjaman, denda, anggota, dan metadata ringkas.

Status eksemplar:

- Tersedia: hijau.
- Dipinjam: biru.
- Terlambat: merah.
- Rusak: amber.
- Hilang: abu gelap.
- Diarsipkan: abu.

Status denda:

- Belum Lunas: merah.
- Sebagian: amber.
- Lunas: hijau.
- Dibebaskan: abu.

```css
.badge {
  border-radius: 999px;
  display: inline-flex;
  font-size: 12px;
  font-weight: 600;
  line-height: 1.3;
  padding: 2px 8px;
  white-space: nowrap;
}

.badge-compact {
  font-size: 11px;
  padding: 2px 8px;
}

.badge-success {
  background: #dafbe1;
  color: #1a7f37;
}

.badge-primary {
  background: #ddf4ff;
  color: #0969da;
}

.badge-danger {
  background: #ffebe9;
  color: #cf222e;
}

.badge-warning {
  background: #fff8c5;
  color: #9a6700;
}

.badge-neutral {
  background: #f6f8fa;
  color: #57606a;
}
```

---

## Alerts dan Flash Messages

Flash success dan error memakai card putih dengan border halus. Gunakan border kiri untuk memperjelas status.

Contoh pesan:

- `Peminjaman berhasil disimpan.`
- `Buku tidak tersedia untuk dipinjam.`
- `Anggota masih memiliki denda belum lunas.`
- `Pengembalian berhasil diproses. Denda keterlambatan: Rp3.000.`
- `Import selesai. 120 data berhasil, 4 data gagal.`

```css
.flash,
.errors,
.warning-box,
.info-box {
  background: #ffffff;
  border: 1px solid #d0d7de;
  border-radius: 6px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 12px 14px;
}

.flash {
  border-left: 4px solid #1f883d;
}

.errors {
  border-left: 4px solid #cf222e;
  color: #cf222e;
}

.warning-box {
  border-left: 4px solid #9a6700;
}

.info-box {
  border-left: 4px solid #0969da;
}
```

---

## Modal dan Dialog

Modal dipakai untuk aksi yang membutuhkan konfirmasi:

- Hapus/nonaktifkan anggota.
- Nonaktifkan buku.
- Batalkan peminjaman.
- Bebaskan denda.
- Tandai buku hilang.
- Tandai buku rusak.
- Konfirmasi import data.

Aturan:

- Modal tidak dipakai untuk proses scan utama.
- Modal harus punya judul jelas.
- Aksi berisiko memakai tombol danger.
- Untuk pembebasan denda, alasan wajib diisi.

```css
.modal-backdrop {
  align-items: center;
  background: rgba(36, 41, 47, .45);
  display: flex;
  inset: 0;
  justify-content: center;
  padding: 20px;
  position: fixed;
  z-index: 50;
}

.modal {
  background: #ffffff;
  border-radius: 8px;
  max-width: 520px;
  width: 100%;
}

.modal-header,
.modal-footer {
  padding: 16px;
}

.modal-body {
  padding: 0 16px 16px;
}
```

---

## Katalog Online / OPAC UI

Katalog digunakan siswa dan guru untuk mencari buku.

Komponen utama:

- Search besar di atas.
- Filter kategori, penulis, tahun, dan status.
- Grid book card.
- Detail buku.
- Informasi ketersediaan dan lokasi rak.
- Tombol `Lihat Detail`.

Aturan:

- Katalog harus lebih visual daripada halaman admin.
- Cover buku terlihat jelas.
- Status ketersediaan harus mudah ditemukan.
- Filter tidak boleh terlalu kompleks pada MVP.

Contoh copy:

```text
Cari buku berdasarkan judul, penulis, ISBN, atau kata kunci.
```

```text
Tersedia 3 dari 5 eksemplar
Rak: A-02
```

---

## Detail Buku UI

Halaman detail buku menampilkan:

- Cover buku.
- Judul.
- Penulis.
- Penerbit.
- Tahun terbit.
- Kategori.
- ISBN.
- Deskripsi.
- Daftar eksemplar.
- Status setiap eksemplar.
- Lokasi rak.
- Riwayat peminjaman, untuk pustakawan/admin.

Layout desktop:

```text
[Cover Buku] [Informasi Buku]
             [Ketersediaan]
             [Daftar Eksemplar]
```

---

## Detail Anggota UI

Halaman detail anggota menampilkan:

- Identitas anggota.
- Status aktif/nonaktif.
- Pinjaman aktif.
- Riwayat peminjaman.
- Denda belum lunas.
- Total peminjaman.
- Tombol cetak kartu anggota, fase 2.

Warna merah hanya digunakan jika anggota memiliki denda belum lunas atau pinjaman terlambat.

---

## Denda UI

Denda harus tampil tegas tetapi tidak menakutkan.

Informasi minimum:

- Nama anggota.
- Judul buku.
- Jenis denda.
- Nominal.
- Status pembayaran.
- Tanggal dibuat.
- Tombol `Bayar`, `Bebaskan`, dan `Detail`.

Gunakan format rupiah konsisten:

```text
Rp1.000
Rp15.000
Rp0
```

---

## Laporan UI

Halaman laporan terdiri dari:

- Filter tanggal.
- Filter kelas/tipe anggota.
- Filter kategori/status.
- Ringkasan angka.
- Tabel hasil.
- Tombol export.

Tombol export:

```text
Export Excel
Export CSV
Export PDF
```

Untuk MVP, `Export Excel` menjadi prioritas utama. `Export PDF` boleh dibuat sederhana.

---

## Empty State

Empty state harus membantu pengguna mengambil tindakan.

Contoh:

```text
Belum ada data buku.
Tambahkan buku pertama atau import dari Excel.
[Tambah Buku] [Import Buku]
```

```text
Tidak ada pinjaman terlambat.
Semua peminjaman masih dalam batas waktu.
```

```css
.empty-state {
  align-items: center;
  background: #ffffff;
  border: 1px dashed #d0d7de;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 32px 16px;
  text-align: center;
}

.empty-state-title {
  font-size: 16px;
  font-weight: 700;
}

.empty-state-text {
  color: #57606a;
  max-width: 420px;
}
```

---

## Loading State

Gunakan loading state sederhana:

- Skeleton untuk tabel dan card.
- Spinner kecil pada tombol yang sedang menyimpan.
- Jangan gunakan loading full-screen kecuali saat login atau proses besar.

Copy loading:

```text
Menyimpan...
Memproses peminjaman...
Mengambil data buku...
Mengimport data...
```

---

## Icons

Gunakan ikon inline SVG lewat komponen `x-icon`.

Ukuran standar:

- `14px` untuk metadata.
- `16px` untuk tombol.
- `20px` untuk card statistik.
- `24px` sampai `32px` untuk empty state/modal.

Ikon yang dibutuhkan:

```text
book
books
user
users
barcode
qr-code
search
plus
edit
trash
download
upload
file-spreadsheet
calendar
clock
check
x
alert-triangle
money
settings
log-out
```

---

## Responsive Rules

- Pada layar kecil, topbar dan header halaman berubah menjadi kolom.
- Tombol di konten utama dapat menjadi full width.
- Tabel tetap horizontal scroll.
- Book grid menjadi 2 kolom kecil atau 1 kolom jika layar sangat sempit.
- Transaction layout menjadi 1 kolom pada mobile.
- Modal memakai `max-width` dan padding horizontal agar aman di layar kecil.
- Input scan tetap besar agar mudah digunakan di tablet/HP.

```css
@media (max-width: 900px) {
  .stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .transaction-layout {
    grid-template-columns: 1fr;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 700px) {
  .topbar-inner {
    align-items: stretch;
    flex-direction: column;
  }

  .page-header {
    flex-direction: column;
  }

  main button,
  main a[class*="btn"] {
    justify-content: center;
    width: 100%;
  }

  .table-wrap {
    overflow-x: auto;
  }

  .book-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 420px) {
  .book-grid {
    grid-template-columns: 1fr;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }
}
```

---

## Copywriting Rules

- Gunakan bahasa Indonesia yang langsung dan instruksional.
- Heading harus pendek.
- Label form harus jelas.
- Tombol harus menjelaskan aksi.
- Untuk risiko penting, gunakan kalimat eksplisit.
- Hindari istilah teknis jika tidak perlu.
- Gunakan kata `eksemplar` untuk buku fisik individual.
- Gunakan kata `buku` untuk data katalog/judul buku.
- Gunakan kata `anggota` untuk siswa/guru/staf yang terdaftar di perpustakaan.

Contoh label:

```text
Nomor Anggota
Kode Eksemplar
Judul Buku
Penulis
Lokasi Rak
Tanggal Pinjam
Tanggal Jatuh Tempo
Kondisi Saat Kembali
Denda Keterlambatan
```

Contoh tombol:

```text
Tambah Buku
Tambah Anggota
Generate Eksemplar
Cetak Label QR
Proses Peminjaman
Proses Pengembalian
Bayar Denda
Bebaskan Denda
Export Excel
Kembali ke Dashboard
```

Contoh pesan validasi:

```text
Nomor anggota wajib diisi.
Kode eksemplar tidak ditemukan.
Buku ini sedang dipinjam.
Anggota masih memiliki denda belum lunas.
Jumlah pinjaman anggota sudah mencapai batas maksimal.
```

Contoh pesan sukses:

```text
Peminjaman berhasil disimpan.
Pengembalian berhasil diproses.
Data buku berhasil diperbarui.
Import selesai. Periksa laporan gagal jika ada data yang tidak masuk.
```

---

## Do's

- Gunakan background lembut `#eef2f6`.
- Gunakan card putih untuk area baca utama.
- Gunakan border halus untuk struktur.
- Gunakan layout compact untuk halaman admin.
- Buat input scan barcode terlihat besar dan mudah difokuskan.
- Gunakan badge status secara konsisten.
- Tampilkan ketersediaan buku secara jelas.
- Prioritaskan alur transaksi yang cepat.
- Pastikan tabel laporan mudah difilter dan diexport.
- Gunakan empty state yang memberi arahan aksi berikutnya.

## Don'ts

- Jangan menambahkan mode gelap atau toggle tema.
- Jangan memakai background putih penuh pada seluruh layar.
- Jangan gunakan gradient.
- Jangan gunakan shadow besar.
- Jangan membuat tombol desktop terlalu besar.
- Jangan memakai terlalu banyak warna aksen dalam satu card.
- Jangan membuat transaksi peminjaman terlalu banyak langkah.
- Jangan menyembunyikan status denda atau keterlambatan.
- Jangan membuat scan barcode bergantung pada kamera untuk MVP.
- Jangan menaruh informasi penting hanya dalam tooltip.
- Jangan menggunakan istilah teknis yang membingungkan pustakawan.

---

## Halaman Utama yang Harus Mengikuti Design System

### Admin/Pustakawan

```text
/dashboard
/members
/members/create
/members/{member}
/books
/books/create
/books/{book}
/book-copies
/loans
/loans/create
/returns
/fines
/reports
/settings/library
```

### Guru/Siswa

```text
/catalog
/catalog/{book}
/my-loans
/my-fines
/profile
```

---

## Prioritas Implementasi UI MVP

Urutan implementasi tampilan:

1. Layout utama, topbar, button, form, table, badge.
2. Dashboard pustakawan.
3. CRUD anggota.
4. CRUD buku dan kategori.
5. CRUD eksemplar dan label QR.
6. Form peminjaman dengan input scan.
7. Form pengembalian dengan input scan.
8. Halaman denda.
9. Katalog online.
10. Laporan dan export.
11. Empty state, loading state, dan responsive polish.

---

## Catatan Khusus untuk Laravel

- Jika memakai Blade + Livewire, gunakan komponen reusable:
  - `x-layout`
  - `x-topbar`
  - `x-card`
  - `x-button`
  - `x-badge`
  - `x-input`
  - `x-table`
  - `x-empty-state`
  - `x-modal`
  - `x-icon`
- Gunakan class utility Tailwind sesuai token warna.
- Simpan token warna di `tailwind.config.js`.
- Jangan hardcode warna berbeda-beda di tiap halaman.
- Form transaksi dapat memakai Livewire agar pencarian anggota/buku terasa cepat tanpa full reload.
- Untuk scanner USB, cukup gunakan input text dan event `keydown.enter`.

---

## Tailwind Color Mapping

```js
export default {
  theme: {
    extend: {
      colors: {
        app: {
          background: "#eef2f6",
          surface: "#ffffff",
          surfaceSubtle: "#f6f8fa",
          border: "#d0d7de",
          borderMuted: "#d8dee4",
          text: "#24292f",
          muted: "#57606a",
          mutedLight: "#8c959f",
          primary: "#0969da",
          primaryHover: "#0757b5",
          success: "#1f883d",
          successSurface: "#dafbe1",
          danger: "#cf222e",
          dangerSurface: "#ffebe9",
          warning: "#9a6700",
          warningSurface: "#fff8c5",
          infoSurface: "#ddf4ff",
          topbar: "#24292f",
          topbarMuted: "#d0d7de"
        }
      },
      borderRadius: {
        app: "6px"
      },
      boxShadow: {
        card: "0 1px 0 rgba(27, 31, 36, .04)"
      }
    }
  }
}
```

---

## Kesimpulan

Design system ini dibuat untuk aplikasi perpustakaan sekolah yang ringan, cepat, dan mudah dipakai. Tampilan dibuat compact untuk pustakawan, visual untuk katalog siswa/guru, serta jelas untuk transaksi pinjam-kembali berbasis barcode/QR.

Fokus desain MVP:

```text
dashboard → anggota → buku → eksemplar → scan pinjam → scan kembali → denda → laporan → katalog
```
