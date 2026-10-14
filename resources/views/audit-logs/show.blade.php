@extends('layouts.dashboard')

@section('title', 'Detail Audit Log')

@section('content')
<div class="max-w-4xl space-y-5">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-app-text">Detail Audit Log</h1>
            <p class="text-app-muted mt-0.5">{{ $log->created_at->format('d M Y H:i:s') }}</p>
        </div>
        <a href="{{ route('audit-logs.index') }}" class="px-3 py-2 text-app-muted hover:text-app-text text-sm font-semibold">Kembali</a>
    </div>

    <div class="bg-app-surface border border-app-border rounded-md p-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-app-muted">User</p>
                <p class="font-semibold text-app-text">{{ $log->user?->name ?? 'System' }}</p>
                <p class="text-app-muted">{{ $log->user?->email }}</p>
            </div>
            <div>
                <p class="text-app-muted">Action</p>
                <p class="font-semibold text-app-text">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</p>
            </div>
            <div>
                <p class="text-app-muted">Model</p>
                <p class="font-semibold text-app-text">{{ $log->model_type ?? '-' }} {{ $log->model_id ? '#'.$log->model_id : '' }}</p>
            </div>
            <div>
                <p class="text-app-muted">IP Address</p>
                <p class="font-mono text-app-text">{{ $log->ip_address ?? '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-app-muted">User Agent</p>
                <p class="text-app-text break-all">{{ $log->user_agent ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-app-surface border border-app-border rounded-md p-5">
            <p class="text-sm font-bold text-app-text mb-3">Old Values</p>
            <pre class="bg-app-background border border-app-border rounded-md p-3 text-xs overflow-x-auto">{{ json_encode($log->old_values ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        <div class="bg-app-surface border border-app-border rounded-md p-5">
            <p class="text-sm font-bold text-app-text mb-3">New Values</p>
            <pre class="bg-app-background border border-app-border rounded-md p-3 text-xs overflow-x-auto">{{ json_encode($log->new_values ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
</div>
@endsection
