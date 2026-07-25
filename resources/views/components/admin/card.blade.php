@props([
    'padding' => true,
])

<div
    {{ $attributes->merge([
        'class' => 'rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-zinc-900' . ($padding ? ' p-6' : '')
    ]) }}
>
    {{ $slot }}
</div>
