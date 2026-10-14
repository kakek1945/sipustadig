@extends('layouts.app')

@section('title', '403 - Akses Ditolak')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-app-background px-4 py-12">
    <div class="text-center">
        <div class="w-20 h-20 bg-app-warning-surface rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-app-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h1 class="text-5xl font-bold text-app-text mb-3">403</h1>
        <h2 class="text-xl font-semibold text-app-muted mb-3">Akses Ditolak</h2>
        <p class="text-app-muted mb-8 max-w-md mx-auto">Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.</p>
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