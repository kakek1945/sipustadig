{{ $attributes->merge([
    'class' => 'w-full px-3 py-2 border border-app-border rounded-md bg-app-surface text-app-text placeholder-app-muted focus:outline-none focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary transition-colors'
]) }}
{{ $slot }}