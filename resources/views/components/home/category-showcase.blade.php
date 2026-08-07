@php
    $linkBase = '/' . trim($baseUrl, '/') . '?category=';
@endphp

@if ($categories->isNotEmpty())
<section
    id="collection"
    aria-labelledby="collection-heading"
    class="relative overflow-hidden bg-wood-bg py-20 sm:py-24 lg:py-28"
>
    {{-- Ambient background accents (premium, subtle) --}}
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
        <div class="animate-blob absolute -top-32 -right-32 h-96 w-96 rounded-full bg-wood-secondary/15 blur-3xl"></div>
        <div class="animate-blob absolute -bottom-40 -left-32 h-[28rem] w-[28rem] rounded-full bg-wood-primary/10 blur-3xl" style="animation-delay: 4s;"></div>
    </div>

    <div class="relative mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- ============================================================
             HEADING (fade-up via IntersectionObserver, tanpa AOS)
             ============================================================ --}}
        <div class="mx-auto max-w-2xl text-center">
            <span
                data-reveal
                class="mb-5 inline-flex items-center gap-2 rounded-full border border-wood-primary/15 bg-wood-primary/5 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-wood-primary"
            >
                <span class="text-wood-secondary">✦</span>
                {{ $badge }}
            </span>

            <h2
                id="collection-heading"
                data-reveal
                data-reveal-delay="100"
                class="font-serif text-3xl font-bold tracking-tight text-wood-text sm:text-4xl lg:text-5xl"
            >
                {{ $title }}
            </h2>

            @if ($subtitle)
                <p data-reveal data-reveal-delay="200" class="mx-auto mt-5 max-w-xl text-base leading-relaxed text-wood-muted sm:text-lg">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        {{-- ============================================================
             GRID — Desktop/Tablet : grid interaktif (Apple-like flex-grow)
             Mobile              : horizontal scroll + scroll-snap
             ============================================================ --}}
        <div x-data="categoryShowcase" class="mt-14 lg:mt-20">
            {{-- Desktop / Tablet : kartu membesar via flex-grow saat hover --}}
            <div
                x-show="!isMobile"
                x-cloak
                @mouseleave="active = null"
                class="category-showcase-desktop hidden flex-wrap items-stretch gap-5 md:flex"
                role="list"
            >
                @foreach ($categories as $category)
                    <div
                        data-reveal
                        data-reveal-delay="{{ $loop->index * 120 }}"
                        x-bind:style="wrapperStyle({{ $loop->index }})"
                        @mouseenter="active = {{ $loop->index }}"
                        class="min-h-[420px] min-w-0 flex-1 basis-0 lg:min-h-[480px]"
                    >
                        <a
                            role="listitem"
                            href="{{ $linkBase . $category->slug }}"
                            x-bind:class="cardClasses({{ $loop->index }})"
                            @focus="active = {{ $loop->index }}"
                            @blur="active = null"
                            class="group relative block h-[420px] w-full cursor-pointer overflow-hidden rounded-[2rem] shadow-sm transition-all duration-500 ease-in-out lg:h-[480px]"
                            aria-label="{{ $category->name }}"
                        >
{{-- Gambar kategori (object-cover, zoom halus saat hover) --}}
                            @if ($category->cover_image_url)
                                <img
                                    src="{{ $category->cover_image_url }}"
                                    alt="{{ $category->name }}"
                                    loading="lazy"
                                    class="absolute inset-0 h-full w-full scale-100 object-cover object-center transition-all duration-700 ease-in-out group-hover:scale-[1.08]"
                                >
                            @else
                                <div
                                    class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-wood-primary/80 to-wood-primary-dark"
                                    aria-hidden="true"
                                >
                                    <span class="font-serif text-5xl font-bold text-white/25">{{ strtoupper(mb_substr($category->name, 0, 1)) }}</span>
                                </div>
                            @endif

                            {{-- Overlay tipis → gelap saat hover --}}
                            <div
                                class="absolute inset-0 bg-black/35 transition-colors duration-500 ease-in-out group-hover:bg-black/60"
                                aria-hidden="true"
                            ></div>

                            {{-- Bottom gradient untuk readability --}}
                            <div class="absolute inset-x-0 bottom-0 h-3/5 bg-gradient-to-t from-black/70 via-black/25 to-transparent opacity-90 transition-opacity duration-500 group-hover:opacity-100" aria-hidden="true"></div>

                            {{-- Konten: nama kategori, deskripsi, tombol --}}
                            <div class="relative z-10 w-full p-7 sm:p-8 lg:p-10">
                                <h3 class="font-serif text-2xl font-bold tracking-tight text-white transition-all duration-500 ease-in-out group-hover:-translate-y-2 sm:text-3xl">
                                    {{ $category->name }}
                                </h3>

