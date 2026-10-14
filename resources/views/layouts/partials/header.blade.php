<div x-data="{ open: false, notificationsOpen: false }" class="flex items-center justify-between h-16 px-4 md:px-6">
    <!-- Left side - Page title -->
    <div class="flex items-center gap-4">
        <h1 class="text-lg md:text-xl font-semibold text-app-text">@yield('title', 'Dashboard')</h1>
    </div>

    <!-- Right side -->
    <div class="flex items-center gap-2 md:gap-4">
        <!-- Search (hidden on mobile) -->
        <div class="hidden lg:flex items-center">
            <div class="relative">
                <input type="text" aria-label="Cari cepat" placeholder="Cari..." class="w-64 pl-10 pr-4 py-2 text-sm border border-app-border rounded-md bg-app-surface text-app-text placeholder-app-muted focus:outline-none focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-app-muted" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        <!-- Notifications -->
        <div class="relative">
            <button type="button" aria-label="Buka notifikasi" :aria-expanded="notificationsOpen.toString()" @click="notificationsOpen = !notificationsOpen; open = false" @click.away="notificationsOpen = false" class="relative p-2 text-app-muted hover:text-app-text hover:bg-app-surface-subtle rounded-md">
                <svg class="w-5 h-5 md:w-6 md:h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="absolute top-1 right-1 w-2 h-2 bg-app-danger rounded-full" aria-hidden="true"></span>
            </button>
            <div x-cloak x-show="notificationsOpen" x-transition class="absolute right-0 z-50 w-80 mt-2 bg-app-surface rounded-lg shadow-[0_4px_6px_-1px_rgba(27,31,36,0.1)] border border-app-border">
                <div class="p-4 border-b border-app-border-muted">
                    <h3 class="font-semibold text-app-text">Notifikasi</h3>
                </div>
                <div class="max-h-64 overflow-y-auto">
                    <div class="p-4 text-center text-app-muted text-sm">
                        Tidak ada notifikasi baru
                    </div>
                </div>
            </div>
        </div>

        <!-- User dropdown -->
        <div class="relative">
            <button type="button" aria-label="Buka menu pengguna" :aria-expanded="open.toString()" @click="open = !open; notificationsOpen = false" @click.away="open = false" class="flex items-center gap-2 md:gap-3 p-2 hover:bg-app-surface-subtle rounded-md">
                <div class="hidden md:block text-right">
                    <p class="text-sm font-medium text-app-text">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-xs text-app-muted">{{ auth()->user()->role->label() ?? '' }}</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 bg-app-primary rounded-full flex items-center justify-center overflow-hidden">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-white text-sm md:text-base font-semibold">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                    @endif
                </div>
            </button>
            <div x-cloak x-show="open" x-transition class="absolute right-0 z-50 w-56 mt-2 bg-app-surface rounded-lg shadow-[0_4px_6px_-1px_rgba(27,31,36,0.1)] border border-app-border">
                <div class="py-2">
                    <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-app-text hover:bg-app-surface-subtle">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profil Saya
                    </a>
                    <hr class="my-2 border-app-border">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-app-danger hover:bg-app-surface-subtle">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
