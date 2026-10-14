{{ $attributes->merge([
    'class' => 'w-full px-3 py-2 border border-app-border rounded-md bg-app-surface text-app-text focus:outline-none focus:ring-2 focus:ring-app-primary/15 focus:border-app-primary transition-colors cursor-pointer'
]) }}
{{ $slot }}