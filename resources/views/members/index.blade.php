@extends('layouts.dashboard')

@section('title', 'Data Anggota')

@section('content')
<div class="space-y-5">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-app-text">Data Anggota</h1>
            <p class="text-app-muted mt-0.5">Kelola data anggota perpustakaan</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('members.create') }}" class="inline-flex items-center gap-1.5 px-3 py-[5px] bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Anggota
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-app-surface border border-app-border rounded-md p-4 shadow-[0_1px_0_rgba(27,31,36,0.04)]">
            <p class="text-xs font-semibold text-app-muted">Total Anggota</p>
            <p class="text-xl font-bold text-app-text mt-1">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="bg-app-surface border border-app-border rounded-md p-4 shadow-[0_1px_0_rgba(27,31,36,0.04)]">
            <p class="text-xs font-semibold text-app-muted">Aktif</p>
            <p class="text-xl font-bold text-app-success mt-1">{{ $stats['active'] ?? 0 }}</p>
        </div>
        <div class="bg-app-surface border border-app-border rounded-md p-4 shadow-[0_1px_0_rgba(27,31,36,0.04)]">
            <p class="text-xs font-semibold text-app-muted">Siswa</p>
            <p class="text-xl font-bold text-app-primary mt-1">{{ $stats['students'] ?? 0 }}</p>
        </div>
        <div class="bg-app-surface border border-app-border rounded-md p-4 shadow-[0_1px_0_rgba(27,31,36,0.04)]">
            <p class="text-xs font-semibold text-app-muted">Guru</p>
            <p class="text-xl font-bold text-app-warning mt-1">{{ $stats['teachers'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-app-surface border border-app-border rounded-md p-4">
        <form method="GET" action="{{ route('members.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-semibold text-app-text mb-1">Cari</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, email, NISN..." class="w-full pl-9 pr-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                    <svg class="absolute left-2.5 top-2 w-4 h-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            <div class="w-36">
                <label class="block text-xs font-semibold text-app-text mb-1">Tipe</label>
                <select name="type" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                    <option value="">Semua</option>
                    <option value="student" {{ request('type') == 'student' ? 'selected' : '' }}>Siswa</option>
                    <option value="teacher" {{ request('type') == 'teacher' ? 'selected' : '' }}>Guru</option>
                </select>
            </div>
            <div class="w-36">
                <label class="block text-xs font-semibold text-app-text mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                    <option value="">Semua</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Terblokir</option>
                </select>
            </div>
            <div class="w-36">
                <label class="block text-xs font-semibold text-app-text mb-1">Kelas</label>
                <select name="class" class="w-full px-3 py-2 border border-app-border rounded-md text-sm bg-app-surface focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary">
                    <option value="">Semua</option>
                    @foreach($classes ?? [] as $class)
                        <option value="{{ $class }}" {{ request('class') == $class ? 'selected' : '' }}>{{ $class }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md transition-colors">
                    Filter
                </button>
                <a href="{{ route('members.index') }}" class="px-3 py-2 text-app-muted hover:text-app-text text-sm font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-app-surface border border-app-border rounded-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-app-border-muted">
                <thead class="bg-app-background">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">No. Anggota</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Kelas</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-semibold text-app-muted uppercase tracking-wider">Bergabung</th>
                        <th class="px-4 py-3 text-right text-[10px] font-semibold text-app-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border-muted">
                    @forelse($members as $member)
                        <tr class="hover:bg-app-surface-subtle">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-app-info-surface rounded-full flex items-center justify-center overflow-hidden flex-shrink-0">
                                        @if($member->photo)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk(config('filesystems.uploads'))->url($member->photo) }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-app-primary font-semibold text-sm">{{ substr($member->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-app-text">{{ $member->name }}</p>
                                        <p class="text-xs text-app-muted">{{ $member->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex px-2 py-0.5 text-[10px] font-medium rounded-full
                                    @if($member->type->value === 'student') bg-app-info-surface text-app-primary
                                    @else bg-app-warning-surface text-app-warning @endif">
                                    {{ $member->type->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-app-text">
                                <span class="font-mono font-semibold">{{ $member->member_number }}</span>
                                <p class="text-[10px] text-app-muted">{{ $member->type->value === 'student' ? 'NISN: ' . ($member->nisn ?? '-') : 'NIP: ' . ($member->employee_number ?? '-') }}</p>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-app-text">
                                @if($member->class)
                                    {{ $member->class }}
                                    @if($member->year_level)
                                        <span class="text-app-muted">({{ $member->year_level }})</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold rounded-full
                                    @if($member->status === 'active') bg-app-success-surface text-app-success
                                    @elseif($member->status === 'inactive') bg-app-surface-subtle text-app-muted
                                    @else bg-app-danger-surface text-app-danger @endif">
                                    @if($member->status === 'active') Aktif
                                    @elseif($member->status === 'inactive') Nonaktif
                                    @else Terblokir @endif
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-app-muted">
                                {{ $member->join_date->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('members.show', $member) }}" class="p-1.5 text-app-muted hover:text-app-primary hover:bg-app-surface-subtle rounded-md" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('members.edit', $member) }}" class="p-1.5 text-app-muted hover:text-app-primary hover:bg-app-surface-subtle rounded-md" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('members.destroy', $member) }}" class="inline" onsubmit="return confirm('Yakin hapus anggota ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-app-muted hover:text-app-danger hover:bg-app-surface-subtle rounded-md" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 text-app-border mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <h3 class="text-sm font-semibold text-app-text mb-1">Belum ada data anggota</h3>
                                    <p class="text-xs text-app-muted">Mulai dengan menambahkan anggota baru</p>
                                    <a href="{{ route('members.create') }}" class="mt-4 px-3 py-2 bg-app-primary hover:bg-app-primary-hover text-white text-sm font-semibold rounded-md transition-colors">
                                        Tambah Anggota
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($members->hasPages())
            <div class="px-4 py-3 bg-app-surface border-t border-app-border-muted">
                {{ $members->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection