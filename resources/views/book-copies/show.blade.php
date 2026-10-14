@extends('layouts.dashboard')

@section('title', 'Detail Eksemplar')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('books.show', $copy->book_id) }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke detail buku
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Detail Eksemplar</h1>
            <p class="text-gray-500 font-mono text-sm">ID: {{ $copy->copy_code }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('book-copies.print-label', $copy->id) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 hover:border-gray-400 bg-white text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Label
            </a>
            @can('update', $copy)
                <a href="{{ route('book-copies.edit', $copy->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Book Summary Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:col-span-1 flex flex-col items-center text-center">
            <div class="w-32 h-44 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-center overflow-hidden mb-4 shadow-xs">
                @if($copy->book->cover_image)
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk(config('filesystems.uploads'))->url($copy->book->cover_image) }}" alt="{{ $copy->book->title }}" class="w-full h-full object-cover">
                @else
                    <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                @endif
            </div>
            <h3 class="font-bold text-gray-900 line-clamp-2" title="{{ $copy->book->title }}">{{ $copy->book->title }}</h3>
            <p class="text-xs text-gray-500 mt-1">Oleh: {{ $copy->book->author }}</p>
            <span class="mt-4 px-2.5 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-md">
                {{ $copy->book->category->name ?? 'Tanpa Kategori' }}
            </span>
            <div class="mt-4 text-xs text-gray-400 font-mono">
                Rak: {{ $copy->book->rack_location ?? '-' }}
            </div>
        </div>

        <!-- Copy Details Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:col-span-2 space-y-6">
            <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Informasi Eksemplar</h2>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="col-span-2 sm:col-span-1">
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Kode Eksemplar</p>
                    <p class="font-mono font-bold text-gray-900 text-base mt-0.5">{{ $copy->copy_code }}</p>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Status</p>
                    <div class="mt-0.5">
                        <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-full bg-{{ $copy->status->color() }}-100 text-{{ $copy->status->color() }}-800">
                            {{ $copy->status->label() }}
                        </span>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Barcode</p>
                    <p class="font-mono text-gray-800 mt-0.5">{{ $copy->barcode ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">QR Code</p>
                    <p class="font-mono text-gray-800 mt-0.5">{{ $copy->qr_code ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Kondisi Fisik</p>
                    <p class="font-semibold text-gray-900 mt-0.5">{{ $copy->condition ?? 'Baik' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Tanggal Pengadaan</p>
                    <p class="font-semibold text-gray-800 mt-0.5">
                        {{ $copy->acquisition_date ? $copy->acquisition_date->format('d M Y') : '-' }}
                    </p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Harga Pembelian</p>
                    <p class="font-bold text-gray-900 mt-0.5">
                        {{ $copy->purchase_price ? 'Rp ' . number_format($copy->purchase_price, 0, ',', '.') : '-' }}
                    </p>
                </div>
            </div>

            @if($copy->notes)
                <div class="pt-4 border-t border-gray-100">
                    <h4 class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Catatan</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $copy->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Active Loan Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">Status Peminjaman Aktif</h3>
        @if($copy->activeLoan())
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm">
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Anggota Peminjam</p>
                    <a href="{{ route('members.show', $copy->activeLoan()->loan->member_id) }}" class="inline-block font-bold text-indigo-600 hover:underline mt-1">
                        {{ $copy->activeLoan()->loan->member->name }}
                    </a>
                    <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $copy->activeLoan()->loan->member->member_number }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Tanggal Peminjaman</p>
                    <p class="font-semibold text-gray-900 mt-1">
                        {{ \Carbon\Carbon::parse($copy->activeLoan()->loan->loan_date)->format('d M Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Batas Pengembalian (Due Date)</p>
                    <p class="font-semibold text-red-600 mt-1">
                        {{ \Carbon\Carbon::parse($copy->activeLoan()->due_date)->format('d M Y') }}
                    </p>
                    @if(\Carbon\Carbon::parse($copy->activeLoan()->due_date)->isPast())
                        <span class="inline-flex px-2 py-0.5 text-3xs font-extrabold bg-red-100 text-red-800 rounded mt-1">TERLAMBAT</span>
                    @endif
                </div>
            </div>
        @else
            <div class="mt-4 flex items-center gap-3 text-sm text-gray-500">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Unit eksemplar ini tidak sedang dipinjam dan berada di rak perpustakaan.</span>
            </div>
        @endif
    </div>

    <!-- Danger Zone / Delete Copy -->
    @can('delete', $copy)
        <div class="bg-red-50 rounded-xl border border-red-200 p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h4 class="font-bold text-red-800 text-sm">Zona Bahaya (Hapus Eksemplar)</h4>
                <p class="text-xs text-red-700 mt-1 leading-relaxed">
                    Menghapus unit fisik ini akan menghapusnya secara permanen dari sistem. Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div>
                <form action="{{ route('book-copies.destroy', $copy->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus eksemplar ini secara permanen?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors flex-shrink-0">
                        Hapus Eksemplar
                    </button>
                </form>
            </div>
        </div>
    @endcan
</div>
@endsection
