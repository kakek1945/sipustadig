@extends('layouts.dashboard')

@section('title', 'Detail Denda')

@section('content')
<div class="max-w-4xl space-y-5">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-app-text">Detail Denda</h1>
            <p class="text-app-muted mt-0.5 font-mono">{{ $fine->fine_number }}</p>
        </div>
        <a href="{{ route('fines.index') }}" class="px-3 py-2 text-app-muted hover:text-app-text text-sm font-semibold">Kembali</a>
    </div>

    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-5">
        <div class="space-y-5">
            <div class="bg-app-surface border border-app-border rounded-md p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-app-muted uppercase">Status</p>
                        <p class="text-lg font-bold text-app-text mt-1">{{ $fine->status->label() }}</p>
                    </div>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        @if($fine->status->value === 'paid') bg-app-success-surface text-app-success
                        @elseif($fine->status->value === 'waived') bg-app-muted-light/20 text-app-muted
                        @else bg-app-danger-surface text-app-danger @endif">
                        {{ $fine->status->label() }}
                    </span>
                </div>

                <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="border border-app-border rounded-md p-3">
                        <p class="text-app-muted">Anggota</p>
                        <p class="font-semibold text-app-text mt-1">{{ $fine->member->name }}</p>
                    </div>
                    <div class="border border-app-border rounded-md p-3">
                        <p class="text-app-muted">Pinjam</p>
                        <p class="font-semibold text-app-text mt-1">{{ $fine->loan?->loan_number }}</p>
                    </div>
                    <div class="border border-app-border rounded-md p-3">
                        <p class="text-app-muted">Hari terlambat</p>
                        <p class="font-semibold text-app-text mt-1">{{ $fine->late_days }}</p>
                    </div>
                    <div class="border border-app-border rounded-md p-3">
                        <p class="text-app-muted">Jumlah</p>
                        <p class="font-semibold text-app-text mt-1">Rp {{ number_format($fine->amount, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            @if($fine->loanItem)
                <div class="bg-app-surface border border-app-border rounded-md p-5">
                    <p class="text-sm font-bold text-app-text mb-3">Eksemplar</p>
                    <p class="text-sm text-app-text">{{ $fine->loanItem->bookCopy?->book?->title }}</p>
                    <p class="text-xs text-app-muted">{{ $fine->loanItem->bookCopy?->copy_code }}</p>
                </div>
            @endif
        </div>

        <aside class="space-y-5">
            <div class="bg-app-surface border border-app-border rounded-md p-5">
                <p class="text-xs font-semibold text-app-muted uppercase">Pembayaran</p>
                <p class="text-sm text-app-text mt-2">Bayar: Rp {{ number_format($fine->paid_amount, 0, ',', '.') }}</p>
                <p class="text-sm text-app-text">Sisa: Rp {{ number_format($fine->remainingAmount(), 0, ',', '.') }}</p>
                <p class="text-xs text-app-muted mt-2">{{ $fine->paid_date?->format('d M Y') ?? 'Belum dibayar' }}</p>
            </div>

            @if($fine->waiver_reason)
                <div class="bg-app-surface border border-app-border rounded-md p-5">
                    <p class="text-xs font-semibold text-app-muted uppercase">Alasan Bebas</p>
                    <p class="text-sm text-app-text mt-2 whitespace-pre-line">{{ $fine->waiver_reason }}</p>
                </div>
            @endif

            @if($fine->status->value === 'pending')
                <form method="POST" action="{{ route('fines.pay', $fine->id) }}" class="bg-app-surface border border-app-border rounded-md p-5 space-y-3">
                    @csrf
                    <p class="text-sm font-semibold text-app-text">Bayar denda</p>
                    <input type="number" name="amount" min="1" step="1" value="{{ $fine->remainingAmount() }}" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                    <button type="submit" class="w-full px-3 py-2 bg-app-success hover:opacity-90 text-white text-sm font-semibold rounded-md">Bayar Sekarang</button>
                </form>

                <form method="POST" action="{{ route('fines.waive', $fine->id) }}" class="bg-app-surface border border-app-border rounded-md p-5 space-y-3">
                    @csrf
                    <p class="text-sm font-semibold text-app-text">Bebaskan denda</p>
                    <textarea name="reason" rows="3" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary" placeholder="Wajib alasan"></textarea>
                    <button type="submit" class="w-full px-3 py-2 bg-app-muted hover:opacity-90 text-white text-sm font-semibold rounded-md">Bebaskan</button>
                </form>
            @endif
        </aside>
    </div>
</div>
@endsection