@if ($category->short_description)
                                    <p class="mt-2 max-w-md translate-y-3 text-sm leading-relaxed text-white/85 opacity-0 transition-all delay-75 duration-500 ease-in-out group-hover:translate-y-0 group-hover:opacity-100 line-clamp-2 sm:text-base">
                                        {{ $category->short_description }}
                                    </p>
                                @endif

                                <span
                                    class="mt-5 inline-flex translate-y-3 items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-wood-primary opacity-0 shadow-lg shadow-black/20 transition-all delay-100 duration-500 ease-in-out group-hover:translate-y-0 group-hover:opacity-100"
                                >
                                    Explore Collection
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                    </svg>
                                </span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            {{-- Mobile : horizontal scroll + scroll-snap, kartu tengah lebih besar --}}
            <div
                x-show="isMobile"
                x-cloak
                x-ref="mobileScroller"
                @scroll.passive="onMobileScroll($refs.mobileScroller)"
                x-init="$nextTick(() => onMobileScroll($refs.mobileScroller))"
                data-reveal
                class="category-scroll no-scrollbar -mx-4 flex snap-x snap-mandatory gap-5 overflow-x-auto px-4 pb-4 sm:-mx-6 sm:px-6 md:hidden"
                role="list"
            >
                @foreach ($categories as $category)
                    <a
                        role="listitem"
                        href="{{ $linkBase . $category->slug }}"
                        class="relative flex h-[460px] w-[85%] shrink-0 snap-center items-end justify-start overflow-hidden rounded-[2rem] shadow-lg shadow-black/10 transition-all duration-500 ease-in-out"
                        aria-label="{{ $category->name }}"
                    >
@if ($category->cover_image_url)
                            <img
                                src="{{ $category->cover_image_url }}"
                                alt="{{ $category->name }}"
                                loading="lazy"
                                class="absolute inset-0 h-full w-full object-cover object-center transition-transform duration-700 ease-in-out"
                            >
                        @else
                            <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-wood-primary/80 to-wood-primary-dark" aria-hidden="true">
                                <span class="font-serif text-5xl font-bold text-white/25">{{ strtoupper(mb_substr($category->name, 0, 1)) }}</span>
                            </div>
                        @endif

                        <div data-overlay class="absolute inset-0 bg-black/35 transition-colors duration-500" aria-hidden="true"></div>
                        <div class="absolute inset-x-0 bottom-0 h-3/5 bg-gradient-to-t from-black/70 via-black/25 to-transparent" aria-hidden="true"></div>

                        <div class="relative z-10 w-full p-7 sm:p-8">
                            <h3 class="font-serif text-2xl font-bold tracking-tight text-white">
                                {{ $category->name }}
                            </h3>
@if ($category->short_description)
                                <p class="mt-2 max-w-md line-clamp-2 text-sm leading-relaxed text-white/85">
                                    {{ $category->short_description }}
                                </p>
                            @endif
                            <span class="mt-5 inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-wood-primary shadow-lg shadow-black/20">
                                Explore Collection
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                </svg>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
