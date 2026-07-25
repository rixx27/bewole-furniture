@props(['items' => []])

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="flex flex-wrap items-center gap-1.5 text-sm">
        {{-- Dashboard home --}}
        <li>
            <a
                href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-1 text-neutral-500 transition-colors hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                <span class="sr-only">Dashboard</span>
            </a>
        </li>

        {{-- Breadcrumb items --}}
        @foreach ($items as $item)
            <li class="flex items-center gap-1.5">
                {{-- Separator --}}
                <svg class="h-4 w-4 text-neutral-300 dark:text-neutral-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>

                @if (isset($item['url']) && !$loop->last)
                    <a
                        href="{{ $item['url'] }}"
                        class="text-neutral-500 transition-colors hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200"
                    >
                        {{ $item['label'] }}
                    </a>
                @else
                    <span
                        class="font-medium text-neutral-900 dark:text-white"
                        aria-current="page"
                    >
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
