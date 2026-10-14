<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Sipustadig</title>
    <link rel="icon" type="image/png" href="{{ asset('images/sirkulib-logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-app-background">
        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-app-primary focus:px-3 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
            Lewati ke konten utama
        </a>
        <div class="flex h-screen">
            <!-- Sidebar -->
            <aside
                id="sidebar"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-30 w-56 bg-app-topbar transform transition-transform duration-300 lg:translate-x-0"
                aria-label="Navigasi utama"
            >
                @include('layouts.partials.sidebar')
            </aside>

            <!-- Mobile overlay -->
            <div
                x-cloak
                x-show="sidebarOpen"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-20 bg-black/45 lg:hidden"
                @click="sidebarOpen = false"
            ></div>

            <!-- Main content -->
            <div class="flex-1 flex flex-col lg:ml-56">
                <!-- Header -->
                <header class="bg-app-surface shadow-[0_1px_0_rgba(27,31,36,0.04)] sticky top-0 z-10">
                @include('layouts.partials.header')
                </header>

                <!-- Page content -->
                <main id="main-content" tabindex="-1" class="flex-1 p-4 md:p-6 overflow-auto">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
