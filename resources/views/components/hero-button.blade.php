@props([
    'target' => null,   // string|null - raw stored value (e.g. "products", "/products")
    'text' => '#',
    'variant' => 'primary', // primary|secondary
])

@php
    use App\Enums\HeroButtonTarget;

    $href = HeroButtonTarget::resolveHref($target);
    $classes = $variant === 'secondary'
        ? 'inline-flex items-center gap-2 rounded-lg border border-white/30 bg-white/10 px-6 py-3 text-sm font-semibold text-white backdrop-blur-sm transition-colors hover:bg-white/20'
        : 'inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 text-sm font-semibold text-white shadow-lg transition-colors hover:bg-primary-dark';
@endphp

<a href="{{ $href }}" class="{{ $classes }}">{{ $text }}</a>
