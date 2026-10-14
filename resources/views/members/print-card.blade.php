@extends('layouts.dashboard')

@section('title', 'Cetak Kartu - ' . $member->member_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6 no-print-container">
    <div class="bg-app-surface border border-app-border rounded-md p-5 no-print">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('members.show', $member) }}" class="text-sm text-app-muted hover:text-app-primary">Kembali ke detail anggota</a>
                <h1 class="text-2xl font-bold text-app-text mt-2">Cetak Kartu Anggota</h1>
                <p class="text-sm text-app-muted mt-1">Kartu ini berisi QR standar untuk scan anggota di transaksi dan lookup cepat.</p>
            </div>
            <button onclick="window.print()" class="px-4 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md">
                Cetak Sekarang
            </button>
        </div>
    </div>

    <div class="bg-app-background border border-app-border rounded-md p-8 flex justify-center print-stage">
        <div class="member-card bg-white border border-black rounded-xl overflow-hidden text-black" id="member-card">
            <div class="bg-app-primary text-white px-5 py-4 flex items-center justify-between card-header">
                <div>
                    <p class="text-xs font-semibold tracking-[0.2em] uppercase">Kartu Anggota</p>
                    <h2 class="text-xl font-extrabold mt-1">{{ app('settings')->get('library.name', 'Perpustakaan Sekolah') }}</h2>
                </div>
                <x-brand-logo class="w-12 h-12 object-contain bg-white rounded-md p-1" />
            </div>

            <div class="p-5 grid grid-cols-[1fr_120px] gap-4 items-center">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Nama</p>
                    <h3 class="text-lg font-bold leading-tight truncate">{{ $member->name }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $member->type->label() }}</p>

                    <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <p class="font-semibold text-gray-500 uppercase">No. Anggota</p>
                            <p class="font-mono font-bold text-sm">{{ $member->member_number }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-500 uppercase">Status</p>
                            <p class="font-bold uppercase">{{ $member->status }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-500 uppercase">Identitas</p>
                            <p>{{ $member->type->value === 'student' ? ($member->nisn ?? '-') : ($member->employee_number ?? '-') }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-500 uppercase">Berlaku</p>
                            <p>{{ $member->expired_date?->format('d/m/Y') ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <canvas id="member-qr" class="w-28 h-28 mx-auto"></canvas>
                    <p class="text-[10px] font-mono mt-2 break-all">{{ $member->member_number }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.member-card {
    width: 86mm;
    min-height: 54mm;
}

@media print {
    body, html {
        background: white !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .no-print,
    aside,
    nav,
    header,
    footer,
    .no-print-container > :not(.print-stage) {
        display: none !important;
    }

    .print-stage {
        display: flex !important;
        padding: 0 !important;
        border: none !important;
        background: white !important;
        justify-content: center !important;
    }

    .member-card {
        box-shadow: none !important;
        page-break-inside: avoid !important;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<script>
    const qrPayload = @json($qrPayload);

    document.addEventListener('DOMContentLoaded', () => {
        const qr = qrcode(0, 'M');
        qr.addData(qrPayload);
        qr.make();

        const canvas = document.getElementById('member-qr');
        const ctx = canvas.getContext('2d');
        const modules = qr.getModuleCount();
        const size = 116;
        const cell = size / modules;

        canvas.width = size;
        canvas.height = size;
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, size, size);
        ctx.fillStyle = '#111827';

        for (let row = 0; row < modules; row++) {
            for (let col = 0; col < modules; col++) {
                if (qr.isDark(row, col)) {
                    ctx.fillRect(Math.round(col * cell), Math.round(row * cell), Math.ceil(cell), Math.ceil(cell));
                }
            }
        }
    });
</script>
@endsection
