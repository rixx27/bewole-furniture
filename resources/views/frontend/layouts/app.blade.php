@php
    $siteName = App\Helpers\WebsiteSettings::siteName();
    $siteLogo = App\Helpers\WebsiteSettings::logoUrl();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

<title>
        @hasSection('title')
            @yield('title') — {{ $siteName }}
        @else
            {{ $siteName }}
        @endif
    </title>
    <meta name="description" content="{{ App\Helpers\WebsiteSettings::get('site_tagline') ?? $siteName }}">

    @if ($siteLogo)
        <link rel="icon" href="{{ $siteLogo }}">
    @else
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @endif

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/frontend.js'])
    @livewireStyles
</head>
<body class="flex min-h-screen flex-col bg-wood-bg font-sans text-wood-text antialiased">
    @include('frontend.partials.navbar')

<main class="flex-1">
        @yield('content')
    </main>

    @include('frontend.partials.footer')

    {{-- Global Realtime Toast Notification --}}
    <div
        x-data="{
            show: false,
            type: 'success',
            message: '',
            productName: '',
            productThumbnail: '',
            productPrice: '',
            productQuantity: 1,
            cartCount: 0,
            timer: null,
            notify(detail) {
                if (!detail) return;
                
                // Extract detail safely whether passed as object, array, or string
                const payload = (detail && typeof detail === 'object' && !Array.isArray(detail)) 
                    ? detail 
                    : (Array.isArray(detail) && detail[0] ? detail[0] : { message: String(detail) });

                this.message = payload.message || 'Operasi berhasil.';
                this.type = payload.type || 'success';
                this.productName = payload.product_name || '';
                this.productThumbnail = payload.product_thumbnail || '';
                this.productPrice = payload.product_price || '';
                this.productQuantity = payload.product_quantity || 1;
                this.cartCount = payload.cart_count || 0;

                this.show = true;

                if (this.timer) clearTimeout(this.timer);
                this.timer = setTimeout(() => {
                    this.show = false;
                }, 4500);
            },
            close() {
                this.show = false;
                if (this.timer) clearTimeout(this.timer);
            }
        }"
        x-on:notify.window="notify($event.detail)"
        x-show="show"
        x-cloak
        x-transition:enter="transition ease-out duration-400"
        x-transition:enter-start="opacity-0 translate-y-6 sm:translate-y-8 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-250"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="fixed bottom-6 right-4 left-4 sm:left-auto sm:right-6 sm:max-w-md z-50 overflow-hidden rounded-3xl border border-white/20 bg-wood-primary/95 text-white shadow-2xl shadow-wood-primary/40 backdrop-blur-xl p-4 sm:p-4.5 transition-all"
        style="box-shadow: 0 20px 40px -15px rgba(44, 24, 16, 0.45);"
        role="status"
        aria-live="polite"
    >
        <div class="flex items-start gap-3.5">
            {{-- Product Image Thumbnail or Status Icon --}}
            <template x-if="productThumbnail">
                <div class="h-12 w-12 shrink-0 overflow-hidden rounded-2xl border border-white/20 bg-black/20 shadow-inner">
                    <img :src="productThumbnail" :alt="productName" class="h-full w-full object-cover">
                </div>
            </template>
            <template x-if="!productThumbnail">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </template>

            {{-- Text & Details --}}
            <div class="flex-1 min-w-0 pr-1">
                <div class="flex items-center gap-1.5 mb-0.5">
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-amber-300">
                        <span>✓</span> Ditambahkan ke Keranjang
                    </span>
                </div>

                <template x-if="productName">
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-white leading-snug line-clamp-1" x-text="productName"></h4>
                        <div class="mt-0.5 flex items-center gap-2 text-[11px] text-white/80">
                            <span class="font-medium text-amber-200" x-text="productPrice"></span>
                            <span class="text-white/40">•</span>
                            <span>Qty: <span class="font-semibold text-white" x-text="productQuantity"></span></span>
                        </div>
                    </div>
                </template>

                <template x-if="!productName">
                    <p class="text-xs sm:text-sm font-medium text-white/95 leading-relaxed" x-text="message"></p>
                </template>

                {{-- Action Links --}}
                <div class="mt-2.5 flex items-center gap-2">
                    <a
                        href="{{ route('cart.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-full bg-white px-3.5 py-1.5 text-[11px] font-bold uppercase tracking-wider text-wood-primary shadow-sm transition-all duration-200 hover:bg-amber-100 hover:scale-105 active:scale-95"
                    >
                        <svg class="h-3.5 w-3.5 text-wood-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span>Lihat Keranjang</span>
                    </a>
                </div>
            </div>

            {{-- Close Button --}}
            <button
                type="button"
                @click="close()"
                class="shrink-0 rounded-full p-1 text-white/60 hover:text-white hover:bg-white/10 transition-colors"
                aria-label="Tutup notifikasi"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Back To Top --}}
    <button
        type="button"
        x-data="{ visible: false }"
        x-init="
            const onScroll = () => visible = window.scrollY > 500;
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        "
        x-show="visible"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-4 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-4 opacity-0"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-6 right-6 z-40 flex h-12 w-12 items-center justify-center rounded-full bg-wood-primary text-white shadow-lg shadow-wood-primary/30 transition-transform duration-300 hover:-translate-y-1 hover:bg-wood-primary-dark"
        aria-label="Kembali ke atas"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 15.75l7.5-7.5 7.5 7.5"/>
        </svg>
    </button>

    @livewireScripts
    @stack('scripts')
</body>
</html>
