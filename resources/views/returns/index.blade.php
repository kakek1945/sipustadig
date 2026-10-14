@extends('layouts.dashboard')

@section('title', 'Pengembalian')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-app-text">Pengembalian</h1>
            <p class="text-app-muted mt-0.5">Scan eksemplar yang dikembalikan dan sistem akan menghitung keterlambatan</p>
        </div>
    </div>

    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    @if($errors->any())
        <x-alert type="error">{{ $errors->first() }}</x-alert>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-app-surface border border-app-border rounded-md p-4">
            <p class="text-xs font-semibold text-app-muted">Dipinjam</p>
            <p class="text-xl font-bold text-app-text mt-1">{{ $stats['borrowed'] ?? 0 }}</p>
        </div>
        <div class="bg-app-surface border border-app-border rounded-md p-4">
            <p class="text-xs font-semibold text-app-muted">Terlambat</p>
            <p class="text-xl font-bold text-app-danger mt-1">{{ $stats['overdue'] ?? 0 }}</p>
        </div>
        <div class="bg-app-surface border border-app-border rounded-md p-4">
            <p class="text-xs font-semibold text-app-muted">Dikembalikan Hari Ini</p>
            <p class="text-xl font-bold text-app-success mt-1">{{ $stats['returned_today'] ?? 0 }}</p>
        </div>
        <div class="bg-app-surface border border-app-border rounded-md p-4">
            <p class="text-xs font-semibold text-app-muted">Denda Terbaru</p>
            <p class="text-xl font-bold text-app-warning mt-1">Auto</p>
        </div>
    </div>

    <div class="bg-app-surface border border-app-border rounded-md p-5" x-data="returnForm()">
        <form method="POST" action="{{ route('returns.process') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-app-text mb-1">Kode eksemplar</label>
                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="text" name="book_code" x-model="bookCode" @keydown.enter.prevent="lookupLoan()" placeholder="Scan barcode atau kode eksemplar" class="flex-1 px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                    <button type="button" @click="lookupLoan()" class="px-4 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md transition-colors">
                        Cari
                    </button>
                </div>
            </div>

            <template x-if="loan">
                <div class="border border-app-border rounded-md p-4 space-y-4 bg-app-background">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-app-muted">Peminjam</p>
                            <p class="font-semibold text-app-text" x-text="loan.member.name"></p>
                        </div>
                        <div>
                            <p class="text-app-muted">Judul Buku</p>
                            <p class="font-semibold text-app-text" x-text="loan.book.title"></p>
                        </div>
                        <div>
                            <p class="text-app-muted">Kode Eksemplar</p>
                            <p class="font-semibold text-app-text" x-text="loan.book_copy.code"></p>
                        </div>
                        <div>
                            <p class="text-app-muted">Jatuh Tempo</p>
                            <p class="font-semibold" :class="loan.is_overdue ? 'text-app-danger' : 'text-app-text'" x-text="loan.due_date"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-app-text mb-1">Tanggal kembali</label>
                            <input type="date" name="return_date" x-model="returnDate" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-app-text mb-1">Hari terlambat</label>
                            <div class="px-3 py-2 border border-app-border rounded-md text-sm bg-white text-app-text" x-text="loan.overdue_days"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-app-text mb-1">Estimasi denda</label>
                            <div class="px-3 py-2 border border-app-border rounded-md text-sm bg-white text-app-text" x-text="formatMoney(loan.fine_amount)"></div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-app-text mb-1">Catatan</label>
                        <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary" placeholder="Opsional"></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2">
                        <button type="submit" class="px-4 py-2 bg-app-success hover:opacity-90 text-white text-sm font-semibold rounded-md transition-colors">
                            Proses Pengembalian
                        </button>
                        <button type="button" @click="reset()" class="px-4 py-2 text-app-muted hover:text-app-text text-sm font-semibold">
                            Batal
                        </button>
                    </div>
                </div>
            </template>
        </form>
    </div>

    <div class="bg-app-surface border border-app-border rounded-md overflow-hidden">
        <div class="px-4 py-3 border-b border-app-border">
            <p class="text-sm font-semibold text-app-text">Daftar Eksemplar Dipinjam</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-app-border-muted">
                <thead class="bg-app-background">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Anggota</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Buku</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border-muted">
                    @forelse($borrowedItems as $item)
                        <tr>
                            <td class="px-4 py-3 text-sm text-app-text">{{ $item->loan->member->name }}</td>
                            <td class="px-4 py-3 text-sm text-app-text">{{ $item->bookCopy?->book?->title }}</td>
                            <td class="px-4 py-3 text-sm text-app-muted">{{ $item->bookCopy?->copy_code }}</td>
                            <td class="px-4 py-3 text-sm {{ $item->due_date->isPast() ? 'text-app-danger font-semibold' : 'text-app-text' }}">{{ $item->due_date->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center text-sm text-app-muted">Tidak ada item yang sedang dipinjam</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($borrowedItems->hasPages())
            <div class="px-4 py-3 border-t border-app-border">
                {{ $borrowedItems->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function returnForm() {
        return {
            bookCode: '',
            returnDate: new Date().toISOString().slice(0, 10),
            loan: null,
            formatMoney(value) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);
            },
            async lookupLoan() {
                if (!this.bookCode) return;
                const response = await fetch(`{{ route('returns.lookup') }}?book_code=${encodeURIComponent(this.bookCode)}`, {
                    headers: { 'Accept': 'application/json' },
                }).catch(() => null);
                if (!response || !response.ok) return;
                this.loan = await response.json();
            },
            reset() {
                this.bookCode = '';
                this.loan = null;
            },
        };
    }
</script>
@endpush
