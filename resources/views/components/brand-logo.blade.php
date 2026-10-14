@props(['alt' => 'Logo SMP Negeri 3 Merbau'])

<img
    src="{{ asset('images/sirkulib-logo.png') }}"
    alt="{{ $alt }}"
    {{ $attributes->merge(['class' => 'object-contain']) }}
>
