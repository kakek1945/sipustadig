@extends('layouts.app')

@section('title', 'Katalog Perpustakaan')

@section('content')
<div class="min-h-screen bg-app-background">
    <header class="bg-app-surface border-b border-app-border sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('catalog.index') }}" class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-app-primary rounded-md flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-app-text">Sipustadig</span>
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">Masuk</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">
        <div>
            <h1 class="text-2xl font-bold text-app-text">Katalog Perpustakaan</h1>
            <p class="text-app-muted mt-0.5">Cari koleksi buku, cek ketersediaan, dan lokasi rak.</p>
        </div>

        <div class="bg-app-surface border border-app-border rounded-md p-4">
            <form method="GET" action="{{ route('catalog.index') }}" class="grid grid-cols-1 md:grid-cols-[1fr_200px_160px_180px_auto] gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-app-text mb-1">Cari</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Judul, pengarang, ISBN, penerbit..." class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-app-text mb-1">Kategori</label>
                    <select name="category" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                        <option value="">Semua</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-app-text mb-1">Jenis</label>
                    <select name="type" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                        <option value="">Semua</option>
                        <option value="physical" {{ request('type') === 'physical' ? 'selected' : '' }}>Fisik</option>
                        <option value="digital" {{ request('type') === 'digital' ? 'selected' : '' }}>Digital</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-app-text mb-1">Ketersediaan</label>
                    <select name="availability" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                        <option value="">Semua</option>
                        <option value="available" {{ request('availability') === 'available' ? 'selected' : '' }}>Tersedia</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">Cari</button>
                    <a href="{{ route('catalog.index') }}" class="px-3 py-2 text-app-muted hover:text-app-text text-sm font-semibold">Reset</a>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($books as $book)
                <a href="{{ route('catalog.show', $book->id) }}" class="bg-app-surface border border-app-border rounded-md overflow-hidden hover:border-app-primary transition-colors">
                    <div class="aspect-[3/4] bg-app-surface-subtle flex items-center justify-center">
                        @if($book->cover_image)
                            <img src="{{ Storage::disk(config('filesystems.default'))->url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-12 h-12 text-app-muted-light" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        @endif
                    </div>
                    <div class="p-4 space-y-2">
                        <div class="flex items-center gap-2">
                            @if($book->is_digital)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-app-info-surface text-app-primary">Digital</span>
                                <span class="text-xs text-app-muted">{{ $book->digital_source ?? 'SIBI/Kemendikdasmen' }}</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $book->available_copies > 0 ? 'bg-app-success-surface text-app-success' : 'bg-app-danger-surface text-app-danger' }}">
                                    {{ $book->available_copies > 0 ? 'Tersedia' : 'Tidak tersedia' }}
                                </span>
                                <span class="text-xs text-app-muted">{{ $book->available_copies }}/{{ $book->total_copies }}</span>
                            @endif
                        </div>
                        <h2 class="text-sm font-bold text-app-text line-clamp-2">{{ $book->title }}</h2>
                        <p class="text-xs text-app-muted">{{ $book->author }}</p>
                        <p class="text-xs text-app-muted">
                            {{ $book->category?->name ?? 'Tanpa Kategori' }} · {{ $book->is_digital ? ($book->digital_format ?? 'Digital') : ($book->rack_location ?? '-') }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="col-span-full bg-app-surface border border-app-border rounded-md p-10 text-center">
                    <p class="text-sm font-semibold text-app-text">Tidak ada buku ditemukan</p>
                    <p class="text-sm text-app-muted mt-1">Coba ubah kata kunci atau filter pencarian.</p>
                </div>
            @endforelse
        </div>

        @if($books->hasPages())
            <div class="flex justify-center">
                {{ $books->withQueryString()->links() }}
            </div>
        @endif
    </main>
</div>
@endsection
