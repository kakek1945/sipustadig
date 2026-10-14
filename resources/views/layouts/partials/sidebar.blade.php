<div class="flex flex-col h-full">
    <!-- Logo/Brand -->
    <div class="flex items-center h-12 px-3 border-b border-app-topbar/80">
        <button type="button" class="lg:hidden p-1.5 -ml-1.5 text-app-topbar-muted hover:text-white hover:bg-white/10 rounded-md mr-1.5" aria-label="Buka atau tutup navigasi" aria-controls="sidebar" :aria-expanded="sidebarOpen.toString()" @click="sidebarOpen = !sidebarOpen">
            <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <div class="w-7 h-7 bg-white rounded-md flex items-center justify-center p-1 shadow-[0_1px_0_rgba(27,31,36,0.08)]">
                <x-brand-logo class="w-full h-full object-contain" />
            </div>
            <div>
                <span class="text-sm font-bold text-white leading-none">Sipustadig</span>
                <span class="block text-[9px] text-app-topbar-muted leading-none mt-0.5">Perpustakaan Sekolah</span>
            </div>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-compact flex-1 px-2.5 py-2 space-y-0.5 overflow-y-auto" aria-label="Menu aplikasi">
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'bg-white text-app-text' : 'text-app-topbar-muted hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        @if(auth()->check() && auth()->user()->isStaff())
            {{-- Data Master --}}
            <div class="pt-4">
                <p class="px-3 text-[10px] font-semibold text-app-topbar-muted uppercase tracking-wider">Data Master</p>
            </div>
            <a href="{{ route('members.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('members.*') ? 'bg-white text-app-text' : 'text-app-topbar-muted hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Anggota
            </a>

            <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('categories.*') ? 'bg-white text-app-text' : 'text-app-topbar-muted hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Kategori
            </a>

            <a href="{{ route('books.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('books.*') ? 'bg-white text-app-text' : 'text-app-topbar-muted hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Buku
            </a>

            <a href="{{ route('book-copies.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('book-copies.*') ? 'bg-white text-app-text' : 'text-app-topbar-muted hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Eksemplar
            </a>

            {{-- Transaksi --}}
            <div class="pt-4">
                <p class="px-3 text-[10px] font-semibold text-app-topbar-muted uppercase tracking-wider">Transaksi</p>
            </div>
            <a href="{{ route('scan.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('scan.*') ? 'bg-white text-app-text' : 'text-app-topbar-muted hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7V5a2 2 0 012-2h2m10 0h2a2 2 0 012 2v2M3 17v2a2 2 0 002 2h2m10 0h2a2 2 0 002-2v-2M7 12h10"/>
                </svg>
                Scan Cepat
            </a>
            <a href="{{ route('loans.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('loans.*') ? 'bg-white text-app-text' : 'text-app-topbar-muted hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Peminjaman
            </a>
            <a href="{{ route('returns.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('returns.*') ? 'bg-white text-app-text' : 'text-app-topbar-muted hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
                Pengembalian
            </a>

            <a href="{{ route('fines.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('fines.*') ? 'bg-white text-app-text' : 'text-app-topbar-muted hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Denda
            </a>
        @endif

        {{-- Catalog (Public) --}}
        <div class="pt-4">
            <p class="px-3 text-[10px] font-semibold text-app-topbar-muted uppercase tracking-wider">Katalog</p>
        </div>
        <a href="{{ route('catalog.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('catalog.*') ? 'bg-white text-app-text' : 'text-app-topbar-muted hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Katalog Online
        </a>

        @if(auth()->check() && auth()->user()->isStaff())
            {{-- Laporan --}}
            <div class="pt-4">
                <p class="px-3 text-[10px] font-semibold text-app-topbar-muted uppercase tracking-wider">Laporan</p>
            </div>
            <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('reports.*') ? 'bg-white text-app-text' : 'text-app-topbar-muted hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Laporan
            </a>

            <a href="{{ route('import.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('import.*') ? 'bg-white text-app-text' : 'text-app-topbar-muted hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import Data
            </a>

            {{-- Pengaturan --}}
            <div class="pt-4">
                <p class="px-3 text-[10px] font-semibold text-app-topbar-muted uppercase tracking-wider">Pengaturan</p>
            </div>
            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('settings.*') ? 'bg-white text-app-text' : 'text-app-topbar-muted hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Pengaturan
            </a>
            <a href="{{ route('audit-logs.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('audit-logs.*') ? 'bg-white text-app-text' : 'text-app-topbar-muted hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Audit Log
            </a>
        @endif
    </nav>

    @auth
        <div class="p-2.5 border-t border-app-topbar/80">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-2 rounded-md px-2.5 py-2 text-xs font-semibold text-app-topbar-muted transition-colors hover:bg-white/10 hover:text-white">
                    <svg class="h-4 w-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    @endauth

</div>
