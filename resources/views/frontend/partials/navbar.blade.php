@php
    $siteName = App\Helpers\WebsiteSettings::siteName();
    $siteLogo = App\Helpers\WebsiteSettings::logoUrl();
    $routeName = request()->route()?->getName();
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
                ['label' => 'Home', 'route' => 'home', 'hash' => '#home'],
                ['label' => 'Tentang Kami', 'hash' => '#about'],
                ['label' => 'Produk', 'hash' => '#produk'],
                ['label' => 'FAQ', 'hash' => '#faq'],
                ['label' => 'Kontak', 'hash' => '#kontak'],
            ] as $item)
                <a
                    href="{{ isset($item['route']) ? route($item['route']) : url($item['hash']) }}"
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

        {{-- Login + Hamburger --}}
        <div class="flex items-center gap-2">
            <a
                href="{{ route('login') }}"
                class="hidden items-center gap-2 rounded-full bg-wood-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-wood-primary/20 transition-all duration-300 hover:-translate-y-0.5 hover:bg-wood-primary-dark sm:inline-flex"
            >
                Login
            </a>

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
                ['label' => 'Home', 'route' => 'home', 'hash' => '#home'],
                ['label' => 'Tentang Kami', 'hash' => '#about'],
                ['label' => 'Produk', 'hash' => '#produk'],
                ['label' => 'FAQ', 'hash' => '#faq'],
                ['label' => 'Kontak', 'hash' => '#kontak'],
            ] as $item)
                <a
                    href="{{ isset($item['route']) ? route($item['route']) : url($item['hash']) }}"
                    @click="mobileOpen = false"
                    class="rounded-2xl px-4 py-3 text-sm font-medium transition-colors duration-200"
                    :class="{{ $routeName === ($item['route'] ?? null) ? 'bg-wood-primary/10 text-wood-primary' : 'text-wood-text hover:bg-wood-primary/5' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach

            <a
                href="{{ route('login') }}"
                @click="mobileOpen = false"
                class="mt-2 inline-flex items-center justify-center rounded-full bg-wood-primary px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-wood-primary/20 transition-all duration-300 hover:bg-wood-primary-dark"
            >
                Login
            </a>
        </div>
    </div>
</header>
