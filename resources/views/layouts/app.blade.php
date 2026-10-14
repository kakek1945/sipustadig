<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sipustadig') - Sistem Perpustakaan</title>
    <link rel="icon" type="image/png" href="{{ asset('images/sirkulib-logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-app-background">
        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-app-primary focus:px-3 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
            Lewati ke konten utama
        </a>
        <main id="main-content" tabindex="-1">
            @yield('content')
        </main>
    </div>
</body>
</html>
