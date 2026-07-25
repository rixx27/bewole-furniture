@props(['compact' => false])

@php
    $user = auth()->user();

    // Helper to determine active menu item
    $isActive = fn($route) => request()->routeIs($route);
    $isGroupActive = fn($routes) => collect($routes)->some(fn($r) => request()->routeIs($r));
@endphp

<!-- Desktop Sidebar -->
<aside
    x-data="{ expanded: true }"
    x-on:keydown.escape="expanded = false"
    class="hidden lg:flex flex-col border-r border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 transition-all duration-300 ease-in-out"
    :class="expanded ? 'w-64' : 'w-16'"
>
    <!-- Logo -->
    <div class="flex h-16 items-center justify-between border-b border-zinc-200 dark:border-zinc-700 px-4">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 overflow-hidden">
            <div class="flex aspect-square size-9 items-center justify-center rounded-lg bg-black dark:bg-white text-white dark:text-black shrink-0">
                <x-app-logo-icon class="size-5 fill-current" />
            </div>
            <span x-show="expanded" x-transition:enter="transition ease-out duration-200" class="text-sm font-semibold tracking-tight whitespace-nowrap">
                {{ config('app.name', 'Bewole') }}
            </span>
        </a>
        <button
            x-on:click="expanded = !expanded"
            class="flex items-center justify-center rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-700 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
        >
            <svg class="size-4 transition-transform duration-200" :class="expanded ? 'rotate-0' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
        {{-- Dashboard --}}
        <x-admin::nav-item
            :route="route('admin.dashboard')"
            :active="request()->routeIs('admin.dashboard')"
            :expanded="true"
            label="Dashboard"
        >
            <x-slot:icon>
                <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </x-slot:icon>
        </x-admin::nav-item>

        <div x-show="expanded" class="my-2 border-t border-zinc-200 dark:border-zinc-700"></div>

        {{-- Master Data Group --}}
        <div x-show="expanded" class="px-3 py-1.5">
            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Master Data') }}</p>
        </div>

        <x-admin::nav-item
            :route="route('admin.categories.index')"
            :active="request()->routeIs('admin.categories.*')"
            :expanded="true"
            label="Categories"
        >
            <x-slot:icon>
                <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </x-slot:icon>
        </x-admin::nav-item>

        <x-admin::nav-item
            :route="route('admin.products.index')"
            :active="request()->routeIs('admin.products.*')"
            :expanded="true"
            label="Products"
        >
            <x-slot:icon>
                <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </x-slot:icon>
        </x-admin::nav-item>

        <x-admin::nav-item
            :route="route('admin.product-images.index')"
            :active="request()->routeIs('admin.product-images.*')"
            :expanded="true"
            label="Product Gallery"
        >
            <x-slot:icon>
                <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </x-slot:icon>
        </x-admin::nav-item>

        <div x-show="expanded" class="my-2 border-t border-zinc-200 dark:border-zinc-700"></div>

        {{-- Content Group --}}
        <div x-show="expanded" class="px-3 py-1.5">
            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Content') }}</p>
        </div>

        <x-admin::nav-item
            :route="route('admin.hero-banners.index')"
            :active="request()->routeIs('admin.hero-banners.*')"
            :expanded="true"
            label="Hero Banner"
        >
            <x-slot:icon>
                <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </x-slot:icon>
        </x-admin::nav-item>

        <x-admin::nav-item
            :route="route('admin.portfolios.index')"
            :active="request()->routeIs('admin.portfolios.*')"
            :expanded="true"
            label="Portfolio"
        >
            <x-slot:icon>
                <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A1.5 1.5 0 0021.75 19.5V4.5A1.5 1.5 0 0020.25 3H3.75A1.5 1.5 0 002.25 4.5v15A1.5 1.5 0 003.75 21z" />
                </svg>
            </x-slot:icon>
        </x-admin::nav-item>

        <x-admin::nav-item
            :route="route('admin.testimonials.index')"
            :active="request()->routeIs('admin.testimonials.*')"
            :expanded="true"
            label="Testimonials"
        >
            <x-slot:icon>
                <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                </svg>
            </x-slot:icon>
        </x-admin::nav-item>

        {{-- FAQ --}}
        <x-admin::nav-item
            :route="route('admin.faq.index')"
            :active="request()->routeIs('admin.faq.*')"
            :expanded="true"
            label="FAQ"
        >
            <x-slot:icon>
                <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                </svg>
            </x-slot:icon>
        </x-admin::nav-item>

        <div x-show="expanded" class="my-2 border-t border-zinc-200 dark:border-zinc-700"></div>

        {{-- Transactions Group --}}
        <div x-show="expanded" class="px-3 py-1.5">
            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Transactions') }}</p>
        </div>

        <x-admin::nav-item
            :route="route('admin.orders.index')"
            :active="request()->routeIs('admin.orders.*')"
            :expanded="true"
            label="Orders"
        >
            <x-slot:icon>
                <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                </svg>
            </x-slot:icon>
        </x-admin::nav-item>

        <x-admin::nav-item
            :route="route('admin.product-reviews.index')"
            :active="request()->routeIs('admin.product-reviews.*')"
            :expanded="true"
            label="Product Reviews"
        >
            <x-slot:icon>
                <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                </svg>
            </x-slot:icon>
        </x-admin::nav-item>

        <div x-show="expanded" class="my-2 border-t border-zinc-200 dark:border-zinc-700"></div>

        {{-- Website Group --}}
        <div x-show="expanded" class="px-3 py-1.5">
            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">{{ __('Website') }}</p>
        </div>

        <x-admin::nav-item
            :route="route('admin.settings.index')"
            :active="request()->routeIs('admin.settings.*')"
            :expanded="true"
            label="Settings"
        >
            <x-slot:icon>
                <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </x-slot:icon>
        </x-admin::nav-item>
    </nav>

    {{-- Bottom: User Profile --}}
    <div class="border-t border-zinc-200 dark:border-zinc-700 p-3">
        <div
            x-data="{ profileOpen: false }"
            class="relative"
        >
            <button
                x-on:click="profileOpen = !profileOpen"
                class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-700/50 transition-colors"
                :class="expanded ? '' : 'justify-center'"
            >
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-zinc-200 text-xs font-semibold text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300">
                    {{ $user->initials() }}
                </span>
                <template x-if="expanded">
                    <span class="flex-1 text-left truncate">{{ $user->name }}</span>
                </template>
                <svg x-show="expanded" class="size-4 shrink-0 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            {{-- Profile Dropdown --}}
            <div
                x-show="profileOpen"
                x-on:click.outside="profileOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute bottom-full left-0 right-0 z-50 mb-2 rounded-xl border border-zinc-200 bg-white p-1.5 shadow-lg dark:border-zinc-700 dark:bg-zinc-900"
            >
                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                >
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    {{ __('Profile') }}
                </a>

                <div class="my-1 border-t border-zinc-100 dark:border-zinc-700"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/50"
                    >
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                        {{ __('Logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
