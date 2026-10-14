<div {{ $attributes->merge(['class' => 'fixed inset-0 z-50 flex items-center justify-center bg-app-topbar/45 p-5']) }}>
    <div class="bg-app-surface rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        @if($title)
            <div class="flex items-center justify-between px-4 py-3 border-b border-app-border-muted">
                <h3 class="text-base font-semibold text-app-text">{{ $title }}</h3>
                <button type="button" class="text-app-muted hover:text-app-text" @click="$dispatch('close-modal')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif
        <div class="p-4">
            {{ $slot }}
        </div>
        @if($footer)
            <div class="px-4 py-3 bg-app-surface-subtle border-t border-app-border-muted rounded-b-lg">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>