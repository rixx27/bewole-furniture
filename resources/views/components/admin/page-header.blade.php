@props([
    'title' => null,
    'description' => null,
    'actions' => null,
])

<div class="mb-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="space-y-1">
            @if ($title)
                <h2 class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white">
                    {{ $title }}
                </h2>
            @endif
            @if ($description)
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    {{ $description }}
                </p>
            @endif
        </div>

        @if ($actions)
            <div class="flex items-center gap-3">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
