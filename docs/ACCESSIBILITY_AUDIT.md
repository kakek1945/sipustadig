# Accessibility Audit Sipustadig

Audit ini dilakukan untuk Week 10 sebagai pemeriksaan statis pada Blade views, layout, dan komponen utama.

## Scope

- Layout publik dan dashboard.
- Header, sidebar, navigasi, tombol ikon, form pencarian, tabel, dan gambar cover/foto.
- Pemeriksaan source untuk label form, `alt` image, fokus keyboard, dan landmark dasar.

## Hasil

- Bahasa halaman sudah mengikuti locale aplikasi melalui atribut `lang`.
- Layout dashboard memiliki landmark `header`, `aside`, `nav`, dan `main`.
- Konten utama dapat dilewati dengan skip link.
- Tombol ikon header dan sidebar sudah diberi nama aksesibel melalui `aria-label`.
- Dropdown notifikasi dan menu pengguna mengekspos `aria-expanded`.
- Input pencarian header sudah memiliki `aria-label`.
- Gambar data seperti foto anggota dan cover buku memakai teks alternatif berdasarkan nama/judul ketika tampil dari storage.
- Komponen tombol memiliki focus ring dan state disabled.
- Form CRUD utama menggunakan label visual untuk input penting.

## Perbaikan Week 10

- Menambahkan skip link ke layout publik dan dashboard.
- Menambahkan `id="main-content"` dan `tabindex="-1"` pada area konten utama.
- Menambahkan label ARIA untuk tombol notifikasi, menu pengguna, dan toggle sidebar.
- Menandai ikon dekoratif di header/sidebar sebagai `aria-hidden`.
- Menambahkan `aria-label` untuk pencarian cepat di header.

## Catatan Lanjutan

- Audit ini belum menggantikan pengujian WCAG penuh dengan browser, screen reader, dan automated checker seperti axe.
- Setelah production deploy, lakukan uji keyboard-only pada login, dashboard, CRUD anggota, CRUD buku, peminjaman, pengembalian, laporan, dan import.
- Lakukan sampling kontras warna di browser untuk semua badge status dan tombol aksi.
