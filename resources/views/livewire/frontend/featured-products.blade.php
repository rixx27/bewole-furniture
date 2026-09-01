<section
    id="products"
    aria-labelledby="featured-products-heading"
    class="relative overflow-hidden bg-wood-surface py-20 sm:py-24 lg:py-28"
>
    {{-- Ambient background accents (premium, subtle) --}}
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
        <div class="animate-blob absolute -top-32 -right-32 h-96 w-96 rounded-full bg-wood-secondary/15 blur-3xl"></div>
        <div class="animate-blob absolute -bottom-40 -left-32 h-[28rem] w-[28rem] rounded-full bg-wood-primary/10 blur-3xl" style="animation-delay: 4s;"></div>
    </div>

    <div class="relative mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- ============================================================
             HEADING
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
                id="featured-products-heading"
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
             PRODUCT GRID
             Mobile  : 2 kolom
             Tablet  : 3 kolom
             Desktop : 4 kolom
             ============================================================ --}}
        @if ($products->isNotEmpty())
            <div class="mt-14 grid grid-cols-2 gap-4 sm:gap-6 md:grid-cols-3 lg:mt-20 lg:grid-cols-4 lg:gap-8">
                @foreach ($products as $product)
                    <article
                        data-reveal
                        data-reveal-blur
                        data-reveal-delay="{{ $loop->index * 90 }}"
                        class="featured-product-card group flex flex-col overflow-hidden rounded-2xl border border-wood-border/60 bg-white shadow-sm transition-all duration-500 ease-out hover:-translate-y-2 hover:shadow-2xl hover:shadow-wood-primary/15 sm:rounded-3xl"
                    >
                        {{-- Image wrapper --}}
                        <a
                            href="{{ route('products.show', $product->slug) }}"
                            class="relative block aspect-[4/5] w-full overflow-hidden bg-wood-bg"
                            aria-label="{{ $product->name }}"
                        >
                            @if ($product->thumbnail)
                                <img
                                    src="{{ asset('storage/' . $product->thumbnail) }}"
                                    alt="{{ $product->name }}"
                                    loading="lazy"
                                    class="h-full w-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-[1.04]"
                                >
                            @else
                                <div
                                    class="flex h-full w-full items-center justify-center bg-gradient-to-br from-wood-primary/80 to-wood-primary-dark"
                                    aria-hidden="true"
                                >
                                    @php
                                        $initial = strtoupper(mb_substr($product->name, 0, 1));
                                    @endphp
                                    <span class="font-serif text-6xl font-bold text-white/25">{{ $initial }}</span>
                                </div>
                            @endif

                            {{-- Category label (top-left) --}}
                            @if ($product->category)
                                <span class="absolute left-3 top-3 rounded-full bg-white/90 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-wood-primary backdrop-blur-sm sm:left-4 sm:top-4 sm:text-xs">
                                    {{ $product->category->name }}
                                </span>
                            @endif

                            {{-- Discount badge (top-right) --}}
                            @if ($product->has_discount)
                                <span class="absolute right-3 top-3 rounded-full bg-red-600/90 px-3 py-1 text-[10px] font-bold text-white sm:right-4 sm:top-4 sm:text-xs">
                                    -{{ $product->discount_percentage }}%
                                </span>
                            @endif
                        </a>

                        {{-- Product info --}}
                        <div class="flex flex-1 flex-col p-4 sm:p-5">
                            <h3 class="font-serif text-base font-semibold leading-snug text-wood-text sm:text-lg">
                                <a href="{{ route('products.show', $product->slug) }}" class="transition-colors hover:text-wood-primary">
                                    {{ $product->name }}
                                </a>
                            </h3>

                            @if ($product->material)
                                <p class="mt-1 line-clamp-1 text-xs text-wood-muted sm:text-sm">
                                    {{ $product->material }}
                                </p>
                            @endif

                            <div class="mt-auto pt-3 sm:pt-4">
                                @if ($product->has_discount)
                                    <p class="text-sm font-bold text-wood-primary sm:text-lg">{{ $product->formatted_discount_price }}</p>
                                    <p class="text-xs text-wood-muted line-through sm:text-sm">{{ $product->formatted_price }}</p>
                                @else
                                    <p class="text-base font-bold text-wood-primary sm:text-lg">{{ $product->formatted_price }}</p>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="mt-3 flex items-center gap-2 sm:mt-4">
                                <button
                                    type="button"
                                    wire:click="addToCart({{ $product->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="addToCart({{ $product->id }})"
                                    title="Tambah ke Keranjang"
                                    class="group/btn flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-wood-primary/10 text-wood-primary transition-all duration-300 hover:bg-wood-primary hover:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                >
                                    <svg wire:loading.remove wire:target="addToCart({{ $product->id }})" class="h-4 w-4 transition-transform group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <svg wire:loading wire:target="addToCart({{ $product->id }})" class="h-4 w-4 animate-spin text-wood-primary group-hover/btn:text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                </button>

                                <a
                                    href="{{ route('products.show', $product->slug) }}"
                                    class="flex flex-1 items-center justify-center gap-1.5 rounded-full border border-wood-primary/20 bg-wood-primary/5 px-3 py-2 text-xs font-semibold text-wood-primary transition-all duration-300 ease-out hover:bg-wood-primary hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-wood-secondary focus-visible:ring-offset-2 sm:text-sm"
                                >
                                    <span>Detail</span>
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            {{-- Empty state when no active products exist yet --}}
            <div data-reveal class="mt-14 flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-wood-border bg-white/50 px-6 py-16 text-center lg:mt-20">
                <span class="text-wood-secondary">✦</span>
                <h3 class="mt-4 font-serif text-xl font-bold text-wood-text">Koleksi Segera Hadir</h3>
                <p class="mt-2 max-w-md text-sm leading-relaxed text-wood-muted">
                    Kami sedang menyiapkan koleksi furniture unggulan terbaru. Silakan kembali lagi nanti.
                </p>
            </div>
        @endif
    </div>
</section>
