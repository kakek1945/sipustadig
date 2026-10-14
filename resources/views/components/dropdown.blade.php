<div x-data="{ open: false }" {{ $attributes->merge(['class' => 'relative']) }}>
    <button type="button" @click="open = !open" @click.away="open = false" class="flex items-center gap-2 px-3 py-2 text-sm text-app-text bg-app-surface border border-app-border rounded-md hover:bg-app-surface-subtle focus:outline-none focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary transition-colors">
        {{ $trigger }}
        <svg class="w-4 h-4 text-app-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div x-show="open" x-transition class="absolute right-0 z-10 w-48 mt-1 bg-app-surface rounded-md shadow-[0_4px_6px_-1px_rgba(27,31,36,0.1)] border border-app-border">
        {{ $slot }}
    </div>
</div>