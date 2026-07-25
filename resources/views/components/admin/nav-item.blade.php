@props([
    'route' => '#',
    'active' => false,
    'expanded' => true,
    'label' => '',
])

<a
    href="{{ $route }}"
    @class([
        'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 group',
        'bg-black text-white dark:bg-white dark:text-black shadow-sm' => $active,
        'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700/50 hover:text-zinc-900 dark:hover:text-zinc-200' => !$active,
    ])
    :class="expanded ? '' : 'justify-center'"
>
    {{ $icon ?? '' }}
    <span x-show="expanded" x-transition:enter="transition ease-out duration-200">{{ $label }}</span>
</a>
