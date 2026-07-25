@props([
    'title' => null,
    'value' => null,
    'icon' => null,
    'trend' => null,
    'trendUp' => true,
    'color' => 'neutral',
])

@php
    $colorClasses = [
        'neutral' => 'bg-neutral-50 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300',
        'blue' => 'bg-blue-50 text-blue-600 dark:bg-blue-950 dark:text-blue-300',
        'green' => 'bg-green-50 text-green-600 dark:bg-green-950 dark:text-green-300',
        'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-300',
        'purple' => 'bg-purple-50 text-purple-600 dark:bg-purple-950 dark:text-purple-300',
        'red' => 'bg-red-50 text-red-600 dark:bg-red-950 dark:text-red-300',
    ][$color] ?? 'bg-neutral-50 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300';
@endphp

<div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-zinc-900">
    <div class="flex items-start justify-between">
        <div class="space-y-2">
            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">
                {{ $title }}
            </p>
            <p class="text-3xl font-bold tracking-tight text-neutral-900 dark:text-white">
                {{ $value }}
            </p>
            @if ($trend)
                <div class="flex items-center gap-1 text-xs">
                    <span class="{{ $trendUp ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        <svg class="inline h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            @if ($trendUp)
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6 9 12.75l4.286-4.286a11.95 11.95 0 0 1 4.306 6.43l.776 2.898m0 0 3.182-5.511m-3.182 5.51-5.511-3.181" />
                            @endif
                        </svg>
                        {{ $trend }}
                    </span>
                    <span class="text-neutral-400 dark:text-neutral-500">dari bulan lalu</span>
                </div>
            @endif
        </div>

        @if ($icon)
            <div class="flex h-12 w-12 items-center justify-center rounded-lg {{ $colorClasses }}">
                {!! $icon !!}
            </div>
        @endif
    </div>
</div>
