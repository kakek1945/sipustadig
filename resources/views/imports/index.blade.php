@extends('layouts.dashboard')

@section('title', 'Import Data')

@section('content')
@php
    $importTypes = [
        'members' => ['label' => 'Anggota', 'desc' => 'Siswa, guru, dan staf anggota perpustakaan'],
        'books' => ['label' => 'Buku', 'desc' => 'Bibliografi, kategori, rak, dan metadata buku'],
        'copies' => ['label' => 'Eksemplar', 'desc' => 'Kode eksemplar, barcode, status, dan kondisi'],
    ];
    $result = session('import_result');
@endphp

<div class="max-w-6xl space-y-5">
    <div>
        <h1 class="text-2xl font-bold text-app-text">Import Data</h1>
        <p class="text-app-muted mt-0.5">Upload CSV, preview validasi, lalu proses data anggota, buku, atau eksemplar.</p>
    </div>

    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    @if($errors->any())
        <x-alert type="error">{{ $errors->first() }}</x-alert>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-5">
        <form method="POST" action="{{ route('import.process') }}" enctype="multipart/form-data" class="bg-app-surface border border-app-border rounded-md p-5 space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-app-text mb-2">Jenis import</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @foreach($importTypes as $type => $item)
                        <label class="border border-app-border rounded-md p-3 cursor-pointer hover:border-app-primary has-[:checked]:border-app-primary has-[:checked]:bg-app-info-surface">
                            <input type="radio" name="type" value="{{ $type }}" class="sr-only" {{ old('type', 'members') === $type ? 'checked' : '' }}>
                            <span class="block text-sm font-bold text-app-text">{{ $item['label'] }}</span>
                            <span class="block text-xs text-app-muted mt-1">{{ $item['desc'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-app-text mb-2">File CSV</label>
                <input type="file" name="file" accept=".csv,.txt,.xlsx" required class="block w-full text-sm text-app-muted file:mr-4 file:px-4 file:py-2 file:rounded-md file:border-0 file:bg-app-primary file:text-white file:font-semibold hover:file:bg-app-primary-hover">
                <p class="text-xs text-app-muted mt-2">Gunakan template CSV atau file XLSX dengan header yang sama. Ukuran maksimal 5MB.</p>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-app-border pt-5">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="dry_run" value="1" checked>
                    <span class="text-sm font-semibold text-app-text">Preview dulu tanpa menyimpan</span>
                </label>
                <button type="submit" class="px-4 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">
                    Jalankan Import
                </button>
            </div>
        </form>

        <aside class="bg-app-surface border border-app-border rounded-md p-5">
            <h2 class="text-base font-bold text-app-text">Template CSV</h2>
            <p class="text-sm text-app-muted mt-1">Download template, isi data, lalu upload kembali.</p>
            <div class="space-y-3 mt-4">
                @foreach($importTypes as $type => $item)
                    <a href="{{ route('import.template', $type) }}" class="flex items-center justify-between border border-app-border rounded-md px-3 py-2 hover:border-app-primary">
                        <span class="text-sm font-semibold text-app-text">{{ $item['label'] }}</span>
                        <span class="text-xs text-app-primary">Download</span>
                    </a>
                @endforeach
            </div>
        </aside>
    </div>

    @if($result)
        <div class="bg-app-surface border border-app-border rounded-md overflow-hidden">
            <div class="px-5 py-4 border-b border-app-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold text-app-text">Hasil Import</h2>
                    <p class="text-sm text-app-muted">{{ strtoupper($result['type']) }} - {{ $result['total'] }} baris, {{ $result['valid'] }} valid, {{ count($result['errors']) }} error</p>
                </div>
                @if($result['dry_run'] && empty($result['errors']))
                    <span class="text-xs font-semibold text-app-success">Preview valid</span>
                @endif
            </div>

            @if(!empty($result['preview']))
                <div class="p-5">
                    <p class="text-sm font-semibold text-app-text mb-3">Preview Data Valid</p>
                    <div class="overflow-x-auto border border-app-border rounded-md">
                        <table class="min-w-full divide-y divide-app-border-muted">
                            <tbody class="divide-y divide-app-border-muted">
                                @foreach($result['preview'] as $row)
                                    <tr>
                                        @foreach($row as $cell)
                                            <td class="px-4 py-3 text-sm text-app-text">{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(!empty($result['errors']))
                <div class="p-5 border-t border-app-border">
                    <p class="text-sm font-semibold text-app-danger mb-3">Error Report</p>
                    <div class="bg-app-danger-surface border border-app-danger/20 rounded-md p-3 max-h-72 overflow-y-auto">
                        <ul class="text-sm text-app-danger space-y-1">
                            @foreach($result['errors'] as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
