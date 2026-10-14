@extends('layouts.dashboard')

@section('title', 'Scan Cepat')

@section('content')
<div class="max-w-5xl space-y-5" x-data="scanLookup()">
    <div>
        <h1 class="text-2xl font-bold text-app-text">Scan Cepat</h1>
        <p class="text-app-muted mt-0.5">Pindai kartu anggota, label eksemplar, barcode, atau ISBN untuk lookup cepat.</p>
    </div>

    <div class="bg-app-surface border border-app-border rounded-md p-5 space-y-4">
        <div>
            <label class="block text-sm font-semibold text-app-text mb-1">Input Scan</label>
            <div class="flex flex-col sm:flex-row gap-2">
                <input type="text" x-model="scan" x-ref="scanInput" @keydown.enter.prevent="resolveScan()" placeholder="Scan QR/barcode atau ketik manual" class="flex-1 px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                <button type="button" @click="resolveScan()" class="px-4 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">
                    Cari
                </button>
                <button type="button" @click="reset()" class="px-4 py-2 bg-app-surface hover:bg-app-surface-subtle text-app-text text-sm font-semibold border border-app-border rounded-md">
                    Reset
                </button>
            </div>
            <p class="text-xs text-app-muted mt-2">Scanner fisik mode keyboard dapat langsung dipakai; tekan Enter untuk memproses hasil scan.</p>
        </div>

        <template x-if="loading">
            <div class="text-sm text-app-muted">Memproses hasil scan...</div>
        </template>

        <template x-if="error">
            <x-alert type="error"><span x-text="error"></span></x-alert>
        </template>
    </div>

    <template x-if="result && result.type === 'member'">
        <div class="bg-app-surface border border-app-border rounded-md p-5">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold text-app-muted uppercase">Anggota</p>
                    <h2 class="text-xl font-bold text-app-text mt-1" x-text="result.member.name"></h2>
                    <p class="text-sm text-app-muted font-mono mt-1" x-text="result.member.member_number"></p>
                </div>
                <span class="inline-flex w-fit px-3 py-1 text-xs font-semibold rounded-full" :class="result.member.can_borrow ? 'bg-app-success-surface text-app-success' : 'bg-app-danger-surface text-app-danger'" x-text="result.member.can_borrow ? 'Bisa meminjam' : 'Perlu dicek'"></span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-5 text-sm">
                <div>
                    <p class="text-app-muted">Email</p>
                    <p class="font-semibold text-app-text" x-text="result.member.email || '-'"></p>
                </div>
                <div>
                    <p class="text-app-muted">Tipe</p>
                    <p class="font-semibold text-app-text" x-text="result.member.type"></p>
                </div>
                <div>
                    <p class="text-app-muted">Kelas</p>
                    <p class="font-semibold text-app-text" x-text="result.member.class || '-'"></p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 mt-5">
                <a :href="memberUrl(result.member.id)" class="px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">Buka Profil</a>
                <a :href="loanUrl(result.member.member_number)" class="px-3 py-2 border border-app-border text-app-text hover:bg-app-surface-subtle text-sm font-semibold rounded-md">Mulai Peminjaman</a>
            </div>
        </div>
    </template>

    <template x-if="result && result.type === 'book_copy'">
        <div class="bg-app-surface border border-app-border rounded-md p-5">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold text-app-muted uppercase">Eksemplar</p>
                    <h2 class="text-xl font-bold text-app-text mt-1" x-text="result.book_copy.book.title"></h2>
                    <p class="text-sm text-app-muted mt-1" x-text="result.book_copy.book.author || '-'"></p>
                </div>
                <span class="inline-flex w-fit px-3 py-1 text-xs font-semibold rounded-full bg-app-info-surface text-app-primary" x-text="result.book_copy.status"></span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-5 text-sm">
                <div>
                    <p class="text-app-muted">Kode</p>
                    <p class="font-mono font-semibold text-app-text" x-text="result.book_copy.copy_code"></p>
                </div>
                <div>
                    <p class="text-app-muted">Barcode</p>
                    <p class="font-mono font-semibold text-app-text" x-text="result.book_copy.barcode || '-'"></p>
                </div>
                <div>
                    <p class="text-app-muted">Rak</p>
                    <p class="font-semibold text-app-text" x-text="result.book_copy.book.rack_location || '-'"></p>
                </div>
                <div>
                    <p class="text-app-muted">Kondisi</p>
                    <p class="font-semibold text-app-text" x-text="result.book_copy.condition || '-'"></p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 mt-5">
                <a :href="copyUrl(result.book_copy.id)" class="px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">Buka Detail</a>
                <a :href="labelUrl(result.book_copy.id)" target="_blank" class="px-3 py-2 border border-app-border text-app-text hover:bg-app-surface-subtle text-sm font-semibold rounded-md">Cetak Label</a>
            </div>
        </div>
    </template>

    <template x-if="result && result.type === 'isbn'">
        <div class="bg-app-surface border border-app-border rounded-md p-5">
            <p class="text-xs font-semibold text-app-muted uppercase">ISBN</p>
            <h2 class="text-xl font-bold text-app-text mt-1" x-text="result.book.title"></h2>
            <p class="text-sm text-app-muted mt-1" x-text="result.book.author || '-'"></p>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-5 text-sm">
                <div>
                    <p class="text-app-muted">ISBN</p>
                    <p class="font-mono font-semibold text-app-text" x-text="result.book.isbn"></p>
                </div>
                <div>
                    <p class="text-app-muted">Penerbit</p>
                    <p class="font-semibold text-app-text" x-text="result.book.publisher || '-'"></p>
                </div>
                <div>
                    <p class="text-app-muted">Tahun</p>
                    <p class="font-semibold text-app-text" x-text="result.book.publish_year || '-'"></p>
                </div>
                <div>
                    <p class="text-app-muted">Rak</p>
                    <p class="font-semibold text-app-text" x-text="result.book.rack_location || '-'"></p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 mt-5">
                <a :href="bookUrl(result.book.id)" class="px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">Buka Buku</a>
            </div>
        </div>
    </template>
