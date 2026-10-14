@extends('layouts.dashboard')

@section('title', 'Data Buku')

@section('content')
<div class="space-y-5">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-app-text">Data Buku</h1>
            <p class="text-app-muted mt-0.5">Kelola katalog buku perpustakaan</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('books.create') }}" class="inline-flex items-center gap-1.5 px-3 py-[5px] bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Buku
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-app-surface border border-app-border rounded-md p-4 shadow-[0_1px_0_rgba(27,31,36,0.04)]">
            <p class="text-xs font-semibold text-app-muted">Total Judul</p>
            <p class="text-xl font-bold text-app-text mt-1">{{ $stats['total_titles'] ?? 0 }}</p>
        </div>
        <div class="bg-app-surface border border-app-border rounded-md p-4 shadow-[0_1px_0_rgba(27,31,36,0.04)]">
            <p class="text-xs font-semibold text-app-muted">Total Eksemplar</p>
            <p class="text-xl font-bold text-app-primary mt-1">{{ $stats['total_copies'] ?? 0 }}</p>
        </div>
        <div class="bg-app-surface border border-app-border rounded-md p-4 shadow-[0_1px_0_rgba(27,31,36,0.04)]">
            <p class="text-xs font-semibold text-app-muted">Tersedia</p>
            <p class="text-xl font-bold text-app-success mt-1">{{ $stats['available_copies'] ?? 0 }}</p>
        </div>
        <div class="bg-app-surface border border-app-border rounded-md p-4 shadow-[0_1px_0_rgba(27,31,36,0.04)]">
            <p class="text-xs font-semibold text-app-muted">Dipinjam</p>
            <p class="text-xl font-bold text-app-warning mt-1">{{ $stats['borrowed_copies'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-app-surface border border-app-border rounded-md p-4">
        <form method="GET" action="{{ route('books.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-semibold text-app-text mb-1">Cari</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul, pengarang, ISBN..." class="w-full pl-9 pr-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                    <svg class="absolute left-2.5 top-2 w-4 h-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            <div class="w-48">
                <label class="block text-xs font-semibold text-app-text mb-1">Kategori</label>
                <select name="category_id" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-48">
                <label class="block text-xs font-semibold text-app-text mb-1">Jenis</label>
                <select name="type" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                    <option value="">Semua Jenis</option>
                    <option value="physical" {{ request('type') === 'physical' ? 'selected' : '' }}>Fisik</option>
                    <option value="digital" {{ request('type') === 'digital' ? 'selected' : '' }}>Digital</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md transition-colors">
                    Filter
                </button>
                <a href="{{ route('books.index') }}" class="px-3 py-2 text-app-muted hover:text-app-text text-sm font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-app-surface border border-app-border rounded-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-app-border-muted">
                <thead class="bg-app-background">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Buku</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Penerbit/Tahun</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Rak</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Eksemplar</th>
                        <th class="px-4 py-3 text-right text-[10px] font-semibold text-app-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border-muted">
                    @forelse($books as $book)
                        <tr class="hover:bg-app-surface-subtle">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-14 bg-app-surface-subtle rounded-md border border-app-border flex items-center justify-center overflow-hidden flex-shrink-0">
                                        @if($book->cover_image)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk(config('filesystems.uploads'))->url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-5 h-5 text-app-border" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="max-w-md truncate">
                                        <div class="flex flex-wrap items-center gap-1">
                                            <p class="text-sm font-semibold text-app-text truncate" title="{{ $book->title }}">{{ $book->title }}</p>
                                            @if($book->is_digital)
                                                <span class="inline-flex px-1.5 py-0.5 text-[10px] font-semibold rounded-full bg-app-info-surface text-app-primary">Digital</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-app-muted">Oleh: {{ $book->author }}</p>
                                        <p class="text-[10px] font-mono text-app-muted-light mt-0.5">ISBN: {{ $book->isbn ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-[10px] font-medium bg-app-surface-subtle text-app-muted rounded-full">
                                    {{ $book->category->name ?? 'Tanpa Kategori' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-app-muted">
                                <p class="text-app-text">{{ $book->publisher ?? '-' }}</p>
                                <p class="text-[10px] text-app-muted-light">{{ $book->publish_year ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-app-text font-mono">
                                @if($book->is_digital)
                                    <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold rounded-full bg-app-info-surface text-app-primary">Digital</span>
                                @else
                                    {{ $book->rack_location ?? '-' }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($book->is_digital)
                                    <span class="text-xs font-semibold text-app-primary">Buku digital</span>
                                @else
                                    <div class="text-[11px]">
                                        <span class="font-bold text-app-text">{{ $book->availableCopies() }}</span>
                                        <span class="text-app-border">/</span>
                                        <span class="text-app-muted">{{ $book->totalCopies() }}</span>
                                        <span class="ml-1 text-app-success font-medium">tersedia</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('books.show', $book->id) }}" class="p-1.5 text-app-muted hover:text-app-primary hover:bg-app-surface-subtle rounded-md transition-colors" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @can('update', $book)
                                        <a href="{{ route('books.edit', $book->id) }}" class="p-1.5 text-app-muted hover:text-app-primary hover:bg-app-surface-subtle rounded-md transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('delete', $book)
                                        <form action="{{ route('books.destroy', $book->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini? Semua eksemplar akan ikut terhapus.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-app-muted hover:text-app-danger hover:bg-app-surface-subtle rounded-md transition-colors" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-app-muted">
                                <svg class="mx-auto h-10 w-10 text-app-border mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <p class="text-sm font-semibold">Tidak Ada Buku Ditemukan</p>
                                <p class="text-xs mt-0.5">Coba sesuaikan kata kunci pencarian atau kategori Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($books->hasPages())
            <div class="px-4 py-3 bg-app-surface border-t border-app-border-muted">
                {{ $books->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection