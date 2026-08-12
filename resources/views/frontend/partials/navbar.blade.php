@php
    $siteName = App\Helpers\WebsiteSettings::siteName();
    $siteLogo = App\Helpers\WebsiteSettings::logoUrl();
    $routeName = request()->route()?->getName();
    $cartCount = count(session('bewole_cart', []));
    $cartQty = collect(session('bewole_cart', []))->sum('quantity');
@endphp

<header
    x-data="navbar"
    x-init="init"
    class="fixed inset-x-0 top-0 z-50 px-3 pt-3 sm:px-5 sm:pt-4"
    aria-label="Navigasi utama"
>
    <nav
        class="mx-auto flex w-full max-w-7xl items-center justify-between rounded-full border px-4 py-3 transition-all duration-300 ease-out sm:px-6"
        :class="scrolled
            ? 'border-wood-border/70 bg-white/85 shadow-lg shadow-wood-primary/10 supports-[backdrop-filter]:bg-white/75'
            : 'border-white/40 bg-white/20 shadow-lg shadow-black/5 supports-[backdrop-filter]:bg-white/15'"
        style="backdrop-filter: blur(20px) saturate(160%); -webkit-backdrop-filter: blur(20px) saturate(160%);"
    >
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="{{ $siteName }}">
            @if ($siteLogo)
                <img src="{{ $siteLogo }}" alt="Logo {{ $siteName }}" class="h-9 w-9 rounded-full object-contain">
            @else
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-wood-primary text-sm font-bold text-white shadow-sm">
                    {{ strtoupper(substr($siteName, 0, 1)) }}
                </span>
            @endif
            <span class="hidden text-lg font-bold tracking-tight sm:block"
                  :class="scrolled ? 'text-wood-text' : 'text-white'">{{ $siteName }}</span>
        </a>

        {{-- Desktop Menu --}}
        <div class="hidden items-center gap-1 lg:flex">
            @foreach ([
                ['label' => 'Home', 'route' => 'home'],
                ['label' => 'Produk', 'route' => 'products.index'],
                ['label' => 'Tentang Kami', 'route' => 'frontend.about'],
                ['label' => 'FAQ', 'hash' => '#faq'],
                ['label' => 'Kontak', 'hash' => '#contact'],
            ] as $item)
                <a
                    href="{{ isset($item['route']) ? route($item['route']) : (isset($item['hash']) ? url($item['hash']) : '#') }}"
                    class="group relative rounded-full px-4 py-2 text-sm font-medium transition-colors duration-300"
                    :class="scrolled
                        ? ({{ $routeName === ($item['route'] ?? null) ? "'text-wood-primary'" : "'text-wood-muted hover:text-wood-primary'" }})
                        : ({{ $routeName === ($item['route'] ?? null) ? "'text-white'" : "'text-white/85 hover:text-white'" }})"
                >
                    {{ $item['label'] }}
                    <span
                        class="absolute inset-x-4 -bottom-0.5 h-0.5 origin-left scale-x-0 rounded-full transition-transform duration-300 group-hover:scale-x-100"
                        :class="scrolled ? 'bg-wood-secondary' : 'bg-white'"
                    ></span>
                </a>
            @endforeach
        </div>

        {{-- Right side: Cart + Auth --}}
        <div class="flex items-center gap-2">

            {{-- Cart Icon --}}
            <a
                href="{{ route('cart.index') }}"
                class="relative flex h-9 w-9 items-center justify-center rounded-full transition-all duration-300"
                :class="scrolled ? 'text-wood-text hover:bg-wood-primary/10 hover:text-wood-primary' : 'text-white hover:bg-white/15'"
                title="Keranjang Belanja"
                aria-label="Keranjang Belanja"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                @if ($cartQty > 0)
                    <span class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-amber-500 text-[9px] font-bold leading-none text-white shadow-sm">
                        {{ $cartQty > 99 ? '99+' : $cartQty }}
                    </span>
                @endif
            </a>

            {{-- Auth: Guest / User --}}
            @guest
                <a
                    href="{{ route('login') }}"
                    class="hidden items-center gap-2 rounded-full bg-wood-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-wood-primary/20 transition-all duration-300 hover:-translate-y-0.5 hover:bg-wood-primary-dark sm:inline-flex"
                >
                    Login
                </a>
            @endguest

            @auth
                {{-- User Avatar Dropdown --}}
                <div class="relative hidden sm:block" x-data="{ open: false }">
                    <button
                        type="button"
                        @click="open = !open"
                        @click.away="open = false"
                        class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-bold text-white shadow-md ring-2 ring-white/30 transition-all hover:ring-wood-secondary/50"
                        :class="scrolled ? 'bg-wood-primary' : 'bg-wood-primary/80'"
                        :aria-expanded="open"
                        aria-label="Menu user"
                    >
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </button>

                    <div
                        x-show="open"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 rounded-2xl border border-wood-border/60 bg-white/95 py-2 shadow-xl shadow-wood-primary/10 backdrop-blur-md"
                    >
                        {{-- User info --}}
                        <div class="border-b border-wood-border/40 px-4 pb-2 mb-1">
                            <p class="text-xs font-bold text-wood-text truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-wood-muted truncate">{{ auth()->user()->email }}</p>
                        </div>

                        @if (auth()->user()->hasRole('admin'))
                            <a
                                href="{{ route('admin.dashboard') }}"
                                class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-wood-text hover:bg-wood-primary/5 hover:text-wood-primary transition-colors"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6z" />
                                </svg>
                                Dashboard Admin
                            </a>
                        @endif

                        <a
                            href="{{ route('orders.index') }}"
                            class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-wood-text hover:bg-wood-primary/5 hover:text-wood-primary transition-colors"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Pesanan Saya
                        </a>

                        <div class="border-t border-wood-border/40 mt-1 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="flex w-full items-center gap-2 px-4 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endauth

            {{-- Hamburger --}}
            <button
                type="button"
                @click="mobileOpen = !mobileOpen"
                class="inline-flex items-center justify-center rounded-full p-2.5 transition-colors duration-200 hover:bg-wood-primary/10 lg:hidden"
                :class="scrolled ? 'text-wood-text' : 'text-white'"
                :aria-expanded="mobileOpen"
                aria-label="Buka menu"
            >
                <svg x-show="!mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
                <svg x-show="mobileOpen" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </nav>

    {{-- Mobile Menu (slide down) --}}
    <div
        x-show="mobileOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-y-4 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="-translate-y-4 opacity-0"
        class="mx-auto mt-3 w-full max-w-7xl overflow-hidden rounded-3xl border border-wood-border/70 bg-white/90 shadow-xl shadow-wood-primary/10 supports-[backdrop-filter]:bg-white/80 lg:hidden"
        style="backdrop-filter: blur(20px) saturate(160%); -webkit-backdrop-filter: blur(20px) saturate(160%);"
    >
        <div class="flex flex-col p-3">
            @foreach ([
                ['label' => 'Home', 'route' => 'home'],
                ['label' => 'Produk', 'route' => 'products.index'],
                ['label' => 'Tentang Kami', 'route' => 'frontend.about'],
                ['label' => 'FAQ', 'hash' => '#faq'],
                ['label' => 'Kontak', 'hash' => '#contact'],
            ] as $item)
                <a
                    href="{{ isset($item['route']) ? route($item['route']) : (isset($item['hash']) ? url($item['hash']) : '#') }}"
                    @click="mobileOpen = false"
                    class="rounded-2xl px-4 py-3 text-sm font-medium transition-colors duration-200"
                    :class="{{ $routeName === ($item['route'] ?? null) ? 'bg-wood-primary/10 text-wood-primary' : 'text-wood-text hover:bg-wood-primary/5' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach

            {{-- Cart --}}
            <a
                href="{{ route('cart.index') }}"
                @click="mobileOpen = false"
                class="mt-1 flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-medium text-wood-text hover:bg-wood-primary/5 transition-colors"
            >
                <span>Keranjang</span>
                @if ($cartQty > 0)
                    <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-amber-500 px-1.5 text-[10px] font-bold text-white">
                        {{ $cartQty }}
                    </span>
                @endif
            </a>

            @guest
                <a
                    href="{{ route('login') }}"
                    @click="mobileOpen = false"
                    class="mt-2 inline-flex items-center justify-center rounded-full bg-wood-primary px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-wood-primary/20 transition-all duration-300 hover:bg-wood-primary-dark"
                >
                    Login
                </a>
            @endguest

            @auth
                <a
                    href="{{ route('orders.index') }}"
                    @click="mobileOpen = false"
                    class="mt-2 inline-flex items-center justify-center rounded-full bg-wood-primary px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-wood-primary/20 transition-all duration-300 hover:bg-wood-primary-dark"
                >
                    Pesanan Saya
                </a>

                @if (auth()->user()->hasRole('admin'))
                    <a
                        href="{{ route('admin.dashboard') }}"
                        @click="mobileOpen = false"
                        class="mt-2 inline-flex items-center justify-center rounded-full border border-wood-primary/30 bg-white px-5 py-2.5 text-sm font-semibold text-wood-primary transition-all hover:bg-wood-primary/5"
                    >
                        Dashboard Admin
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="mt-2 w-full">
                    @csrf
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center rounded-full border border-rose-200 px-5 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors"
                    >
                        Keluar
                    </button>
                </form>
            @endauth
        </div>
    </div>
</header>
