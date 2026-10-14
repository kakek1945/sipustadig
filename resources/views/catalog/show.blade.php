@extends('layouts.app')

@section('title', $book->title . ' - Katalog Perpustakaan')

@section('content')
<div class="min-h-screen bg-app-background">
    <header class="bg-app-surface border-b border-app-border sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-app-muted hover:text-app-text">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Katalog
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">Masuk</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-5">
            <div class="bg-app-surface border border-app-border rounded-md p-5">
                <div class="aspect-[3/4] bg-app-surface-subtle rounded-md overflow-hidden flex items-center justify-center">
                    @if($book->cover_image)
                        <img src="{{ Storage::disk(config('filesystems.default'))->url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    @else
                        <svg class="w-16 h-16 text-app-muted-light" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    @endif
                </div>
                @if($book->is_digital)
                    <div class="mt-4 border border-app-info-surface rounded-md p-4 bg-app-info-surface/40">
                        <p class="text-xs text-app-muted">Koleksi Digital</p>
                        <p class="text-xl font-bold text-app-primary mt-1">Online</p>
                    </div>
                @else
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <div class="border border-app-border rounded-md p-3">
                            <p class="text-xs text-app-muted">Tersedia</p>
                            <p class="text-xl font-bold text-app-success">{{ $book->available_copies }}</p>
                        </div>
                        <div class="border border-app-border rounded-md p-3">
                            <p class="text-xs text-app-muted">Total</p>
                            <p class="text-xl font-bold text-app-text">{{ $book->total_copies }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-5">
                <div class="bg-app-surface border border-app-border rounded-md p-5">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-app-info-surface text-app-primary">{{ $book->category?->name ?? 'Tanpa Kategori' }}</span>
                        @if($book->is_digital)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-app-info-surface text-app-primary">Digital</span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $book->available_copies > 0 ? 'bg-app-success-surface text-app-success' : 'bg-app-danger-surface text-app-danger' }}">
                                {{ $book->available_copies > 0 ? 'Tersedia' : 'Tidak tersedia' }}
                            </span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-bold text-app-text">{{ $book->title }}</h1>
                    <p class="text-app-muted mt-1">{{ $book->author }}</p>

                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                        <div>
                            <p class="text-app-muted">ISBN</p>
                            <p class="font-semibold text-app-text">{{ $book->isbn ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-app-muted">Penerbit</p>
                            <p class="font-semibold text-app-text">{{ $book->publisher ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-app-muted">Tahun</p>
                            <p class="font-semibold text-app-text">{{ $book->publish_year ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-app-muted">{{ $book->is_digital ? 'Akses' : 'Rak' }}</p>
                            <p class="font-semibold text-app-text">{{ $book->is_digital ? ($book->digital_source ?? 'SIBI/Kemendikdasmen') : ($book->rack_location ?? '-') }}</p>
                        </div>
                        <div>
                            <p class="text-app-muted">Bahasa</p>
                            <p class="font-semibold text-app-text">{{ $book->language ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-app-muted">Halaman</p>
                            <p class="font-semibold text-app-text">{{ $book->page_count ?? '-' }}</p>
                        </div>
                    </div>

                    @if($book->description)
                        <div class="mt-5">
                            <p class="text-sm font-semibold text-app-text mb-1">Deskripsi</p>
                            <p class="text-sm text-app-muted leading-6">{{ $book->description }}</p>
                        </div>
                    @endif
                    @if($book->is_digital && $book->digital_url)
                        <div class="mt-5 rounded-md border border-app-info-surface bg-app-info-surface/40 p-4">
                            <p class="text-sm font-semibold text-app-text">Akses buku digital</p>
                            <p class="text-sm text-app-muted mt-1">Buku ini dibuka melalui sumber resmi eksternal dan tidak memiliki stok eksemplar fisik.</p>
                            <a href="{{ $book->digital_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex mt-3 px-4 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">
                                Buka di SIBI/Kemendikdasmen
                            </a>
                            @if($book->digital_access_note)
                                <p class="text-xs text-app-muted mt-2">{{ $book->digital_access_note }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                @if($book->is_digital)
                    <div class="bg-app-surface border border-app-border rounded-md p-5">
                        <p class="text-sm font-bold text-app-text">Koleksi Digital</p>
                        <p class="text-sm text-app-muted mt-1">Buku digital tidak masuk alur stok, peminjaman, pengembalian, atau denda.</p>
                    </div>
                @else
                    <div class="bg-app-surface border border-app-border rounded-md overflow-hidden">
                    <div class="px-5 py-4 border-b border-app-border">
                        <p class="text-sm font-bold text-app-text">Ketersediaan Eksemplar</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-app-border-muted">
                            <thead class="bg-app-background">
                                <tr>
                                    <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Kode</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Kondisi</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Lokasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-app-border-muted">
                                @forelse($book->copies as $copy)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-mono text-app-text">{{ $copy->copy_code }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                @if($copy->status->value === 'available') bg-app-success-surface text-app-success
                                                @elseif($copy->status->value === 'borrowed') bg-app-warning-surface text-app-warning
                                                @else bg-app-danger-surface text-app-danger @endif">
                                                {{ $copy->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-app-muted">{{ $copy->condition ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-app-muted">{{ $book->rack_location ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-12 text-center text-sm text-app-muted">Belum ada eksemplar untuk buku ini</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>
@endsection
