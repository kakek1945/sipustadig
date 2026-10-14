@extends('layouts.app')

@section('title', '500 - Server Error')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-app-background px-4 py-12">
    <div class="text-center">
        <div class="w-20 h-20 bg-app-danger-surface rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-app-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h1 class="text-5xl font-bold text-app-text mb-3">500</h1>
        <h2 class="text-xl font-semibold text-app-muted mb-3">Terjadi Kesalahan Server</h2>
        <p class="text-app-muted mb-8 max-w-md mx-auto">Maaf, terjadi kesalahan pada server. Silakan coba lagi nanti atau hubungi administrator.</p>
        <div class="flex items-center justify-center gap-3">
            <a href="{{ url()->previous() }}" class="px-5 py-2 bg-app-surface hover:bg-app-surface-subtle text-app-text font-medium rounded-md border border-app-border transition-colors text-sm">
                Kembali
            </a>
            <a href="{{ route('dashboard') }}" class="px-5 py-2 bg-app-primary hover:bg-app-primary-hover text-white font-medium rounded-md transition-colors text-sm">
                Ke Dashboard
            </a>
        </div>
    </div>
</div>
@endsection