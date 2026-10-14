<span
    {{ $attributes->merge([
        'class' => 'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold whitespace-nowrap'
        . ($type === 'success' ? ' bg-app-success-surface text-app-success' : '')
        . ($type === 'primary' ? ' bg-app-info-surface text-app-primary' : '')
        . ($type === 'danger' ? ' bg-app-danger-surface text-app-danger' : '')
        . ($type === 'warning' ? ' bg-app-warning-surface text-app-warning' : '')
        . ($type === 'neutral' ? ' bg-app-surface-subtle text-app-muted' : '')
        . ($type === 'info' ? ' bg-app-info-surface text-app-info' : '')
        . (empty($type) || $type === 'default' ? ' bg-app-surface-subtle text-app-muted' : '')
    ]) }}
>
    {{ $slot }}
</span>