</div>

<script>
    function scanLookup() {
        return {
            scan: '',
            result: null,
            loading: false,
            error: '',
            init() {
                this.$nextTick(() => this.$refs.scanInput?.focus());
            },
            async resolveScan() {
                const value = this.scan.trim();
                if (!value) return;

                this.loading = true;
                this.error = '';
                this.result = null;

                try {
                    const response = await fetch(@json(route('scan.resolve')), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': @json(csrf_token()),
                        },
                        body: JSON.stringify({ scan: value }),
                    });
                    const data = await response.json();

                    if (!response.ok) {
                        this.error = data.message || 'Hasil scan tidak ditemukan.';
                        return;
                    }

                    this.result = data;
                    this.scan = '';
                    this.$nextTick(() => this.$refs.scanInput?.focus());
                } catch (e) {
                    this.error = 'Gagal memproses scan.';
                } finally {
                    this.loading = false;
                }
            },
            reset() {
                this.scan = '';
                this.result = null;
                this.error = '';
                this.$nextTick(() => this.$refs.scanInput?.focus());
            },
            memberUrl(id) {
                return @json(url('/dashboard/members')) + '/' + id;
            },
            copyUrl(id) {
                return @json(url('/dashboard/book-copies')) + '/' + id;
            },
            labelUrl(id) {
                return @json(url('/dashboard/book-copies')) + '/' + id + '/print-label';
            },
            bookUrl(id) {
                return @json(url('/dashboard/books')) + '/' + id;
            },
            loanUrl(memberNumber) {
                return @json(route('loans.create')) + '?member=' + encodeURIComponent(memberNumber);
            },
        };
    }
</script>
@endsection
