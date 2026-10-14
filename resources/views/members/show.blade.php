@extends('layouts.dashboard')

@section('title', 'Detail Anggota')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('members.index') }}" class="p-2 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Anggota</h1>
                <p class="text-gray-500">{{ $member->member_number }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('members.print-card', $member) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 hover:border-gray-400 bg-white text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Kartu
            </a>
            <a href="{{ route('members.edit', $member) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    <div class="w-24 h-24 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4 overflow-hidden">
                        @if($member->photo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk(config('filesystems.uploads'))->url($member->photo) }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl font-bold text-indigo-600">{{ substr($member->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $member->name }}</h2>
                    <p class="text-gray-500">{{ $member->email }}</p>
                    <div class="mt-3 flex items-center justify-center gap-2 flex-wrap">
                        <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full
                            @if($member->type->value === 'student') bg-blue-100 text-blue-700
                            @else bg-purple-100 text-purple-700 @endif">
                            {{ $member->type->label() }}
                        </span>
                        <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full
                            @if($member->status === 'active') bg-green-100 text-green-700
                            @elseif($member->status === 'inactive') bg-gray-100 text-gray-700
                            @else bg-red-100 text-red-700 @endif">
                            @if($member->status === 'active') Aktif
                            @elseif($member->status === 'inactive') Nonaktif
                            @else Terblokir @endif
                        </span>
                    </div>
                </div>

                <hr class="my-6 border-gray-200">

                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">No. Anggota</p>
                        <p class="text-sm font-medium text-gray-900 font-mono">{{ $member->member_number }}</p>
                    </div>
                    @if($member->type->value === 'student')
                        <div>
                            <p class="text-sm text-gray-500">NISN</p>
                            <p class="text-sm font-medium text-gray-900">{{ $member->nisn ?? '-' }}</p>
                        </div>
                        @if($member->class)
                            <div>
                                <p class="text-sm text-gray-500">Kelas</p>
                                <p class="text-sm font-medium text-gray-900">{{ $member->class }}
                                    @if($member->year_level)
                                        ({{ $member->year_level }})
                                    @endif
                                </p>
                            </div>
                        @endif
                    @else
                        <div>
                            <p class="text-sm text-gray-500">NIP</p>
                            <p class="text-sm font-medium text-gray-900">{{ $member->employee_number ?? '-' }}</p>
                        </div>
                    @endif
                    @if($member->phone)
                        <div>
                            <p class="text-sm text-gray-500">No. Telepon</p>
                            <p class="text-sm font-medium text-gray-900">{{ $member->phone }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500">Bergabung</p>
                        <p class="text-sm font-medium text-gray-900">{{ $member->join_date->format('d F Y') }}</p>
                    </div>
                    @if($member->expired_date)
                        <div>
                            <p class="text-sm text-gray-500">Masa Berlaku</p>
                            <p class="text-sm font-medium text-gray-900 {{ $member->expired_date->isPast() ? 'text-red-600' : '' }}">
                                {{ $member->expired_date->format('d F Y') }}
                                @if($member->expired_date->isPast())
                                    <span class="text-xs">(Expired)</span>
                                @endif
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Stats -->
                <hr class="my-6 border-gray-200">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-indigo-600">{{ $totalLoans }}</p>
                        <p class="text-xs text-gray-500">Total Pinjam</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold {{ $activeLoansCount > 0 ? 'text-amber-600' : 'text-green-600' }}">{{ $activeLoansCount }}</p>
                        <p class="text-xs text-gray-500">Aktif</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold {{ $overdueLoansCount > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $overdueLoansCount }}</p>
                        <p class="text-xs text-gray-500">Terlambat</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold {{ $unpaidFinesTotal > 0 ? 'text-red-600' : 'text-gray-400' }}">Rp {{ number_format($unpaidFinesTotal, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">Denda</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity -->
        <div class="lg:col-span-2">
            <!-- Active Loans -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Peminjaman Aktif</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($member->loans->where('status', 'active') as $loan)
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-16 bg-gray-200 rounded flex items-center justify-center overflow-hidden">
                                    @if($loan->items->first()?->bookCopy?->book?->cover_image)
                                        <img src="{{ \Illuminate\Support\Facades\\Illuminate\Support\Facades\Storage::disk(config('filesystems.uploads'))->url($loan->items->first()->bookCopy->book->cover_image) }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $loan->items->first()?->bookCopy?->book?->title ?? 'Unknown Book' }}</p>
                                    <p class="text-xs text-gray-500">{{ $loan->loan_number }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm @if($loan->isOverdue()) text-red-600 font-medium @else text-gray-900 @endif">
                                    {{ $loan->due_date->format('d M Y') }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $loan->isOverdue() ? $loan->daysOverdue() . ' hari terlambat' : 'Jatuh tempo' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p>Tidak ada peminjaman aktif</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Loan History -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Riwayat Peminjaman</h3>
                    <span class="text-sm text-gray-500">{{ $member->loans->count() }} total</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Pinjam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($member->loans as $loan)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ $loan->loan_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $loan->loan_date->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $loan->due_date->format('d M Y') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                            @if($loan->status->value === 'returned') bg-green-100 text-green-700
                                            @elseif($loan->status->value === 'overdue') bg-red-100 text-red-700
                                            @else bg-amber-100 text-amber-700 @endif">
                                            {{ $loan->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $loan->items->count() }} buku
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada riwayat peminjaman
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection