@extends('layouts.dashboard')

@section('title', 'Tambah Eksemplar')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <a href="{{ route('book-copies.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke daftar
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Tambah Eksemplar Baru</h1>
        <p class="text-gray-500">Tambahkan salinan fisik baru untuk buku terdaftar</p>
    </div>

    <!-- Error Alert -->
    @if($errors->has('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            {{ $errors->first('error') }}
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('book-copies.store') }}" class="space-y-6">
            @csrf

            <!-- Book Selection -->
            <div>
                <label for="book_id" class="block text-sm font-semibold text-gray-700 mb-1">Buku <span class="text-red-500">*</span></label>
                <select id="book_id" name="book_id" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('book_id') border-red-500 @enderror">
                    <option value="">Pilih Buku</option>
                    @foreach($books as $book)
                        <option value="{{ $book->id }}" {{ old('book_id', $selectedBookId) == $book->id ? 'selected' : '' }}>
                            {{ $book->title }} ({{ $book->author }})
                        </option>
                    @endforeach
                </select>
                @error('book_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Copy Code -->
            <div>
                <label for="copy_code" class="block text-sm font-semibold text-gray-700 mb-1">Kode Eksemplar</label>
                <input type="text" id="copy_code" name="copy_code" value="{{ old('copy_code') }}"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('copy_code') border-red-500 @enderror"
                    placeholder="Contoh: KOD-YYMMDD-001 (Otomatis jika kosong)">
                <p class="mt-1 text-xs text-gray-500">Kosongkan agar sistem men-generate kode otomatis secara unik.</p>
                @error('copy_code')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Barcode & QR Code (Optional override) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="barcode" class="block text-sm font-semibold text-gray-700 mb-1">Barcode (Opsional)</label>
                    <input type="text" id="barcode" name="barcode" value="{{ old('barcode') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Samakan dengan kode jika kosong">
                </div>
                <div>
                    <label for="qr_code" class="block text-sm font-semibold text-gray-700 mb-1">QR Code (Opsional)</label>
                    <input type="text" id="qr_code" name="qr_code" value="{{ old('qr_code') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Samakan dengan kode jika kosong">
                </div>
            </div>

            <!-- Status & Condition -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ old('status') == $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="condition" class="block text-sm font-semibold text-gray-700 mb-1">Kondisi Fisik <span class="text-red-500">*</span></label>
                    <input type="text" id="condition" name="condition" value="{{ old('condition', 'Baik') }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('condition') border-red-500 @enderror"
                        placeholder="Contoh: Baik, Rusak Ringan, Kertas Menguning">
                    @error('condition')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Acquisition Date & Purchase Price -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="acquisition_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pengadaan</label>
                    <input type="date" id="acquisition_date" name="acquisition_date" value="{{ old('acquisition_date', date('Y-m-d')) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('acquisition_date') border-red-500 @enderror">
                    @error('acquisition_date')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="purchase_price" class="block text-sm font-semibold text-gray-700 mb-1">Harga Beli (Rp)</label>
                    <input type="number" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" min="0"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('purchase_price') border-red-500 @enderror"
                        placeholder="Masukkan harga pembelian per unit">
                    @error('purchase_price')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                <textarea id="notes" name="notes" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Catatan khusus mengenai unit eksemplar ini...">{{ old('notes') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('book-copies.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                    Simpan Eksemplar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
