<button
    {{ $attributes->merge([
        'type' => $type ?? 'button',
        'class' => 'inline-flex items-center justify-center gap-1.5 px-3 py-[5px] text-sm font-semibold rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed'
        . ($variant === 'primary' ? ' bg-app-primary hover:bg-app-primary-hover text-white focus:ring-app-primary' : '')
        . ($variant === 'success' ? ' bg-app-success hover:bg-app-success-hover text-white focus:ring-app-success' : '')
        . ($variant === 'danger' ? ' bg-app-danger hover:bg-app-danger-hover text-white focus:ring-app-danger' : '')
        . ($variant === 'warning' ? ' bg-app-warning hover:opacity-90 text-white focus:ring-app-warning' : '')
        . ($variant === 'secondary' ? ' bg-app-surface hover:bg-app-surface-subtle border border-app-border text-app-text focus:ring-app-primary' : '')
        . ($variant === 'ghost' ? ' bg-transparent hover:bg-app-surface-subtle text-app-text focus:ring-app-primary' : '')
        . ($variant === 'link' ? ' bg-transparent hover:underline text-app-primary focus:ring-0 p-0 min-h-0' : '')
        . (empty($variant) || $variant === 'default' ? ' bg-app-primary hover:bg-app-primary-hover text-white focus:ring-app-primary' : '')
    ]) }}
>
    {{ $slot }}
</button>