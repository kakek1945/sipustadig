<div {{ $attributes->merge(['class' => 'bg-app-surface border border-app-border rounded-md shadow-[0_1px_0_rgba(27,31,36,0.04)]) }}>
    @if($title)
        <div class="px-4 py-3 border-b border-app-border-muted flex items-center justify-between">
            <h3 class="text-base font-semibold text-app-text">{{ $title }}</h3>
            @if($description)
                <p class="mt-0.5 text-xs text-app-muted">{{ $description }}</p>
            @endif
        </div>
    @endif
    <div class="p-4">
        {{ $slot }}
    </div>
</div>