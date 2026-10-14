@extends('layouts.dashboard')

@section('title', 'Laporan')

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-2xl font-bold text-app-text">Laporan</h1>
        <p class="text-app-muted mt-0.5">Pilih jenis laporan untuk melihat, memfilter, atau mengunduh data.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($reportTypes as $type => $label)
            <div class="bg-app-surface border border-app-border rounded-md p-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-base font-bold text-app-text">{{ $label }}</h2>
                        <p class="text-sm text-app-muted mt-1">Laporan {{ strtolower($label) }} siap filter dan export CSV.</p>
                    </div>
                    <div class="w-10 h-10 bg-app-info-surface rounded-md flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-app-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-4">
                    <a href="{{ route('reports.show', $type) }}" class="px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">Lihat</a>
                    <a href="{{ route('reports.export', ['type' => $type, 'format' => 'csv']) }}" class="px-3 py-2 bg-app-surface-subtle hover:bg-app-border-muted text-app-text text-sm font-semibold rounded-md">CSV</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
