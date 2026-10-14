<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profile['library_name'] }} - Sipustadig</title>
    <link rel="icon" type="image/png" href="{{ asset('images/sirkulib-logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    @php
        $lineItems = function (?string $text, array $fallback) {
            $items = collect(preg_split('/\r\n|\r|\n/', trim((string) $text)))
                ->map(fn ($item) => trim($item, " \t\n\r\0\x0B-•"))
                ->filter()
                ->values();

            return $items->isNotEmpty() ? $items : collect($fallback);
        };

        $missionItems = $lineItems($profile['mission'], [
            'Menyediakan layanan baca dan pinjam buku yang tertib, ramah, dan mudah diakses.',
            'Mendorong budaya literasi warga sekolah melalui koleksi dan layanan yang relevan.',
            'Mengelola data koleksi dan transaksi secara transparan melalui sistem digital.',
        ]);

        $goalItems = $lineItems($profile['goals'], [
            'Meningkatkan minat baca siswa dan guru.',
            'Memudahkan pencarian informasi koleksi perpustakaan.',
            'Mendukung kegiatan pembelajaran berbasis literasi.',
        ]);

        $sopFlowItems = $lineItems($profile['sop_flow'], [
            'Cari buku melalui katalog atau rak koleksi.',
            'Bawa buku dan kartu anggota ke petugas perpustakaan.',
            'Petugas memindai data anggota dan eksemplar untuk mencatat transaksi.',
            'Kembalikan buku sesuai tanggal jatuh tempo yang ditentukan.',
        ]);
    @endphp

    <div class="min-h-screen bg-app-background">
        <nav class="bg-app-surface shadow-[0_1px_0_rgba(27,31,36,0.04)] sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-14">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                        <div class="w-9 h-9 bg-white rounded-lg flex items-center justify-center p-1 border border-app-border shadow-[0_1px_0_rgba(27,31,36,0.04)]">
                            <x-brand-logo class="w-full h-full object-contain" />
                        </div>
                        <span class="text-lg font-bold text-app-text">Sipustadig</span>
                    </a>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('catalog.index') }}" class="text-sm text-app-muted hover:text-app-text font-medium">Katalog</a>
                        <a href="{{ route('login') }}" class="px-4 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md transition-colors">
                            Masuk
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                <div class="grid grid-cols-1 lg:grid-cols-[1.3fr_0.7fr] gap-8 lg:items-center">
                    <div>
                        <p class="text-sm font-semibold text-app-primary mb-3">Profil Pustaka Sekolah</p>
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-app-text leading-tight">
                            {{ $profile['library_name'] ?: 'Perpustakaan Sekolah' }}
                        </h1>
                        <p class="text-lg text-app-muted mt-3">
                            {{ $profile['school_name'] ?: 'Sekolah' }}
                            @if($profile['npsn'])
                                <span class="block text-sm mt-1">NSP/NPSN: {{ $profile['npsn'] }}</span>
                            @endif
                        </p>
                        <p class="text-base text-app-muted max-w-2xl mt-5">
                            {{ $profile['vision'] ?: 'Menjadi pusat literasi sekolah yang nyaman, tertib, dan mendukung pembelajaran sepanjang hayat.' }}
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 mt-7">
                            <a href="{{ route('catalog.index') }}" class="px-5 py-2.5 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md transition-colors text-center">
                                Jelajahi Katalog
                            </a>
                            <a href="{{ route('login') }}" class="px-5 py-2.5 bg-app-surface hover:bg-app-surface-subtle text-app-text text-sm font-semibold rounded-md border border-app-border transition-colors text-center">
                                Masuk Dashboard
                            </a>
                        </div>
                    </div>

                    <div class="bg-app-surface border border-app-border rounded-lg p-5 space-y-4">
                        <h2 class="text-base font-bold text-app-text">Informasi Layanan</h2>
                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-app-muted">Alamat</p>
                                <p class="font-semibold text-app-text">{{ $profile['address'] ?: 'Alamat perpustakaan belum diisi.' }}</p>
                            </div>
                            <div>
                                <p class="text-app-muted">Jam Layanan</p>
                                <p class="font-semibold text-app-text">{{ $profile['service_hours'] ?: 'Jam layanan belum diatur.' }}</p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <p class="text-app-muted">Kontak</p>
                                    <p class="font-semibold text-app-text">{{ $profile['phone'] ?: '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-app-muted">Email</p>
                                    <p class="font-semibold text-app-text break-all">{{ $profile['email'] ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-app-surface py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-8 items-start">
                        <div class="bg-app-background border border-app-border rounded-lg p-5 text-center">
                            @if($profile['principal_photo'])
                                <img src="{{ Storage::disk(config('filesystems.default'))->url($profile['principal_photo']) }}" alt="Foto kepala sekolah" class="w-36 h-36 rounded-full object-cover mx-auto border border-app-border">
                            @else
                                <div class="w-36 h-36 rounded-full bg-app-surface-subtle border border-app-border mx-auto flex items-center justify-center text-app-muted text-sm px-4">
                                    Foto Kepala Sekolah
                                </div>
                            @endif
                            <h2 class="text-lg font-bold text-app-text mt-4">{{ $profile['principal_name'] ?: 'Kepala Sekolah' }}</h2>
                            <p class="text-sm text-app-muted">{{ $profile['principal_title'] ?: 'Kepala Sekolah' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-app-primary mb-2">Sambutan</p>
                            <h2 class="text-2xl font-bold text-app-text">Sambutan Kepala Sekolah</h2>
                            <p class="text-app-muted mt-4 leading-relaxed">
                                {{ $profile['principal_speech'] ?: 'Selamat datang di layanan perpustakaan digital sekolah. Semoga sistem ini membantu siswa, guru, dan warga sekolah mengakses informasi koleksi dengan lebih mudah serta menumbuhkan budaya literasi yang positif.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-app-surface border border-app-border rounded-lg p-5">
                        <h2 class="text-lg font-bold text-app-text mb-3">Visi</h2>
                        <p class="text-sm text-app-muted leading-relaxed">{{ $profile['vision'] ?: 'Menjadi perpustakaan sekolah yang inspiratif, inklusif, dan mendukung pembelajaran sepanjang hayat.' }}</p>
                    </div>
                    <div class="bg-app-surface border border-app-border rounded-lg p-5">
                        <h2 class="text-lg font-bold text-app-text mb-3">Misi</h2>
                        <ul class="space-y-2 text-sm text-app-muted">
                            @foreach($missionItems as $item)
                                <li class="flex gap-2"><span class="text-app-primary font-bold">•</span><span>{{ $item }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="bg-app-surface border border-app-border rounded-lg p-5">
                        <h2 class="text-lg font-bold text-app-text mb-3">Tujuan</h2>
                        <ul class="space-y-2 text-sm text-app-muted">
                            @foreach($goalItems as $item)
                                <li class="flex gap-2"><span class="text-app-primary font-bold">•</span><span>{{ $item }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </section>

            <section class="bg-app-surface py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-[0.9fr_1.1fr] gap-8">
                        <div>
                            <p class="text-sm font-semibold text-app-primary mb-2">SOP Pustaka</p>
                            <h2 class="text-2xl font-bold text-app-text">Alur dan Aturan Layanan</h2>
                            <p class="text-app-muted mt-4 leading-relaxed">
                                {{ $profile['sop_summary'] ?: 'Layanan perpustakaan dilakukan secara tertib melalui pencarian katalog, pemilihan koleksi, pencatatan peminjaman, dan pengembalian sesuai jadwal.' }}
                            </p>
                            @if($profile['sop_document_url'])
                                <a href="{{ $profile['sop_document_url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex mt-5 px-4 py-2 bg-app-primary text-white text-sm font-semibold rounded-md hover:bg-app-primary-hover transition-colors">
                                    Buka Dokumen SOP
                                </a>
                            @endif
                        </div>
                        <div class="bg-app-background border border-app-border rounded-lg p-5">
                            <ol class="space-y-4">
                                @foreach($sopFlowItems as $index => $item)
                                    <li class="flex gap-3">
                                        <span class="w-7 h-7 rounded-full bg-app-primary text-white text-xs font-bold flex items-center justify-center shrink-0">{{ $index + 1 }}</span>
                                        <span class="text-sm text-app-text pt-1">{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-app-surface border border-app-border rounded-lg p-5">
                        <p class="text-xs font-semibold text-app-muted uppercase">Total Judul</p>
                        <p class="text-2xl font-bold text-app-text mt-2">{{ number_format($stats['total_titles']) }}</p>
                    </div>
                    <div class="bg-app-surface border border-app-border rounded-lg p-5">
                        <p class="text-xs font-semibold text-app-muted uppercase">Eksemplar Tersedia</p>
                        <p class="text-2xl font-bold text-app-success mt-2">{{ number_format($stats['available_copies']) }}</p>
                    </div>
                    <div class="bg-app-surface border border-app-border rounded-lg p-5">
                        <p class="text-xs font-semibold text-app-muted uppercase">Kategori</p>
                        <p class="text-2xl font-bold text-app-text mt-2">{{ number_format($stats['categories']) }}</p>
                    </div>
                    <div class="bg-app-surface border border-app-border rounded-lg p-5">
                        <p class="text-xs font-semibold text-app-muted uppercase">Buku Baru</p>
                        <p class="text-2xl font-bold text-app-primary mt-2">{{ number_format($stats['new_books']) }}</p>
                    </div>
                </div>
            </section>

            <section class="bg-app-surface py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
                        <div>
                            <p class="text-sm font-semibold text-app-primary mb-2">Katalog</p>
                            <h2 class="text-2xl font-bold text-app-text">Buku Terbaru</h2>
                        </div>
                        <a href="{{ route('catalog.index') }}" class="text-sm font-semibold text-app-primary hover:text-app-primary-hover">Lihat semua katalog</a>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @forelse($newBooks as $book)
                            <a href="{{ route('catalog.show', $book->id) }}" class="bg-app-background border border-app-border rounded-lg p-4 hover:border-app-primary transition-colors">
                                <p class="text-sm font-bold text-app-text line-clamp-2">{{ $book->title }}</p>
                                <p class="text-xs text-app-muted mt-2">{{ $book->author }}</p>
                                <p class="text-xs text-app-muted mt-1">{{ $book->category?->name ?? 'Tanpa Kategori' }}</p>
                            </a>
                        @empty
                            <div class="sm:col-span-2 lg:col-span-4 bg-app-background border border-app-border rounded-lg p-8 text-center text-sm text-app-muted">
                                Koleksi terbaru belum tersedia.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </main>

        <footer class="bg-app-surface-subtle py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-sm text-app-muted">&copy; {{ date('Y') }} {{ $profile['library_name'] ?: 'Sipustadig' }}. Didukung Sipustadig.</p>
            </div>
        </footer>
    </div>
</body>
</html>
