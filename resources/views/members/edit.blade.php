@extends('layouts.dashboard')

@section('title', 'Edit Anggota')

@section('content')
<div class="max-w-3xl">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('members.show', $member) }}" class="p-2 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Anggota</h1>
                <p class="text-gray-500">{{ $member->name }}</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('members.update', $member) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Photo & Basic Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h3>

            <div class="flex flex-col md:flex-row gap-6">
                <!-- Photo -->
                <div class="flex-shrink-0">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto</label>
                    <div class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center cursor-pointer hover:border-indigo-500 transition-colors overflow-hidden" id="photo-preview-container">
                        @if($member->photo)
                            <img id="photo-preview" src="{{ \Illuminate\Support\Facades\Storage::disk(config('filesystems.uploads'))->url($member->photo) }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                        @else
                            <div id="photo-placeholder" class="text-center">
                                <svg class="w-8 h-8 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-xs text-gray-500 mt-1">Upload</p>
                            </div>
                            <img id="photo-preview" src="#" alt="Preview" class="w-full h-full object-cover hidden">
                        @endif
                    </div>
                    <input type="file" name="photo" id="photo" accept="image/*" class="hidden">
                    <p class="text-xs text-gray-500 mt-1">Klik untuk ganti foto</p>
                </div>

                <!-- Fields -->
                <div class="flex-1 space-y-4">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $member->name) }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                            placeholder="Masukkan nama lengkap">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $member->email) }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror"
                            placeholder="nama@email.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Member Type & ID -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tipe & Identitas</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Anggota <span class="text-red-500">*</span></label>
                    <select id="type" name="type" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('type') border-red-500 @enderror">
                        <option value="student" {{ old('type', $member->type->value) == 'student' ? 'selected' : '' }}>Siswa</option>
                        <option value="teacher" {{ old('type', $member->type->value) == 'teacher' ? 'selected' : '' }}>Guru</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <select id="gender" name="gender"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Pilih</option>
                        <option value="male" {{ old('gender', $member->gender?->value) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $member->gender?->value) == 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- NISN (for student) -->
                <div id="nisn-field">
                    <label for="nisn" class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
                    <input type="text" id="nisn" name="nisn" value="{{ old('nisn', $member->nisn) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="10 digit">
                </div>

                <!-- Employee Number (for teacher) -->
                <div id="employee-number-field" class="{{ $member->type->value === 'teacher' ? '' : 'hidden' }}">
                    <label for="employee_number" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                    <input type="text" id="employee_number" name="employee_number" value="{{ old('employee_number', $member->employee_number) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="NIP">
                </div>

                <!-- Class (for student) -->
                <div id="class-field">
                    <label for="class" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <select id="class" name="class"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Pilih kelas</option>
                        <option value="X" {{ old('class', $member->class) == 'X' ? 'selected' : '' }}>X</option>
                        <option value="XI" {{ old('class', $member->class) == 'XI' ? 'selected' : '' }}>XI</option>
                        <option value="XII" {{ old('class', $member->class) == 'XII' ? 'selected' : '' }}>XII</option>
                    </select>
                </div>

                <!-- Year Level -->
                <div id="year-level-field">
                    <label for="year_level" class="block text-sm font-medium text-gray-700 mb-1">Tingkat</label>
                    <select id="year_level" name="year_level"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Pilih tingkat</option>
                        @for($i = 10; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('year_level', $member->year_level) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="active" {{ old('status', $member->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $member->status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="blocked" {{ old('status', $member->status) == 'blocked' ? 'selected' : '' }}>Terblokir</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Tambahan</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $member->phone) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="08xxxxxxxxxx">
                </div>

                <!-- Birth Date -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $member->birth_date?->toDateString()) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Birth Place -->
                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" id="birth_place" name="birth_place" value="{{ old('birth_place', $member->birth_place) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Kota kelahiran">
                </div>

                <!-- Join Date -->
                <div>
                    <label for="join_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bergabung</label>
                    <input type="date" id="join_date" name="join_date" value="{{ old('join_date', $member->join_date->toDateString()) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Address -->
            <div class="mt-4">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea id="address" name="address" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Masukkan alamat lengkap">{{ old('address', $member->address) }}</textarea>
            </div>

            <!-- Notes -->
            <div class="mt-4">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea id="notes" name="notes" rows="2"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Catatan tambahan (opsional)">{{ old('notes', $member->notes) }}</textarea>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('members.show', $member) }}" class="px-6 py-2.5 text-gray-700 font-medium hover:bg-gray-100 rounded-lg transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const nisnField = document.getElementById('nisn-field');
        const employeeNumberField = document.getElementById('employee-number-field');
        const classField = document.getElementById('class-field');
        const yearLevelField = document.getElementById('year-level-field');
        const photoPreviewContainer = document.getElementById('photo-preview-container');
        const photoInput = document.getElementById('photo');
        const photoPreview = document.getElementById('photo-preview');
        const photoPlaceholder = document.getElementById('photo-placeholder');

        function toggleFields() {
            const isStudent = typeSelect.value === 'student';
            const isTeacher = typeSelect.value === 'teacher';

            nisnField.classList.toggle('hidden', !isStudent);
            employeeNumberField.classList.toggle('hidden', !isTeacher);
            classField.classList.toggle('hidden', !isStudent);
            yearLevelField.classList.toggle('hidden', !isStudent);
        }

        // Photo preview
        photoPreviewContainer.addEventListener('click', function() {
            photoInput.click();
        });

        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                    photoPreview.classList.remove('hidden');
                    if (photoPlaceholder) photoPlaceholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        typeSelect.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>
@endpush