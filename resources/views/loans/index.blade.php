@extends('layouts.dashboard')

@section('title', 'Peminjaman')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-app-text">Peminjaman</h1>
            <p class="text-app-muted mt-0.5">Pantau peminjaman aktif, terlambat, dan riwayat pengembalian</p>
        </div>
        <a href="{{ route('loans.create') }}" class="inline-flex items-center gap-1.5 px-3 py-[5px] bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Peminjaman Baru
        </a>
    </div>

    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    @if($errors->any())
        <x-alert type="error">{{ $errors->first() }}</x-alert>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-app-surface border border-app-border rounded-md p-4">
            <p class="text-xs font-semibold text-app-muted">Aktif</p>
            <p class="text-xl font-bold text-app-success mt-1">{{ $stats['active'] ?? 0 }}</p>
        </div>
        <div class="bg-app-surface border border-app-border rounded-md p-4">
            <p class="text-xs font-semibold text-app-muted">Terlambat</p>
            <p class="text-xl font-bold text-app-danger mt-1">{{ $stats['overdue'] ?? 0 }}</p>
        </div>
        <div class="bg-app-surface border border-app-border rounded-md p-4">
            <p class="text-xs font-semibold text-app-muted">Selesai</p>
            <p class="text-xl font-bold text-app-primary mt-1">{{ $stats['returned'] ?? 0 }}</p>
        </div>
        <div class="bg-app-surface border border-app-border rounded-md p-4">
            <p class="text-xs font-semibold text-app-muted">Eksemplar Tersedia</p>
            <p class="text-xl font-bold text-app-warning mt-1">{{ $stats['available_copies'] ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-app-surface border border-app-border rounded-md p-4">
        <form method="GET" action="{{ route('loans.index') }}" class="flex flex-wrap items-end gap-3">
            <input type="hidden" name="tab" value="{{ request('tab', 'active') }}">
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs font-semibold text-app-text mb-1">Cari</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="No. pinjam, anggota, buku, kode eksemplar..." class="w-full pl-9 pr-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                    <svg class="absolute left-2.5 top-2 w-4 h-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md transition-colors">
                    Filter
                </button>
                <a href="{{ route('loans.index', ['tab' => request('tab', 'active')]) }}" class="px-3 py-2 text-app-muted hover:text-app-text text-sm font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-app-surface border border-app-border rounded-md overflow-hidden">
        <div class="border-b border-app-border">
            <nav class="flex overflow-x-auto">
                @foreach(['active' => 'Aktif', 'returned' => 'Dikembalikan', 'overdue' => 'Terlambat', 'all' => 'Semua'] as $tab => $label)
                    <a href="{{ route('loans.index', ['tab' => $tab, 'search' => request('search')]) }}" class="px-4 py-3 text-sm font-semibold border-b-2 whitespace-nowrap {{ request('tab', 'active') === $tab ? 'border-app-primary text-app-primary' : 'border-transparent text-app-muted hover:text-app-text' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-app-border-muted">
                <thead class="bg-app-background">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">No. Pinjam</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Anggota</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Eksemplar</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-[10px] font-semibold text-app-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border-muted">
                    @forelse($loans as $loan)
                        @php
                            $status = $loan->status->value;
                            $isOverdue = $loan->isOverdue();
                        @endphp
                        <tr class="hover:bg-app-surface-subtle">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <p class="font-mono text-sm font-semibold text-app-text">{{ $loan->loan_number }}</p>
                                <p class="text-xs text-app-muted">{{ $loan->items_count }} buku</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-semibold text-app-text">{{ $loan->member->name }}</p>
                                <p class="text-xs text-app-muted">{{ $loan->member->member_number }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                    @foreach($loan->items->take(2) as $item)
                                        <p class="text-sm text-app-text">{{ $item->bookCopy?->book?->title ?? '-' }}</p>
                                        <p class="text-xs text-app-muted">{{ $item->bookCopy?->copy_code }}</p>
                                    @endforeach
                                    @if($loan->items->count() > 2)
                                        <p class="text-xs font-semibold text-app-primary">+{{ $loan->items->count() - 2 }} eksemplar lain</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-app-muted">
                                {{ $loan->loan_date->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <p class="text-sm {{ $isOverdue ? 'text-app-danger font-semibold' : 'text-app-muted' }}">{{ $loan->due_date->format('d M Y') }}</p>
                                @if($isOverdue)
                                    <p class="text-xs text-app-danger">{{ $loan->daysOverdue() }} hari terlambat</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($status === 'returned') bg-app-success-surface text-app-success
                                    @elseif($isOverdue || $status === 'overdue') bg-app-danger-surface text-app-danger
                                    @elseif($status === 'extended') bg-app-info-surface text-app-primary
                                    @else bg-app-warning-surface text-app-warning @endif">
                                    {{ $isOverdue && $status !== 'returned' ? 'Terlambat' : $loan->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-semibold">
                                <a href="{{ route('loans.show', $loan->id) }}" class="text-app-primary hover:text-app-primary-hover">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <p class="text-sm font-semibold text-app-text">Belum ada peminjaman</p>
                                <p class="text-sm text-app-muted mt-1">Peminjaman baru akan muncul di tabel ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($loans->hasPages())
            <div class="px-4 py-3 border-t border-app-border">
                {{ $loans->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
