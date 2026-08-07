@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'class' => '',
])

@php
    $variants = [
        'primary' => 'bg-wood-primary text-white hover:bg-wood-primary-dark shadow-lg shadow-wood-primary/20',
        'secondary' => 'bg-wood-secondary text-white hover:bg-wood-primary-dark shadow-lg shadow-wood-secondary/20',
        'outline' => 'border border-wood-primary text-wood-primary hover:bg-wood-primary hover:text-white',
        'outline-light' => 'border border-white/70 text-white hover:bg-white hover:text-wood-primary',
        'ghost' => 'text-wood-primary hover:bg-wood-primary/10',
        'light' => 'bg-white text-wood-primary hover:bg-wood-bg shadow-lg shadow-black/5',
    ];

    $sizes = [
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-6 py-3 text-sm',
        'lg' => 'px-8 py-4 text-base',
    ];

    $classes = implode(' ', [
        'inline-flex items-center justify-center gap-2 rounded-full font-semibold',
        'transition-all duration-300 ease-out',
        'hover:-translate-y-0.5 active:translate-y-0',
        'focus:outline-none focus-visible:ring-2 focus-visible:ring-wood-secondary focus-visible:ring-offset-2',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
        $class,
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
