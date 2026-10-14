<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-8 px-4 text-center border border-dashed border-app-border rounded-lg bg-app-surface']) }}>
    <svg class="w-10 h-10 md:w-12 md:h-12 text-app-border mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
    </svg>
    <h3 class="text-base font-semibold text-app-text mb-1">{{ $title ?? 'Belum ada data' }}</h3>
    <p class="text-sm text-app-muted max-w-sm">{{ $description ?? 'Tidak ada item untuk ditampilkan.' }}</p>
    @if($action)
        <div class="mt-4 flex gap-2">
            {{ $action }}
        </div>
    @endif
</div>