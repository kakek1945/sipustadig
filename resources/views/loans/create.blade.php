@extends('layouts.dashboard')

@section('title', 'Peminjaman Baru')

@section('content')
@php
    $memberOptions = $members->map(fn ($member) => [
        'id' => $member->id,
        'name' => $member->name,
        'member_number' => $member->member_number,
        'type' => $member->type->label(),
        'class' => $member->class,
    ])->values();

    $copyOptions = $bookCopies->map(fn ($copy) => [
        'id' => $copy->id,
        'code' => $copy->copy_code,
        'barcode' => $copy->barcode,
        'title' => $copy->book?->title,
        'author' => $copy->book?->author,
    ])->values();
@endphp

<div class="max-w-5xl space-y-5" x-data="loanForm(@js($memberOptions), @js($copyOptions), {{ $maxBooks }}, {{ $loanDuration }})">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-app-text">Peminjaman Baru</h1>
            <p class="text-app-muted mt-0.5">Pilih anggota, scan atau pilih eksemplar, lalu konfirmasi transaksi</p>
        </div>
        <a href="{{ route('loans.index') }}" class="px-3 py-2 text-app-muted hover:text-app-text text-sm font-semibold">
            Kembali
        </a>
    </div>

    @if($errors->any())
        <x-alert type="error">{{ $errors->first() }}</x-alert>
    @endif

    <form method="POST" action="{{ route('loans.store') }}" class="bg-app-surface border border-app-border rounded-md overflow-hidden">
        @csrf
        <input type="hidden" name="loan_date" value="{{ now()->toDateString() }}">

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px]">
            <div class="p-5 space-y-6">
                <div class="flex flex-wrap items-center gap-3">
                    @foreach([1 => 'Anggota', 2 => 'Eksemplar', 3 => 'Konfirmasi'] as $number => $label)
                        <button type="button" @click="step = {{ $number }}" class="flex items-center gap-2 text-sm font-semibold" :class="step >= {{ $number }} ? 'text-app-primary' : 'text-app-muted'">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center border" :class="step >= {{ $number }} ? 'bg-app-primary text-white border-app-primary' : 'bg-app-background border-app-border'">{{ $number }}</span>
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <section x-show="step === 1" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-app-text mb-1">Scan / cari anggota</label>
                        <input type="text" x-model="memberQuery" @input="findMember()" @keydown.enter.prevent="selectFirstMember()" placeholder="Nama, nomor anggota, NISN/NIP..." class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[360px] overflow-y-auto">
                        <template x-for="member in filteredMembers" :key="member.id">
                            <button type="button" @click="selectMember(member)" class="text-left border rounded-md p-3 hover:border-app-primary hover:bg-app-info-surface transition-colors" :class="selectedMember?.id === member.id ? 'border-app-primary bg-app-info-surface' : 'border-app-border'">
                                <p class="text-sm font-semibold text-app-text" x-text="member.name"></p>
                                <p class="text-xs text-app-muted mt-1" x-text="member.member_number + ' - ' + member.type"></p>
                                <p class="text-xs text-app-muted" x-show="member.class" x-text="'Kelas ' + member.class"></p>
                            </button>
                        </template>
                    </div>

                    <input type="hidden" name="member_id" :value="selectedMember?.id ?? '{{ old('member_id') }}'">
                </section>

                <section x-show="step === 2" class="space-y-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-app-text mb-1">Scan / cari eksemplar</label>
                            <input type="text" x-model="copyQuery" @input="findCopies()" @keydown.enter.prevent="selectFirstCopy()" placeholder="Kode eksemplar, barcode, judul..." class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                        </div>
                        <div class="sm:w-40">
                            <label class="block text-sm font-semibold text-app-text mb-1">Batas</label>
                            <div class="px-3 py-2 border border-app-border rounded-md text-sm text-app-muted">
                                <span x-text="selectedCopies.length"></span> / {{ $maxBooks }} buku
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[360px] overflow-y-auto">
                        <template x-for="copy in filteredCopies" :key="copy.id">
                            <button type="button" @click="toggleCopy(copy)" class="text-left border rounded-md p-3 hover:border-app-primary hover:bg-app-info-surface transition-colors" :class="isCopySelected(copy.id) ? 'border-app-primary bg-app-info-surface' : 'border-app-border'">
                                <p class="text-sm font-semibold text-app-text" x-text="copy.title"></p>
                                <p class="text-xs text-app-muted mt-1" x-text="copy.code + (copy.barcode ? ' - ' + copy.barcode : '')"></p>
                                <p class="text-xs text-app-muted" x-text="copy.author"></p>
                            </button>
                        </template>
                    </div>

                    <template x-for="copy in selectedCopies" :key="copy.id">
                        <input type="hidden" name="book_copy_ids[]" :value="copy.id">
                    </template>
                </section>

                <section x-show="step === 3" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-app-border rounded-md p-4">
                            <p class="text-xs font-semibold text-app-muted uppercase">Anggota</p>
                            <p class="text-sm font-semibold text-app-text mt-2" x-text="selectedMember?.name ?? '-'"></p>
                            <p class="text-xs text-app-muted" x-text="selectedMember?.member_number ?? ''"></p>
                        </div>
                        <div class="border border-app-border rounded-md p-4">
                            <p class="text-xs font-semibold text-app-muted uppercase">Jatuh Tempo</p>
                            <p class="text-sm font-semibold text-app-text mt-2" x-text="dueDate"></p>
                            <p class="text-xs text-app-muted">{{ $loanDuration }} hari peminjaman</p>
                        </div>
                    </div>

                    <div class="border border-app-border rounded-md overflow-hidden">
                        <div class="px-4 py-3 bg-app-background border-b border-app-border">
                            <p class="text-sm font-semibold text-app-text">Daftar Eksemplar</p>
                        </div>
                        <div class="divide-y divide-app-border-muted">
                            <template x-for="copy in selectedCopies" :key="copy.id">
                                <div class="px-4 py-3">
                                    <p class="text-sm font-semibold text-app-text" x-text="copy.title"></p>
                                    <p class="text-xs text-app-muted" x-text="copy.code"></p>
                                </div>
                            </template>
                            <p x-show="selectedCopies.length === 0" class="px-4 py-8 text-sm text-center text-app-muted">Belum ada eksemplar dipilih</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-app-text mb-1">Catatan</label>
                        <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary" placeholder="Opsional">{{ old('notes') }}</textarea>
                    </div>
                </section>
            </div>

            <aside class="bg-app-background border-t lg:border-t-0 lg:border-l border-app-border p-5 space-y-4">
                <div>
                    <p class="text-sm font-semibold text-app-text">Ringkasan</p>
                    <p class="text-sm text-app-muted mt-1">Nomor pinjam akan dibuat otomatis saat transaksi disimpan.</p>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="text-app-muted">Anggota</span>
                        <span class="font-semibold text-app-text text-right" x-text="selectedMember?.name ?? '-'"></span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-app-muted">Total buku</span>
                        <span class="font-semibold text-app-text" x-text="selectedCopies.length"></span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-app-muted">Jatuh tempo</span>
                        <span class="font-semibold text-app-text" x-text="dueDate"></span>
                    </div>
                </div>

                <div class="flex gap-2 pt-3 border-t border-app-border">
                    <button type="button" @click="step = Math.max(1, step - 1)" class="px-3 py-2 text-app-muted hover:text-app-text text-sm font-semibold">
                        Kembali
                    </button>
                    <button type="button" x-show="step < 3" @click="nextStep()" class="flex-1 px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md transition-colors">
                        Lanjut
                    </button>
                    <button type="submit" x-show="step === 3" :disabled="!canSubmit()" class="flex-1 px-3 py-2 bg-app-success hover:opacity-90 text-white text-sm font-semibold rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Simpan
                    </button>
                </div>
            </aside>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function loanForm(members, copies, maxBooks, loanDuration) {
        return {
            step: 1,
            members,
            copies,
            maxBooks,
            memberQuery: '',
            copyQuery: '',
            filteredMembers: members.slice(0, 20),
            filteredCopies: copies.slice(0, 20),
            selectedMember: null,
            selectedCopies: [],
            dueDate: new Date(Date.now() + (loanDuration * 86400000)).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }),
            normalize(value) {
                return String(value ?? '').toLowerCase();
            },
            findMember() {
                const query = this.normalize(this.memberQuery);
                this.filteredMembers = this.members.filter((member) => {
                    return [member.name, member.member_number, member.type, member.class].some((field) => this.normalize(field).includes(query));
                }).slice(0, 20);
            },
            findCopies() {
                const query = this.normalize(this.copyQuery);
                this.filteredCopies = this.copies.filter((copy) => {
                    return [copy.title, copy.code, copy.barcode, copy.author].some((field) => this.normalize(field).includes(query));
                }).slice(0, 20);
            },
            selectFirstMember() {
                if (this.filteredMembers.length > 0) {
                    this.selectMember(this.filteredMembers[0]);
                }
            },
            selectFirstCopy() {
                if (this.filteredCopies.length > 0) {
                    this.toggleCopy(this.filteredCopies[0]);
                }
            },
            selectMember(member) {
                this.selectedMember = member;
            },
            isCopySelected(id) {
                return this.selectedCopies.some((copy) => copy.id === id);
            },
            toggleCopy(copy) {
                if (this.isCopySelected(copy.id)) {
                    this.selectedCopies = this.selectedCopies.filter((item) => item.id !== copy.id);
                    return;
                }

                if (this.selectedCopies.length < this.maxBooks) {
                    this.selectedCopies.push(copy);
                }
            },
            nextStep() {
                if (this.step === 1 && this.selectedMember) {
                    this.step = 2;
                } else if (this.step === 2 && this.selectedCopies.length > 0) {
                    this.step = 3;
                }
            },
            canSubmit() {
                return this.selectedMember && this.selectedCopies.length > 0;
            },
        };
    }
</script>
@endpush
