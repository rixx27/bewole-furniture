@props([
    'size' => 'default',
    'class' => '',
])

@php
    $sizes = [
        'default' => 'max-w-7xl',
        'narrow'  => 'max-w-5xl',
        'wide'    => 'max-w-[90rem]',
    ];

    $containerClass = ($sizes[$size] ?? $sizes['default']) . ' mx-auto w-full px-4 sm:px-6 lg:px-8';
@endphp

<div {{ $attributes->merge(['class' => trim($containerClass . ' ' . $class)]) }}>
    {{ $slot }}
</div>
