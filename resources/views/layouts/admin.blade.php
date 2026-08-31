<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

@php
        $siteName = App\Helpers\WebsiteSettings::siteName();
        $siteLogo = App\Helpers\WebsiteSettings::logoUrl();
    @endphp

    <title>
        {{ filled($title ?? null) ? $title . ' — ' . $siteName : $siteName . ' — Admin' }}
    </title>

    @if ($siteLogo)
        <link rel="icon" href="{{ $siteLogo }}">
    @else
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @endif

@fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @fluxAppearance
</head>
<body x-data="{ mobileOpen: false }" x-on:toggle-mobile-sidebar.window="mobileOpen = !mobileOpen" class="bg-bg-primary font-sans text-text-primary antialiased">

    {{-- ============================================
         MOBILE SIDEBAR OVERLAY
         ============================================ --}}
    <div x-show="mobileOpen"
         class="fixed inset-0 z-40 lg:hidden">
        <div x-show="mobileOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm"
             x-on:click="mobileOpen = false">
        </div>

        <div x-show="mobileOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 z-50 flex w-[280px] flex-col bg-sidebar shadow-2xl">
            <div class="flex h-[72px] items-center justify-between border-b border-white/10 px-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10">
                        <span class="text-sm font-bold text-white">B</span>
                    </div>
                    <span class="text-base font-semibold tracking-tight text-sidebar-text">Bewole Jepara Furniture</span>
                </a>
                <button x-on:click="mobileOpen = false" class="rounded-lg p-1.5 text-sidebar-text hover:bg-white/10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <nav x-on:click="if ($event.target.closest('a')) mobileOpen = false" class="sidebar-scroll flex-1 space-y-1 overflow-y-auto px-4 py-6">
                @include('partials.admin-sidebar-menu')
            </nav>
        </div>
    </div>

    {{-- ============================================
         MAIN CONTAINER
         ============================================ --}}
    <div class="flex h-screen w-full min-w-0 overflow-hidden">

        {{-- DESKTOP SIDEBAR --}}
        <aside class="hidden w-[280px] shrink-0 lg:flex lg:flex-col">
            <div class="flex h-full flex-col bg-sidebar shadow-2xl">
                <div class="flex h-[72px] items-center border-b border-white/10 px-6">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary shadow-lg">
                            <span class="text-lg font-bold text-white">B</span>
                        </div>
                        <div>
                            <span class="text-base font-bold tracking-tight text-white">Bewole Jepara Furniture</span>
                            <span class="block text-[10px] font-medium uppercase tracking-[0.2em] text-sidebar-text">Administrator</span>
                        </div>
                    </a>
                </div>

                <nav class="sidebar-scroll flex-1 space-y-1 overflow-y-auto px-4 py-6">
                    @include('partials.admin-sidebar-menu')
                </nav>

                <div class="border-t border-white/10 px-4 py-4">
                    <div x-data="{ open: false }" class="relative">
                        <button x-on:click="open = !open"
                                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-sidebar-text hover:bg-sidebar-hover hover:text-sidebar-text-hover transition-all duration-200">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-white shadow-sm">
                                {{ auth()->user()->initials() }}
                            </span>
                            <span class="flex-1 truncate text-left">{{ auth()->user()->name }}</span>
                            <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute bottom-full left-0 right-0 mb-2 rounded-xl border border-white/10 bg-sidebar-hover p-1 shadow-xl">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-red-300 hover:bg-white/10">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                                    </svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- MAIN CONTENT AREA --}}
        <div class="flex flex-1 flex-col min-w-0 overflow-hidden">

            {{-- Top Navbar --}}
            <header class="flex h-16 shrink-0 items-center justify-between border-b border-border bg-card px-4 shadow-xs lg:px-6">
                <div class="flex items-center gap-3 min-w-0">
                    <button x-on:click="window.dispatchEvent(new CustomEvent('toggle-mobile-sidebar'))"
                            class="flex items-center justify-center rounded-lg p-2 text-text-secondary hover:bg-bg-secondary lg:hidden">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                        </svg>
                    </button>
                    <div class="hidden lg:block">
                        <h2 class="text-sm font-semibold text-text-primary">{{ $title ?? 'Dashboard' }}</h2>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-sm text-text-secondary hover:bg-bg-secondary transition-colors">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <span class="hidden text-xs text-text-muted sm:inline">Cari...</span>
                    </button>

                    <button class="relative flex items-center justify-center rounded-lg p-2 text-text-secondary hover:bg-bg-secondary transition-colors">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                        </svg>
                    </button>

                    <div x-data="{ profileOpen: false }" class="relative">
                        <button x-on:click="profileOpen = !profileOpen"
                                class="flex items-center gap-2 rounded-lg p-1.5 text-text-secondary hover:bg-bg-secondary transition-colors">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-xs font-bold text-white shadow-sm shrink-0">
                                {{ auth()->user()->initials() }}
                            </span>
                            <svg class="hidden h-4 w-4 sm:block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </button>

                        <div x-show="profileOpen"
                             x-on:click.away="profileOpen = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 top-full z-50 mt-2 w-56 origin-top-right rounded-xl border border-border bg-card p-1 shadow-lg">
                            <div class="border-b border-border px-3 py-2">
                                <p class="text-sm font-medium text-text-primary truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-text-secondary truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950">
                                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                                    </svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto min-w-0 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="border-t border-border bg-card px-4 py-3 sm:px-6 shrink-0">
                <div class="flex flex-col gap-1.5 text-center text-xs text-text-muted sm:flex-row sm:items-center sm:justify-between sm:text-left">
                    <p class="leading-relaxed">&copy; {{ date('Y') }} {{ config('app.name', 'Bewole Jepara Furniture') }}. Hak Cipta Dilindungi.</p>
                    <p class="shrink-0 font-medium">Panel Admin v1.0</p>
                </div>
            </footer>
        </div>
    </div>

    @stack('scripts')
    @fluxScripts
</body>
</html>
