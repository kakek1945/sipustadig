@extends('layouts.dashboard')

@section('title', 'Data Denda')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-app-text">Data Denda</h1>
            <p class="text-app-muted mt-0.5">Kelola denda yang belum lunas, dibayar, dan dibebaskan</p>
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-app-surface border border-app-border rounded-md p-4">
            <p class="text-xs font-semibold text-app-muted">Belum Lunas</p>
            <p class="text-xl font-bold text-app-danger mt-1">{{ $stats['pending'] ?? 0 }}</p>
        </div>
        <div class="bg-app-surface border border-app-border rounded-md p-4">
            <p class="text-xs font-semibold text-app-muted">Lunas</p>
            <p class="text-xl font-bold text-app-success mt-1">{{ $stats['paid'] ?? 0 }}</p>
        </div>
        <div class="bg-app-surface border border-app-border rounded-md p-4">
            <p class="text-xs font-semibold text-app-muted">Dibebaskan</p>
            <p class="text-xl font-bold text-app-muted mt-1">{{ $stats['waived'] ?? 0 }}</p>
        </div>
        <div class="bg-app-surface border border-app-border rounded-md p-4">
            <p class="text-xs font-semibold text-app-muted">Total Tertagih</p>
            <p class="text-xl font-bold text-app-warning mt-1">Rp {{ number_format($stats['pending_amount'] ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-app-surface border border-app-border rounded-md p-4">
        <form method="GET" action="{{ route('fines.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs font-semibold text-app-text mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nomor denda, anggota, pinjam, buku..." class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
            </div>
            <div class="w-40">
                <label class="block text-xs font-semibold text-app-text mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="waived" {{ request('status') === 'waived' ? 'selected' : '' }}>Dibebaskan</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md transition-colors">Filter</button>
                <a href="{{ route('fines.index') }}" class="px-3 py-2 text-app-muted hover:text-app-text text-sm font-semibold">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-app-surface border border-app-border rounded-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-app-border-muted">
                <thead class="bg-app-background">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Denda</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Anggota</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Pinjam</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Jumlah</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-[10px] font-semibold text-app-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border-muted">
                    @forelse($fines as $fine)
                        <tr class="hover:bg-app-surface-subtle">
                            <td class="px-4 py-3">
                                <p class="font-mono text-sm font-semibold text-app-text">{{ $fine->fine_number }}</p>
                                <p class="text-xs text-app-muted">{{ $fine->late_days }} hari terlambat</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm font-semibold text-app-text">{{ $fine->member->name }}</p>
                                <p class="text-xs text-app-muted">{{ $fine->member->member_number }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-app-text">{{ $fine->loan?->loan_number }}</p>
                                <p class="text-xs text-app-muted">{{ $fine->loanItem?->bookCopy?->book?->title }}</p>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <p class="text-sm font-semibold text-app-text">Rp {{ number_format($fine->amount, 0, ',', '.') }}</p>
                                <p class="text-xs text-app-muted">Bayar Rp {{ number_format($fine->paid_amount, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($fine->status->value === 'paid') bg-app-success-surface text-app-success
                                    @elseif($fine->status->value === 'waived') bg-app-muted-light/20 text-app-muted
                                    @else bg-app-danger-surface text-app-danger @endif">
                                    {{ $fine->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-semibold space-x-3">
                                <a href="{{ route('fines.show', $fine->id) }}" class="text-app-primary hover:text-app-primary-hover">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-app-muted">Belum ada data denda</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($fines->hasPages())
            <div class="px-4 py-3 border-t border-app-border">
                {{ $fines->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
