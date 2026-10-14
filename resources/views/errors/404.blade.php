@extends('layouts.app')

@section('title', '404 - Halaman Tidak Ditemukan')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-app-background px-4 py-12">
    <div class="text-center">
        <div class="w-20 h-20 bg-app-info-surface rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-app-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="text-5xl font-bold text-app-text mb-3">404</h1>
        <h2 class="text-xl font-semibold text-app-muted mb-3">Halaman Tidak Ditemukan</h2>
        <p class="text-app-muted mb-8 max-w-md mx-auto">Maaf, halaman yang Anda cari tidak tersedia atau telah dipindahkan.</p>
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