@extends('layouts.dashboard')

@section('title', 'Detail Peminjaman')

@section('content')
@php
    $status = $loan->status->value;
    $isOverdue = $loan->isOverdue();
@endphp

<div class="max-w-5xl space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-app-text">Detail Peminjaman</h1>
            <p class="text-app-muted mt-0.5">No. <span class="font-mono font-semibold">{{ $loan->loan_number }}</span></p>
        </div>
        <a href="{{ route('loans.index') }}" class="px-3 py-2 text-app-muted hover:text-app-text text-sm font-semibold">
            Kembali
        </a>
    </div>

    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    @if($errors->any())
        <x-alert type="error">{{ $errors->first() }}</x-alert>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-5">
        <div class="space-y-5">
            <div class="bg-app-surface border border-app-border rounded-md p-5">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold text-app-muted uppercase">Status</p>
                        <p class="text-lg font-bold text-app-text mt-1">{{ $isOverdue && $status !== 'returned' ? 'Terlambat' : $loan->status->label() }}</p>
                    </div>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        @if($status === 'returned') bg-app-success-surface text-app-success
                        @elseif($isOverdue || $status === 'overdue') bg-app-danger-surface text-app-danger
                        @elseif($status === 'extended') bg-app-info-surface text-app-primary
                        @else bg-app-warning-surface text-app-warning @endif">
                        {{ $isOverdue && $status !== 'returned' ? $loan->daysOverdue() . ' hari terlambat' : $loan->status->label() }}
                    </span>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                    <div class="border border-app-border rounded-md p-3">
                        <p class="text-app-muted">Dipinjam</p>
                        <p class="font-semibold text-app-text mt-1">{{ $loan->loan_date->format('d M Y') }}</p>
                    </div>
                    <div class="border border-app-border rounded-md p-3">
                        <p class="text-app-muted">Jatuh Tempo</p>
                        <p class="font-semibold {{ $isOverdue ? 'text-app-danger' : 'text-app-text' }} mt-1">{{ $loan->due_date->format('d M Y') }}</p>
                    </div>
                    <div class="border border-app-border rounded-md p-3">
                        <p class="text-app-muted">Dikembalikan</p>
                        <p class="font-semibold text-app-text mt-1">{{ $loan->return_date?->format('d M Y') ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-app-surface border border-app-border rounded-md overflow-hidden">
                <div class="px-5 py-4 border-b border-app-border">
                    <h2 class="text-sm font-bold text-app-text">Eksemplar Dipinjam</h2>
                </div>
                <div class="divide-y divide-app-border-muted">
                    @foreach($loan->items as $item)
                        <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-app-text">{{ $item->bookCopy?->book?->title ?? '-' }}</p>
                                <p class="text-xs text-app-muted">{{ $item->bookCopy?->copy_code }} - {{ $item->bookCopy?->book?->author }}</p>
                            </div>
                            <div class="text-sm sm:text-right">
                                <p class="font-semibold {{ $item->isOverdue() ? 'text-app-danger' : 'text-app-text' }}">{{ ucfirst($item->status) }}</p>
                                <p class="text-xs text-app-muted">Tempo {{ $item->due_date->format('d M Y') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <aside class="space-y-5">
            <div class="bg-app-surface border border-app-border rounded-md p-5">
                <p class="text-xs font-semibold text-app-muted uppercase">Anggota</p>
                <p class="text-base font-bold text-app-text mt-2">{{ $loan->member->name }}</p>
                <p class="text-sm text-app-muted">{{ $loan->member->member_number }}</p>
                <p class="text-sm text-app-muted">{{ $loan->member->email }}</p>
            </div>

            <div class="bg-app-surface border border-app-border rounded-md p-5">
                <p class="text-xs font-semibold text-app-muted uppercase">Diproses oleh</p>
                <p class="text-sm font-semibold text-app-text mt-2">{{ $loan->processedBy?->name ?? '-' }}</p>
                <p class="text-xs text-app-muted">{{ $loan->created_at->format('d M Y H:i') }}</p>
            </div>

            @if($loan->notes)
                <div class="bg-app-surface border border-app-border rounded-md p-5">
                    <p class="text-xs font-semibold text-app-muted uppercase">Catatan</p>
                    <p class="text-sm text-app-text mt-2 whitespace-pre-line">{{ $loan->notes }}</p>
                </div>
            @endif

            @if($loan->canExtend())
                <form method="POST" action="{{ route('loans.extend', $loan->id) }}" class="bg-app-surface border border-app-border rounded-md p-5 space-y-3">
                    @csrf
                    <p class="text-xs font-semibold text-app-muted uppercase">Perpanjangan</p>
                    <div>
                        <label class="block text-sm font-semibold text-app-text mb-1">Tambah hari</label>
                        <input type="number" name="days" min="1" max="30" value="{{ (int) app('settings')->get('loan.max_borrow_days', 7) }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-app-text mb-1">Catatan</label>
                        <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary" placeholder="Opsional"></textarea>
                    </div>
                    <button type="submit" class="w-full px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">
                        Perpanjang
                    </button>
                </form>
            @endif
        </aside>
    </div>
</div>
@endsection
