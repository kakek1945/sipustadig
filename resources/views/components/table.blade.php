<table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-app-border-muted']) }}>
    <thead class="bg-app-background">
        <tr>
            {{ $thead }}
        </tr>
    </thead>
    <tbody class="bg-app-surface divide-y divide-app-border-muted">
        {{ $tbody }}
    </tbody>
</table>