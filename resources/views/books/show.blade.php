@extends('layouts.dashboard')

@section('title', 'Detail Buku')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('books.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke katalog
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Detail Buku</h1>
        </div>
        <div class="flex items-center gap-3">
            @can('update', $book)
                <a href="{{ route('books.edit', $book->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Buku
                </a>
            @endcan
        </div>
    </div>

    <!-- Success Alerts -->
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Book Info Panel -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <!-- Cover Image -->
                <div class="w-full h-80 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-center overflow-hidden mb-6">
                    @if($book->cover_image)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk(config('filesystems.uploads'))->url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="text-center p-6">
                            <svg class="w-20 h-20 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="text-sm text-gray-400">Tidak ada cover</span>
                        </div>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <h2 class="text-xl font-bold text-gray-900">{{ $book->title }}</h2>
                    @if($book->is_digital)
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700">Digital</span>
                    @endif
                </div>
                <p class="text-sm font-medium text-gray-500 mb-6">Oleh: {{ $book->author }}</p>

                <!-- Spec Details -->
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">ISBN</span>
                        <span class="font-semibold text-gray-900">{{ $book->isbn ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Kategori</span>
                        <span class="font-semibold text-indigo-600">{{ $book->category->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Penerbit</span>
                        <span class="font-semibold text-gray-900">{{ $book->publisher ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Tahun Terbit</span>
                        <span class="font-semibold text-gray-900">{{ $book->publish_year ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Edisi / Cetakan</span>
                        <span class="font-semibold text-gray-900">{{ $book->edition ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Bahasa</span>
                        <span class="font-semibold text-gray-900">{{ $book->language ?? 'Indonesian' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Halaman</span>
                        <span class="font-semibold text-gray-900">{{ $book->page_count ?? '-' }} hlm</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Dimensi / Berat</span>
                        <span class="font-semibold text-gray-900">
                            {{ $book->dimensions ?? '-' }} @if($book->weight) ({{ $book->weight }} Kg) @endif
                        </span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Lokasi Rak</span>
                        <span class="font-mono font-semibold text-gray-900 bg-gray-50 px-2 py-0.5 rounded">{{ $book->rack_location ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Harga Buku</span>
                        <span class="font-semibold text-gray-900">
                            {{ $book->price ? 'Rp ' . number_format($book->price, 0, ',', '.') : '-' }}
                        </span>
                    </div>
                    @if($book->is_digital)
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-500">Sumber Digital</span>
                            <span class="font-semibold text-gray-900">{{ $book->digital_source ?? 'SIBI/Kemendikdasmen' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-500">Format</span>
                            <span class="font-semibold text-gray-900">{{ $book->digital_format ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-500">Jenjang/Kelas</span>
                            <span class="font-semibold text-gray-900">{{ $book->digital_grade_level ?? '-' }}</span>
                        </div>
                    @endif
                </div>
            </div>

            @if($book->is_digital && $book->digital_url)
                <div class="bg-white rounded-xl shadow-sm border border-indigo-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Akses Buku Digital</h3>
                    <p class="text-sm text-gray-600 mb-4">Buku ini tersedia melalui sumber resmi eksternal dan tidak memiliki eksemplar fisik untuk dipinjam.</p>
                    <a href="{{ $book->digital_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg">
                        Buka di SIBI/Kemendikdasmen
                    </a>
                    @if($book->digital_access_note)
                        <p class="mt-3 text-xs text-gray-500">{{ $book->digital_access_note }}</p>
                    @endif
                </div>
            @endif

            @if($book->description)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Sinopsis / Ringkasan</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $book->description }}</p>
                </div>
            @endif

            <!-- Notes -->
            @if($book->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Catatan Internal</h3>
                    <p class="text-sm text-gray-500 italic">{{ $book->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Copies Panel -->
        @if($book->is_digital)
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-indigo-100 p-6">
                    <h3 class="font-bold text-gray-900 mb-2">Koleksi Digital</h3>
                    <p class="text-sm text-gray-600">Buku digital tidak masuk alur stok eksemplar, peminjaman, pengembalian, atau denda. Gunakan link resmi untuk membuka bahan bacaan.</p>
                </div>
            </div>
        @else
            <div class="lg:col-span-2 space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
                    <p class="text-3xl font-bold text-gray-900">{{ $book->totalCopies() }}</p>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-1">Total Eksemplar</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $book->availableCopies() }}</p>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-1">Tersedia</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-center">
                    <p class="text-3xl font-bold text-amber-600">{{ $book->borrowedCopies() }}</p>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-1">Dipinjam</p>
                </div>
            </div>

            <!-- Copies Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-gray-50">
                    <h3 class="font-bold text-gray-900">Daftar Eksemplar (Fisik)</h3>
                    @can('create', App\Models\BookCopy::class)
                        <div class="flex items-center gap-2">
                            <a href="{{ route('book-copies.generate', ['book_id' => $book->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-indigo-200 hover:border-indigo-300 bg-white text-indigo-600 hover:bg-indigo-50 text-xs font-semibold rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                Generate Massal
                            </a>
                            <a href="{{ route('book-copies.create', ['book_id' => $book->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tambah Eksemplar
                            </a>
                        </div>
                    @endcan
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($book->copies as $copy)
                                <tr class="hover:bg-gray-50 text-sm">
                                    <td class="px-6 py-4 whitespace-nowrap font-mono font-semibold text-gray-900">
                                        {{ $copy->copy_code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $copy->status->color() }}-100 text-{{ $copy->status->color() }}-800">
                                            {{ $copy->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        {{ $copy->condition ?? 'Baik' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        @if($copy->activeLoan())
                                            <a href="{{ route('members.show', $copy->activeLoan()->loan->member_id) }}" class="text-indigo-600 hover:underline">
                                                {{ $copy->activeLoan()->loan->member->name }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('book-copies.show', $copy->id) }}" class="p-1 text-gray-500 hover:text-indigo-600 hover:bg-gray-100 rounded-md transition-colors" title="Detail Eksemplar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            @can('update', $copy)
                                                <a href="{{ route('book-copies.edit', $copy->id) }}" class="p-1 text-gray-500 hover:text-indigo-600 hover:bg-gray-100 rounded-md transition-colors" title="Edit Eksemplar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                            @endcan
                                            <a href="{{ route('book-copies.print-label', $copy->id) }}" target="_blank" class="p-1 text-gray-500 hover:text-indigo-600 hover:bg-gray-100 rounded-md transition-colors" title="Cetak QR/Barcode Label">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                </svg>
                                            </a>
                                            @can('delete', $copy)
                                                <form action="{{ route('book-copies.destroy', $copy->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus eksemplar ini?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1 text-gray-500 hover:text-red-600 hover:bg-gray-100 rounded-md transition-colors" title="Hapus Eksemplar">
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
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <span class="text-sm">Belum ada eksemplar terdaftar untuk buku ini.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
        @endif
    </div>
</div>
@endsection
