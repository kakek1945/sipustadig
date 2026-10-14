@extends('layouts.dashboard')

@section('title', 'Audit Log')

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-2xl font-bold text-app-text">Audit Log</h1>
        <p class="text-app-muted mt-0.5">Pantau perubahan dan aktivitas penting di sistem.</p>
    </div>

    <div class="bg-app-surface border border-app-border rounded-md p-4">
        <form method="GET" action="{{ route('audit-logs.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs font-semibold text-app-text mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="User, action, model, IP..." class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
            </div>
            <div class="w-40">
                <label class="block text-xs font-semibold text-app-text mb-1">Action</label>
                <select name="action" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                    <option value="">Semua</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <label class="block text-xs font-semibold text-app-text mb-1">Model</label>
                <select name="model_type" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
                    <option value="">Semua</option>
                    @foreach($modelTypes as $modelType)
                        <option value="{{ $modelType }}" {{ request('model_type') === $modelType ? 'selected' : '' }}>{{ $modelType }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-app-text mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface">
            </div>
            <button type="submit" class="px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">Filter</button>
            <a href="{{ route('audit-logs.index') }}" class="px-3 py-2 text-app-muted hover:text-app-text text-sm font-semibold">Reset</a>
        </form>
    </div>

    <div class="bg-app-surface border border-app-border rounded-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-app-border-muted">
                <thead class="bg-app-background">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Waktu</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">User</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Action</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Model</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">IP</th>
                        <th class="px-4 py-3 text-right text-[10px] font-semibold text-app-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border-muted">
                    @forelse($logs as $log)
                        <tr class="hover:bg-app-surface-subtle">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-app-muted">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-sm text-app-text">{{ $log->user?->name ?? 'System' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-app-info-surface text-app-primary">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-app-muted">{{ $log->model_type ?? '-' }} {{ $log->model_id ? '#'.$log->model_id : '' }}</td>
                            <td class="px-4 py-3 text-sm font-mono text-app-muted">{{ $log->ip_address ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('audit-logs.show', $log->id) }}" class="text-sm font-semibold text-app-primary hover:text-app-primary-hover">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-app-muted">Belum ada audit log</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-4 py-3 border-t border-app-border">{{ $logs->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
