@extends('layouts.dashboard')

@section('title', 'Tambah Buku')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <a href="{{ route('books.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-indigo-600 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke katalog
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Tambah Buku Baru</h1>
        <p class="text-gray-500">Lengkapi form di bawah untuk menambahkan buku baru ke dalam katalog perpustakaan</p>
    </div>

    <!-- Error Alert -->
    @if($errors->has('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            {{ $errors->first('error') }}
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Cover Image Upload -->
            <div class="flex flex-col sm:flex-row items-start gap-6 pb-6 border-b border-gray-100">
                <div class="w-32 h-44 bg-gray-50 rounded-lg border-2 border-gray-200 flex items-center justify-center overflow-hidden flex-shrink-0" id="cover-preview-container">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cover Buku</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition-colors cursor-pointer relative" id="cover-dropzone">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <div class="text-sm text-gray-600">
                                <label for="cover_image" class="relative cursor-pointer bg-white rounded-md font-semibold text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                    <span>Upload file</span>
                                    <input id="cover_image" name="cover_image" type="file" accept="image/*" class="sr-only" onchange="previewCover(this)">
                                </label>
                                <p class="pl-1 inline">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, JPEG hingga 2MB</p>
                        </div>
                    </div>
                    @error('cover_image')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Book Metadata -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Judul Buku <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror"
                        placeholder="Masukkan judul lengkap buku">
                    @error('title')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Author -->
                <div>
                    <label for="author" class="block text-sm font-semibold text-gray-700 mb-1">Pengarang <span class="text-red-500">*</span></label>
                    <input type="text" id="author" name="author" value="{{ old('author') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('author') border-red-500 @enderror"
                        placeholder="Nama pengarang">
                    @error('author')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select id="category_id" name="category_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('category_id') border-red-500 @enderror">
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ISBN -->
                <div>
                    <label for="isbn" class="block text-sm font-semibold text-gray-700 mb-1">ISBN</label>
                    <input type="text" id="isbn" name="isbn" value="{{ old('isbn') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('isbn') border-red-500 @enderror"
                        placeholder="ISBN-10 atau ISBN-13">
                    @error('isbn')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Publisher -->
                <div>
                    <label for="publisher" class="block text-sm font-semibold text-gray-700 mb-1">Penerbit</label>
                    <input type="text" id="publisher" name="publisher" value="{{ old('publisher') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Nama penerbit">
                </div>

                <!-- Publish Year -->
                <div>
                    <label for="publish_year" class="block text-sm font-semibold text-gray-700 mb-1">Tahun Terbit</label>
                    <input type="text" id="publish_year" name="publish_year" value="{{ old('publish_year', date('Y')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('publish_year') border-red-500 @enderror"
                        placeholder="Contoh: 2024">
                    @error('publish_year')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Edition -->
                <div>
                    <label for="edition" class="block text-sm font-semibold text-gray-700 mb-1">Edisi / Cetakan</label>
                    <input type="text" id="edition" name="edition" value="{{ old('edition') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Contoh: Cetakan ke-2">
                </div>

                <!-- Language -->
                <div>
                    <label for="language" class="block text-sm font-semibold text-gray-700 mb-1">Bahasa</label>
                    <input type="text" id="language" name="language" value="{{ old('language', 'Indonesian') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Bahasa buku">
                </div>

                <!-- Page Count -->
                <div>
                    <label for="page_count" class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Halaman</label>
                    <input type="number" id="page_count" name="page_count" value="{{ old('page_count') }}" min="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('page_count') border-red-500 @enderror"
                        placeholder="Halaman">
                    @error('page_count')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dimensions -->
                <div>
                    <label for="dimensions" class="block text-sm font-semibold text-gray-700 mb-1">Dimensi Buku</label>
                    <input type="text" id="dimensions" name="dimensions" value="{{ old('dimensions') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Contoh: 15cm x 23cm">
                </div>

                <!-- Weight -->
                <div>
                    <label for="weight" class="block text-sm font-semibold text-gray-700 mb-1">Berat (Kg)</label>
                    <input type="number" id="weight" name="weight" value="{{ old('weight') }}" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Contoh: 0.35">
                </div>

                <!-- Rack Location -->
                <div>
                    <label for="rack_location" class="block text-sm font-semibold text-gray-700 mb-1">Lokasi Rak</label>
                    <input type="text" id="rack_location" name="rack_location" value="{{ old('rack_location') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Contoh: RAK-01-A">
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-semibold text-gray-700 mb-1">Harga (Rp)</label>
                    <input type="number" id="price" name="price" value="{{ old('price') }}" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('price') border-red-500 @enderror"
                        placeholder="Harga pembelian buku">
                    @error('price')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Digital Book Metadata -->
            <div class="rounded-lg border border-indigo-100 bg-indigo-50/50 p-4 space-y-4">
                <label class="flex items-start gap-3">
                    <input type="hidden" name="is_digital" value="0">
                    <input type="checkbox" name="is_digital" value="1" {{ old('is_digital') ? 'checked' : '' }} class="mt-1 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span>
                        <span class="block text-sm font-semibold text-gray-900">Buku Digital</span>
                        <span class="block text-xs text-gray-600">Simpan metadata dan link resmi saja, bukan file PDF/audio/interaktif.</span>
                    </span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="digital_source" class="block text-sm font-semibold text-gray-700 mb-1">Sumber Digital</label>
                        <input type="text" id="digital_source" name="digital_source" value="{{ old('digital_source', 'SIBI/Kemendikdasmen') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="SIBI/Kemendikdasmen">
                    </div>
                    <div>
                        <label for="digital_url" class="block text-sm font-semibold text-gray-700 mb-1">Link Resmi</label>
                        <input type="url" id="digital_url" name="digital_url" value="{{ old('digital_url') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('digital_url') border-red-500 @enderror" placeholder="https://buku.kemendikdasmen.go.id/...">
                        @error('digital_url')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="digital_format" class="block text-sm font-semibold text-gray-700 mb-1">Format</label>
                        <input type="text" id="digital_format" name="digital_format" value="{{ old('digital_format') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="PDF, interaktif, audio, web">
                    </div>
                    <div>
                        <label for="digital_grade_level" class="block text-sm font-semibold text-gray-700 mb-1">Jenjang/Kelas</label>
                        <input type="text" id="digital_grade_level" name="digital_grade_level" value="{{ old('digital_grade_level') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="SD kelas 4, SMP, SMA">
                    </div>
                    <div class="md:col-span-2">
                        <label for="digital_access_note" class="block text-sm font-semibold text-gray-700 mb-1">Catatan Akses</label>
                        <textarea id="digital_access_note" name="digital_access_note" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Instruksi singkat untuk membuka sumber resmi">{{ old('digital_access_note') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Sinopsis / Ringkasan</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Sinopsis singkat mengenai buku...">{{ old('description') }}</textarea>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-1">Catatan Tambahan</label>
                <textarea id="notes" name="notes" rows="2"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Catatan tambahan mengenai buku...">{{ old('notes') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('books.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                    Simpan Buku
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewCover(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById('cover-preview-container');
                container.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Drag and drop
    const dropzone = document.getElementById('cover-dropzone');
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-indigo-400', 'bg-indigo-50');
    });
    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-indigo-400', 'bg-indigo-50');
    });
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-indigo-400', 'bg-indigo-50');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            document.getElementById('cover_image').files = e.dataTransfer.files;
            previewCover(document.getElementById('cover_image'));
        }
    });
</script>
@endpush
