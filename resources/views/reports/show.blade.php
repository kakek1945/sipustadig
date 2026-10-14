@extends('layouts.dashboard')

@section('title', $reportTypes[$type] ?? 'Laporan')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-app-text">{{ $reportTypes[$type] ?? 'Laporan' }}</h1>
            <p class="text-app-muted mt-0.5">Filter data dan unduh hasil laporan.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.index') }}" class="px-3 py-2 text-app-muted hover:text-app-text text-sm font-semibold">Kembali</a>
            <a href="{{ route('reports.export', array_merge(request()->query(), ['type' => $type, 'format' => 'csv'])) }}" class="px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">Export CSV</a>
        </div>
    </div>

    <div class="bg-app-surface border border-app-border rounded-md p-4">
        <form method="GET" action="{{ route('reports.show', $type) }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-semibold text-app-text mb-1">Tanggal mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
            </div>
            <div>
                <label class="block text-xs font-semibold text-app-text mb-1">Tanggal selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
            </div>
            <div>
                <label class="block text-xs font-semibold text-app-text mb-1">Tahun</label>
                <input type="number" name="year" value="{{ request('year', now()->year) }}" class="w-28 px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
            </div>
            <button type="submit" class="px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">Filter</button>
            <a href="{{ route('reports.show', $type) }}" class="px-3 py-2 text-app-muted hover:text-app-text text-sm font-semibold">Reset</a>
        </form>
    </div>

    <div class="bg-app-surface border border-app-border rounded-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-app-border-muted">
                <thead class="bg-app-background">
                    <tr>
                        @foreach($report['columns'] as $column)
                            <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border-muted">
                    @forelse($report['rows'] as $row)
                        <tr>
                            @foreach($row as $cell)
                                <td class="px-4 py-3 text-sm text-app-text">{{ is_float($cell) ? number_format($cell, 0, ',', '.') : $cell }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($report['columns']) }}" class="px-4 py-12 text-center text-sm text-app-muted">Tidak ada data untuk filter ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
