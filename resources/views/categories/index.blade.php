@extends('layouts.dashboard')

@section('title', 'Kategori')

@section('content')
<div
    x-data="{
        open: false,
        editMode: false,
        categoryId: null,
        name: '',
        code: '',
        description: '',
        color: '#6366f1',
        openCategoryModal() {
            this.open = true;
            this.editMode = false;
            this.categoryId = null;
            this.name = '';
            this.code = '';
            this.description = '';
            this.color = '#6366f1';
        },
        editCategoryModal(category) {
            this.open = true;
            this.editMode = true;
            this.categoryId = category.id;
            this.name = category.name;
            this.code = category.code;
            this.description = category.description || '';
            this.color = category.color || '#6366f1';
        },
        closeCategoryModal() {
            this.open = false;
        }
    }"
    class="space-y-6"
>
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-app-text">Kategori</h1>
            <p class="text-app-muted">Kelola kategori buku</p>
        </div>
        <button type="button" @click="openCategoryModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Kategori
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-app-surface rounded-lg shadow-[0_1px_0_rgba(27,31,36,0.04)] border border-app-border p-4">
            <p class="text-sm font-semibold text-app-muted">Total Kategori</p>
            <p class="text-2xl font-bold text-app-text mt-1">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="bg-app-surface rounded-lg shadow-[0_1px_0_rgba(27,31,36,0.04)] border border-app-border p-4">
            <p class="text-sm font-semibold text-app-muted">Dengan Buku</p>
            <p class="text-2xl font-bold text-app-primary mt-1">{{ $stats['with_books'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-app-surface rounded-lg shadow-[0_1px_0_rgba(27,31,36,0.04)] border border-app-border p-4">
        <form method="GET" action="{{ route('categories.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori..." class="w-full pl-10 pr-4 py-2 border border-app-border rounded-md bg-app-surface text-app-text placeholder-app-muted focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            <button type="submit" class="px-4 py-2 bg-app-primary hover:bg-app-primary-hover text-white font-semibold rounded-md transition-colors">
                Cari
            </button>
        </form>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($categories as $category)
            <div class="bg-app-surface rounded-lg shadow-[0_1px_0_rgba(27,31,36,0.04)] border border-app-border p-6 hover:shadow-sm transition-shadow">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: {{ $category->color ?? '#6366f1' }}20">
                            <span class="w-6 h-6 rounded" style="background-color: {{ $category->color ?? '#6366f1' }}"></span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-app-text">{{ $category->name }}</h3>
                            <p class="text-sm text-app-muted">{{ $category->code }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <button type="button" @click="editCategoryModal(@js([
                            'id' => $category->id,
                            'name' => $category->name,
                            'code' => $category->code,
                            'description' => $category->description ?? '',
                            'color' => $category->color ?? '#6366f1',
                        ]))" class="p-2 text-app-muted hover:text-app-primary hover:bg-app-info-surface rounded-md" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Yakin hapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-app-muted hover:text-app-danger hover:bg-app-danger-surface rounded-md" title="Hapus">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @if($category->description)
                    <p class="mt-4 text-sm text-app-muted line-clamp-2">{{ $category->description }}</p>
                @endif
                <div class="mt-4 pt-4 border-t border-app-border-muted flex items-center justify-between">
                    <span class="text-xs text-app-muted-light">Sort: {{ $category->sort_order }}</span>
                    @if($category->books_count > 0)
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-app-info-surface text-app-primary">
                            {{ $category->books_count }} buku
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-app-surface rounded-lg shadow-[0_1px_0_rgba(27,31,36,0.04)] border border-app-border p-12 text-center">
                <svg class="w-16 h-16 text-app-border mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <h3 class="text-lg font-semibold text-app-text mb-1">Belum ada kategori</h3>
                <p class="text-app-muted">Mulai dengan menambahkan kategori baru</p>
                <button type="button" @click="openCategoryModal()" class="mt-4 px-4 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md transition-colors">
                    Tambah Kategori
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
        <div class="bg-app-surface rounded-lg shadow-[0_1px_0_rgba(27,31,36,0.04)] border border-app-border px-6 py-4">
            {{ $categories->withQueryString()->links() }}
        </div>
    @endif
    <!-- Category Modal -->
    <div x-cloak x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-app-topbar/45 p-4 sm:p-6" style="display: none;">
        <div class="bg-app-surface rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto border border-app-border" @click.stop>
            <form method="POST" :action="editMode ? '/categories/' + categoryId : '{{ route('categories.store') }}'">
                @csrf
                <template x-if="editMode">
                    @method('PUT')
                </template>
                <div class="px-5 py-4 border-b border-app-border-muted flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-app-text" x-text="editMode ? 'Edit Kategori' : 'Tambah Kategori'"></h3>
                        <p class="text-sm text-app-muted mt-0.5">Isi data kategori buku dengan ringkas dan konsisten.</p>
                    </div>
                    <button type="button" @click="closeCategoryModal()" class="p-2 text-app-muted hover:text-app-text hover:bg-app-surface-subtle rounded-md">
                        <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label for="modal-name" class="block text-sm font-semibold text-app-text mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                        <input type="text" id="modal-name" name="name" x-model="name" required
                            class="w-full px-3 py-2 border border-app-border rounded-md bg-app-surface text-app-text placeholder-app-muted focus:outline-none focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary @error('name') border-red-500 @enderror"
                            placeholder="Masukkan nama kategori">
                        @error('name')
                            <p class="mt-1 text-sm text-app-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="modal-code" class="block text-sm font-semibold text-app-text mb-1">Kode <span class="text-red-500">*</span></label>
                        <input type="text" id="modal-code" name="code" x-model="code" required
                            class="w-full px-3 py-2 border border-app-border rounded-md bg-app-surface text-app-text placeholder-app-muted focus:outline-none focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary @error('code') border-red-500 @enderror"
                            placeholder="Contoh: FIK, NFK">
                        @error('code')
                            <p class="mt-1 text-sm text-app-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="modal-description" class="block text-sm font-semibold text-app-text mb-1">Deskripsi</label>
                        <textarea id="modal-description" name="description" x-model="description" rows="2"
                            class="w-full px-3 py-2 border border-app-border rounded-md bg-app-surface text-app-text placeholder-app-muted focus:outline-none focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary"
                            placeholder="Deskripsi kategori (opsional)"></textarea>
                    </div>
                    <div>
                        <label for="modal-color" class="block text-sm font-semibold text-app-text mb-1">Warna</label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="color" name="color"
                                class="w-10 h-10 rounded-md cursor-pointer border border-app-border p-1 bg-app-surface">
                            <input type="text" x-model="color"
                                class="w-full px-3 py-2 border border-app-border rounded-md bg-app-surface text-app-text placeholder-app-muted focus:outline-none focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary"
                                placeholder="#6366f1">
                        </div>
                    </div>
                </div>
                <div class="px-5 py-4 bg-app-surface-subtle border-t border-app-border-muted flex justify-end gap-3 rounded-b-lg">
                    <button type="button" @click="closeCategoryModal()" class="px-4 py-2 text-sm font-semibold text-app-text hover:bg-app-surface rounded-md transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-semibold bg-app-primary hover:bg-app-primary-hover text-white rounded-md transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
