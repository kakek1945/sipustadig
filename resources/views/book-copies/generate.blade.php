@extends('layouts.dashboard')

@section('title', 'Generate Massal Eksemplar')

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
        <h1 class="text-2xl font-bold text-gray-900">Generate Massal Eksemplar</h1>
        <p class="text-gray-500">Buat banyak unit fisik (eksemplar) buku sekaligus dengan kode berurutan otomatis</p>
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

            <!-- Bulk Count -->
            <div>
                <label for="bulk_count" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Eksemplar <span class="text-red-500">*</span></label>
                <input type="number" id="bulk_count" name="bulk_count" value="{{ old('bulk_count', 5) }}" min="1" max="100" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('bulk_count') border-red-500 @enderror"
                    placeholder="Masukkan jumlah eksemplar yang ingin dibuat">
                <p class="mt-1 text-xs text-gray-500">Maksimal 100 eksemplar per pembuatan massal.</p>
                @error('bulk_count')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Condition -->
                <div>
                    <label for="condition" class="block text-sm font-semibold text-gray-700 mb-1">Kondisi Fisik <span class="text-red-500">*</span></label>
                    <input type="text" id="condition" name="condition" value="{{ old('condition', 'Baik') }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('condition') border-red-500 @enderror"
                        placeholder="Contoh: Baik">
                    @error('condition')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Acquisition Date -->
                <div>
                    <label for="acquisition_date" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pengadaan</label>
                    <input type="date" id="acquisition_date" name="acquisition_date" value="{{ old('acquisition_date', date('Y-m-d')) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Purchase Price (Individual cost) -->
            <div>
                <label for="purchase_price" class="block text-sm font-semibold text-gray-700 mb-1">Harga Beli Per Unit (Rp)</label>
                <input type="number" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" min="0"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Masukkan harga pembelian per eksemplar">
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                <textarea id="notes" name="notes" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Catatan untuk seluruh eksemplar ini...">{{ old('notes') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('book-copies.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                    Generate Eksemplar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
