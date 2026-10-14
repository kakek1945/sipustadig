@extends('layouts.app')

@section('title', 'Login - Sipustadig')

@section('content')
<div class="min-h-screen flex">
    <!-- Left side - Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-app-topbar flex-col items-center justify-center p-12">
        <div class="text-center">
            <div class="w-24 h-24 bg-white rounded-xl flex items-center justify-center mx-auto mb-6 p-2 shadow-[0_4px_6px_-1px_rgba(27,31,36,0.1)]">
                <x-brand-logo class="w-full h-full object-contain" />
            </div>
            <h1 class="text-4xl font-bold text-white mb-3">Sipustadig</h1>
            <p class="text-lg text-app-topbar-muted mb-6">Sistem Informasi Perpustakaan Digital</p>
            <p class="text-app-topbar-muted max-w-md">Kelola koleksi buku, peminjaman, dan pengembalian dengan mudah dan efisien.</p>
        </div>
    </div>

    <!-- Right side - Login Form -->
    <div class="flex-1 flex items-center justify-center p-8 bg-app-background">
        <div class="w-full max-w-md">
            <!-- Mobile logo -->
            <div class="lg:hidden text-center mb-8">
                <div class="w-20 h-20 bg-white rounded-lg flex items-center justify-center mx-auto mb-4 p-2 shadow-[0_1px_0_rgba(27,31,36,0.04)] border border-app-border">
                    <x-brand-logo class="w-full h-full object-contain" />
                </div>
                <h1 class="text-2xl font-bold text-app-text">Sipustadig</h1>
            </div>

            <div class="bg-app-surface rounded-lg shadow-[0_1px_0_rgba(27,31,36,0.04),0_4px_6px_-1px_rgba(27,31,36,0.05)] p-8">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-app-text">Selamat Datang</h2>
                    <p class="text-app-muted mt-1">Silakan masuk dengan akun Anda</p>
                </div>

                @if ($errors->any())
                    <div class="mb-5 p-3 bg-app-danger-surface border border-app-danger/20 rounded-md">
                        <div class="flex items-center gap-2 text-app-danger text-sm">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">Email atau password salah</span>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-semibold text-app-text mb-1.5">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full pl-10 pr-4 py-2.5 border border-app-border rounded-md focus:outline-none focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary"
                                placeholder="nama@email.com">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-app-text mb-1.5">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input type="password" id="password" name="password" required
                                class="w-full pl-10 pr-4 py-2.5 border border-app-border rounded-md focus:outline-none focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-app-primary border-app-border rounded focus:ring-app-primary">
                            <span class="ml-2 text-sm text-app-muted">Ingat saya</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-app-primary hover:underline">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-app-primary hover:bg-app-primary-hover text-white font-semibold rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-app-primary focus:ring-offset-2">
                        Masuk
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-app-muted">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="font-medium text-app-primary hover:underline">
                            Daftar sekarang
                        </a>
                    </p>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-app-muted">
                &copy; {{ date('Y') }} Sipustadig. Hak cipta dilindungi.
            </p>
        </div>
    </div>
</div>
@endsection
