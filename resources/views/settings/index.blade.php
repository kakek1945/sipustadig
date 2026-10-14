@extends('layouts.dashboard')

@section('title', 'Pengaturan')

@section('content')
@php
    $value = fn (string $key, mixed $default = '') => old($key, $settings[$key]->value ?? $default);
@endphp

<div class="max-w-5xl space-y-5">
    <div>
        <h1 class="text-2xl font-bold text-app-text">Pengaturan</h1>
        <p class="text-app-muted mt-0.5">Konfigurasi aturan peminjaman, denda, anggota, dan informasi perpustakaan.</p>
    </div>

    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    @if($errors->any())
        <x-alert type="error">{{ $errors->first() }}</x-alert>
    @endif

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="bg-app-surface border border-app-border rounded-md p-5 space-y-6">
        @csrf
        @method('PUT')

        <section>
            <h2 class="text-base font-bold text-app-text mb-3">Aturan Peminjaman</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Hari pinjam</label>
                    <input type="number" name="loan.max_borrow_days" value="{{ $value('loan.max_borrow_days', 7) }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Maks. buku</label>
                    <input type="number" name="loan.max_books" value="{{ $value('loan.max_books', 3) }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Maks. perpanjangan</label>
                    <input type="number" name="loan.max_extensions" value="{{ $value('loan.max_extensions', 2) }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Batas terlambat</label>
                    <input type="number" name="loan.max_overdue_days" value="{{ $value('loan.max_overdue_days', 14) }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
            </div>
        </section>

        <section class="border-t border-app-border pt-5">
            <h2 class="text-base font-bold text-app-text mb-3">Aturan Denda</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Denda per hari</label>
                    <input type="number" name="fine.daily_rate" value="{{ $value('fine.daily_rate', 1000) }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Denda maksimal</label>
                    <input type="number" name="fine.max_amount" value="{{ $value('fine.max_amount', 50000) }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
                <label class="flex items-center gap-2 pt-7">
                    <input type="checkbox" name="fine.waiver_requires_reason" value="1" {{ filter_var($value('fine.waiver_requires_reason', true), FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                    <span class="text-sm font-semibold text-app-text">Bebas denda wajib alasan</span>
                </label>
            </div>
        </section>

        <section class="border-t border-app-border pt-5">
            <h2 class="text-base font-bold text-app-text mb-3">Anggota</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Maks. denda belum lunas</label>
                    <input type="number" name="member.max_unpaid_fines" value="{{ $value('member.max_unpaid_fines', 50000) }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Masa berlaku kartu (tahun)</label>
                    <input type="number" name="member.card_validity_years" value="{{ $value('member.card_validity_years', 3) }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
            </div>
        </section>

        <section class="border-t border-app-border pt-5">
            <h2 class="text-base font-bold text-app-text mb-3">Info Perpustakaan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Nama Perpustakaan</label>
                    <input type="text" name="library.name" value="{{ $value('library.name', 'Perpustakaan Sekolah') }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Nama Sekolah</label>
                    <input type="text" name="library.school_name" value="{{ $value('library.school_name', 'Sekolah') }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">NSP/NPSN</label>
                    <input type="text" name="library.npsn" value="{{ $value('library.npsn') }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Jam Layanan</label>
                    <input type="text" name="library.service_hours" value="{{ $value('library.service_hours') }}" placeholder="Senin-Jumat, 07.30-14.00" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Email</label>
                    <input type="email" name="library.email" value="{{ $value('library.email') }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Telepon</label>
                    <input type="text" name="library.phone" value="{{ $value('library.phone') }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-app-text mb-1">Alamat</label>
                    <textarea name="library.address" rows="2" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">{{ $value('library.address') }}</textarea>
                </div>
            </div>
        </section>

        <section class="border-t border-app-border pt-5">
            <h2 class="text-base font-bold text-app-text mb-3">Profil Beranda Publik</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Visi</label>
                    <textarea name="library.vision" rows="3" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">{{ $value('library.vision') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Misi</label>
                    <textarea name="library.mission" rows="4" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface" placeholder="Tulis satu poin per baris agar tampil rapi di beranda">{{ $value('library.mission') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Tujuan</label>
                    <textarea name="library.goals" rows="4" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface" placeholder="Tulis satu poin per baris agar tampil rapi di beranda">{{ $value('library.goals') }}</textarea>
                </div>
            </div>
        </section>

        <section class="border-t border-app-border pt-5">
            <h2 class="text-base font-bold text-app-text mb-3">SOP Pustaka</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Ringkasan SOP</label>
                    <textarea name="library.sop_summary" rows="4" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">{{ $value('library.sop_summary') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Alur Layanan</label>
                    <textarea name="library.sop_flow" rows="4" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface" placeholder="Contoh: Cari buku di katalog, ambil di rak, bawa ke petugas, scan kartu anggota">{{ $value('library.sop_flow') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Link Dokumen SOP</label>
                    <input type="url" name="library.sop_document_url" value="{{ $value('library.sop_document_url') }}" placeholder="https://..." class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
            </div>
        </section>

        <section class="border-t border-app-border pt-5">
            <h2 class="text-base font-bold text-app-text mb-3">Sambutan Kepala Sekolah</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Nama Kepala Sekolah</label>
                    <input type="text" name="library.principal_name" value="{{ $value('library.principal_name') }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-app-text mb-1">Jabatan</label>
                    <input type="text" name="library.principal_title" value="{{ $value('library.principal_title', 'Kepala Sekolah') }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-app-text mb-1">Sambutan Singkat</label>
                    <textarea name="library.principal_speech" rows="4" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">{{ $value('library.principal_speech') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-app-text mb-1">Foto Kepala Sekolah</label>
                    <div class="flex flex-col sm:flex-row gap-4 sm:items-center">
                        @if($value('library.principal_photo'))
                            <img src="{{ Storage::disk(config('filesystems.default'))->url($value('library.principal_photo')) }}" alt="Foto kepala sekolah" class="w-24 h-24 rounded-md object-cover border border-app-border">
                        @else
                            <div class="w-24 h-24 rounded-md border border-dashed border-app-border flex items-center justify-center text-xs text-app-muted text-center px-2">Belum ada foto</div>
                        @endif
                        <input type="file" name="library.principal_photo" accept="image/*" class="block w-full text-sm text-app-muted file:mr-4 file:py-2 file:px-3 file:rounded-md file:border-0 file:bg-app-primary file:text-white file:font-semibold">
                    </div>
                    <p class="text-xs text-app-muted mt-2">Format gambar maksimal 2MB.</p>
                </div>
            </div>
        </section>

        <section class="border-t border-app-border pt-5">
            <h2 class="text-base font-bold text-app-text mb-3">Sistem</h2>
            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="system.auto_assign_barcode" value="1" {{ filter_var($value('system.auto_assign_barcode', true), FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                    <span class="text-sm font-semibold text-app-text">Auto barcode</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="system.auto_assign_qrcode" value="1" {{ filter_var($value('system.auto_assign_qrcode', true), FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                    <span class="text-sm font-semibold text-app-text">Auto QR code</span>
                </label>
            </div>
        </section>

        <div class="flex justify-end border-t border-app-border pt-5">
            <button type="submit" class="px-4 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">Simpan Pengaturan</button>
        </div>
    </form>
</div>
@endsection
