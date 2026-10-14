@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-5">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-app-text">Dashboard</h1>
            <p class="text-app-muted mt-0.5">Selamat datang kembali, {{ Auth::user()->name ?? 'User' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-app-muted">{{ now()->format('d F Y') }}</span>
        </div>
    </div>

    @if(($role?->value ?? '') === 'principal')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-app-surface border border-app-border rounded-md p-4">
                <p class="text-xs font-semibold text-app-muted">Tingkat Sirkulasi</p>
                <p class="text-2xl font-bold text-app-primary mt-1">{{ $principal_summary['circulation_rate'] ?? 0 }}%</p>
            </div>
            <div class="bg-app-surface border border-app-border rounded-md p-4">
                <p class="text-xs font-semibold text-app-muted">Ketersediaan Koleksi</p>
                <p class="text-2xl font-bold text-app-success mt-1">{{ $principal_summary['availability_rate'] ?? 0 }}%</p>
            </div>
            <div class="bg-app-surface border border-app-border rounded-md p-4">
                <p class="text-xs font-semibold text-app-muted">Risiko Terlambat</p>
                <p class="text-2xl font-bold text-app-danger mt-1">{{ $principal_summary['overdue_rate'] ?? 0 }}%</p>
            </div>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Members -->
        <div class="bg-app-surface border border-app-border rounded-md p-4 shadow-[0_1px_0_rgba(27,31,36,0.04)]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-app-muted">Total Anggota</p>
                    <p class="text-2xl font-bold text-app-text mt-1">{{ $stats['total_members'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-app-info-surface rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-app-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs">
                <span class="text-app-success flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                </span>
                <span class="text-app-muted ml-1.5">Aktif bulan ini</span>
            </div>
        </div>

        <!-- Total Books -->
        <div class="bg-app-surface border border-app-border rounded-md p-4 shadow-[0_1px_0_rgba(27,31,36,0.04)]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-app-muted">Total Buku</p>
                    <p class="text-2xl font-bold text-app-text mt-1">{{ $stats['total_books'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-app-info-surface rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-app-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs">
                <span class="text-app-muted">{{ $stats['available_copies'] ?? 0 }} tersedia dari {{ $stats['total_copies'] ?? 0 }} eksemplar</span>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="bg-app-surface border border-app-border rounded-md p-4 shadow-[0_1px_0_rgba(27,31,36,0.04)]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-app-muted">Peminjaman Aktif</p>
                    <p class="text-2xl font-bold text-app-text mt-1">{{ $stats['active_loans'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-app-warning-surface rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-app-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs">
                <span class="text-app-danger">{{ $stats['overdue_loans'] ?? 0 }} terlambat</span>
            </div>
        </div>

        <!-- Pending Fines -->
        <div class="bg-app-surface border border-app-border rounded-md p-4 shadow-[0_1px_0_rgba(27,31,36,0.04)]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-app-muted">Denda Tertunda</p>
                    <p class="text-xl font-bold text-app-text mt-1">Rp {{ number_format($stats['pending_fines'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 bg-app-danger-surface rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-app-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs">
                <span class="text-app-muted">{{ $stats['unpaid_fines_count'] ?? 0 }} belum lunas</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-app-surface border border-app-border rounded-md p-4 shadow-[0_1px_0_rgba(27,31,36,0.04)]">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-semibold text-app-text">Tren Peminjaman</h2>
                <span class="text-xs text-app-muted">7 hari</span>
            </div>
            <canvas id="loanTrendChart" height="160" data-labels='@json($loan_trend["labels"] ?? [])' data-values='@json($loan_trend["values"] ?? [])'></canvas>
        </div>

        <div class="bg-app-surface border border-app-border rounded-md p-4 shadow-[0_1px_0_rgba(27,31,36,0.04)]">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-semibold text-app-text">Sebaran Kategori</h2>
                <span class="text-xs text-app-muted">Top 6</span>
            </div>
            <canvas id="categoryChart" height="160" data-labels='@json($category_distribution["labels"] ?? [])' data-values='@json($category_distribution["values"] ?? [])'></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Recent Loans -->
        <div class="bg-app-surface border border-app-border rounded-md shadow-[0_1px_0_rgba(27,31,36,0.04)]">
            <div class="px-4 py-3 border-b border-app-border-muted flex items-center justify-between">
                <h2 class="text-base font-semibold text-app-text">Peminjaman Terbaru</h2>
                <a href="{{ route('loans.index') }}" class="text-xs text-app-primary hover:underline">Lihat semua</a>
            </div>
            <div class="divide-y divide-app-border-muted">
                @forelse($recent_loans ?? [] as $loan)
                    <div class="p-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-app-surface-subtle rounded-full flex items-center justify-center">
                                <span class="text-xs font-medium text-app-muted">{{ substr($loan->member->name ?? 'M', 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-app-text">{{ $loan->member->name ?? 'Member' }}</p>
                                <p class="text-xs text-app-muted">{{ $loan->items->first()?->bookCopy?->book?->title ?? 'Buku' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full
                                @if($loan->status->value === 'active') bg-app-success-surface text-app-success
                                @elseif($loan->status->value === 'returned') bg-app-surface-subtle text-app-muted
                                @else bg-app-danger-surface text-app-danger @endif">
                                {{ $loan->status->label() }}
                            </span>
                            <p class="text-xs text-app-muted mt-0.5">{{ $loan->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-app-muted text-sm">
                        <p>Belum ada data peminjaman</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Popular Books -->
        <div class="bg-app-surface border border-app-border rounded-md shadow-[0_1px_0_rgba(27,31,36,0.04)]">
            <div class="px-4 py-3 border-b border-app-border-muted flex items-center justify-between">
                <h2 class="text-base font-semibold text-app-text">Buku Populer</h2>
                <a href="{{ route('catalog.index') }}" class="text-xs text-app-primary hover:underline">Lihat katalog</a>
            </div>
            <div class="divide-y divide-app-border-muted">
                @forelse($popular_books ?? [] as $book)
                    <div class="p-3 flex items-center gap-3">
                        <div class="w-10 h-14 bg-app-surface-subtle rounded flex items-center justify-center flex-shrink-0">
                            @if($book->cover_image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover rounded">
                            @else
                                <svg class="w-5 h-5 text-app-border" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-app-text truncate">{{ $book->title }}</p>
                            <p class="text-xs text-app-muted">{{ $book->author ?? 'Unknown' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-app-text">{{ $book->loan_count ?? 0 }}x</p>
                            <p class="text-xs text-app-muted">dipinjam</p>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-app-muted text-sm">
                        <p>Belum ada data buku</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function drawBarChart(canvasId, color) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        const labels = JSON.parse(canvas.dataset.labels || '[]');
        const values = JSON.parse(canvas.dataset.values || '[]');
        const context = canvas.getContext('2d');
        const width = canvas.clientWidth;
        const height = canvas.height;
        const ratio = window.devicePixelRatio || 1;

        canvas.width = width * ratio;
        canvas.height = height * ratio;
        context.scale(ratio, ratio);
        context.clearRect(0, 0, width, height);

        const max = Math.max(...values, 1);
        const gap = 10;
        const chartHeight = height - 34;
        const barWidth = values.length ? (width - gap * (values.length + 1)) / values.length : 0;

        context.font = '11px Inter, sans-serif';
        context.textAlign = 'center';

        values.forEach((value, index) => {
            const barHeight = Math.max(4, (value / max) * (chartHeight - 20));
            const x = gap + index * (barWidth + gap);
            const y = chartHeight - barHeight;

            context.fillStyle = color;
            context.fillRect(x, y, barWidth, barHeight);
            context.fillStyle = '#57606a';
            context.fillText(labels[index] ?? '', x + barWidth / 2, height - 12, Math.max(32, barWidth + gap));
        });
    }

    window.addEventListener('load', () => {
        drawBarChart('loanTrendChart', '#0969da');
        drawBarChart('categoryChart', '#1f883d');
    });
</script>
@endpush
