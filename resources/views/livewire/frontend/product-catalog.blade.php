<div>
    {{-- Search & Filter Section --}}
    <div class="mb-10 flex flex-col gap-6">
        {{-- Top Bar: Search Input & Sort Selector --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-wood-muted">Kategori:</span>
                <span class="text-xs font-semibold text-wood-primary">
                    {{ empty($selectedCategory) ? 'Semua Produk' : ($categories->firstWhere('slug', $selectedCategory)?->name ?? 'Semua Produk') }}
                </span>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                {{-- Search Input --}}
                <div class="relative min-w-[260px]">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="q"
                        placeholder="Cari produk kayu..."
                        class="w-full rounded-full border border-wood-border/60 bg-white/80 py-2.5 pl-11 pr-4 text-xs font-medium text-wood-text placeholder-wood-muted shadow-sm transition-colors focus:border-wood-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                    />
                    <svg class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-wood-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                {{-- Sort Dropdown --}}
                <div class="shrink-0">
                    <select
                        wire:model.live="sort"
                        class="w-full rounded-full border border-wood-border/60 bg-white/80 py-2.5 px-4 text-xs font-medium text-wood-text shadow-sm transition-colors focus:border-wood-primary focus:bg-white focus:outline-none"
                    >
                        <option value="latest">Terbaru</option>
                        <option value="price_low">Harga: Terendah</option>
                        <option value="price_high">Harga: Tertinggi</option>
                        <option value="name">Nama: A - Z</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Category Pills (Statis & Rapi: tanpa horizontal scroll, dimensi stabil, tidak bergeser saat dipilih) --}}
        <div class="flex flex-wrap items-center gap-2">
            <button
                type="button"
                wire:click="selectCategory('')"
                class="rounded-full border px-4 py-2 text-xs font-semibold uppercase tracking-wider transition-colors duration-150 {{ empty($selectedCategory) ? 'border-wood-primary bg-wood-primary text-white shadow-sm' : 'border-wood-border/70 bg-white/80 text-wood-muted hover:border-wood-primary hover:text-wood-primary' }}"
            >
                Semua Produk
            </button>
            @foreach ($categories as $cat)
                <button
                    type="button"
                    wire:click="selectCategory('{{ $cat->slug }}')"
                    class="rounded-full border px-4 py-2 text-xs font-semibold uppercase tracking-wider transition-colors duration-150 {{ $selectedCategory === $cat->slug ? 'border-wood-primary bg-wood-primary text-white shadow-sm' : 'border-wood-border/70 bg-white/80 text-wood-muted hover:border-wood-primary hover:text-wood-primary' }}"
                >
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Search Feedback & Category Priority Guidance --}}
    @if (!empty($q))
        <div class="mb-8 rounded-3xl border border-wood-border/60 bg-white/95 p-4 sm:p-5 shadow-sm backdrop-blur-sm">
            <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-wood-text flex flex-wrap items-center gap-2">
                        <span>Hasil pencarian untuk:</span>
                        <span class="rounded-full bg-wood-primary/10 border border-wood-primary/20 px-3 py-0.5 text-wood-primary font-bold">"{{ $q }}"</span>
                        <span class="text-xs font-normal text-wood-muted">({{ $products->total() }} produk ditemukan)</span>
                    </h3>

                    @if (!empty($detectedMaterials))
                        <p class="mt-1 text-xs text-amber-800 flex items-center gap-1.5 font-medium">
                            <svg class="h-3.5 w-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Terdeteksi pencarian bahan: <strong class="underline decoration-amber-400">{{ implode(', ', array_map('ucfirst', $detectedMaterials)) }}</strong>. Produk dengan material ini diprioritaskan di atas.</span>
                        </p>
                    @endif
                </div>

                <button
                    type="button"
                    wire:click="$set('q', '')"
                    class="self-start sm:self-auto inline-flex items-center gap-1.5 rounded-full border border-wood-border/70 px-3 py-1.5 text-xs font-medium text-wood-muted hover:border-wood-primary hover:text-wood-primary hover:bg-wood-light/40 transition-all cursor-pointer"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Hapus Pencarian
                </button>
            </div>

            {{-- Suggested matching categories --}}
            @if ($matchingCategories->isNotEmpty() && empty($selectedCategory))
                <div class="mt-3 pt-3 border-t border-wood-border/40 flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-wood-muted flex items-center gap-1">
                        <svg class="h-3.5 w-3.5 text-wood-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                        Kategori Terkait:
                    </span>
                    @foreach ($matchingCategories as $mCat)
                        <button
                            type="button"
                            wire:click="selectCategory('{{ $mCat->slug }}')"
                            class="inline-flex items-center gap-1.5 rounded-full border border-wood-primary/30 bg-wood-primary/5 px-3 py-1 text-xs font-semibold text-wood-primary hover:bg-wood-primary hover:text-white transition-all shadow-2xs cursor-pointer"
                        >
                            <span>{{ $mCat->name }}</span>
                            <span class="text-[10px] opacity-75 font-normal">({{ $mCat->products_count ?? $mCat->products()->active()->count() }})</span>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- Product Grid --}}
    @if ($products->count() > 0)
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($products as $product)
                <div class="group relative flex flex-col overflow-hidden rounded-3xl border border-wood-border/50 bg-white shadow-md shadow-wood-primary/5 transition-all duration-500 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-wood-primary/15">
                    {{-- Image Container --}}
                    <a href="{{ route('products.show', $product->slug) }}" class="relative block aspect-[4/3] overflow-hidden bg-wood-light/40">
                        @if ($product->thumbnail)
                            <img
                                src="{{ asset('storage/' . $product->thumbnail) }}"
                                alt="{{ $product->name }}"
                                class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                                loading="lazy"
                            />
                        @else
                            <div class="flex h-full w-full items-center justify-center text-wood-muted">
                                <svg class="h-12 w-12 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        @endif

                        {{-- Category Badge --}}
                        <div class="absolute left-3 top-3">
                            <span class="rounded-full bg-white/90 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-wood-primary backdrop-blur-md">
                                {{ $product->category?->name ?? 'Bewole' }}
                            </span>
                        </div>

                        {{-- Discount Badge --}}
                        @if ($product->has_discount)
                            <div class="absolute right-3 top-3">
                                <span class="rounded-full bg-amber-600 px-2.5 py-1 text-[10px] font-bold text-white shadow-sm">
                                    -{{ $product->discount_percentage }}%
                                </span>
                            </div>
                        @endif
                    </a>

                    {{-- Content --}}
                    <div class="flex flex-1 flex-col p-5">
                        <a href="{{ route('products.show', $product->slug) }}" class="group-hover:text-wood-primary transition-colors">
                            <h3 class="text-base font-semibold text-wood-text line-clamp-1">{{ $product->name }}</h3>
                        </a>

                        {{-- Material Indicator Badge --}}
                        @if ($product->material)
                            @php
                                $isMaterialMatch = false;
                                if (!empty($detectedMaterials)) {
                                    foreach ($detectedMaterials as $dm) {
                                        if (stripos($product->material, $dm) !== false) {
                                            $isMaterialMatch = true;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <div class="mt-1.5 flex items-center gap-1.5 flex-wrap">
                                <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[11px] font-medium transition-colors {{ $isMaterialMatch ? 'bg-amber-100 text-amber-900 border border-amber-300 font-semibold shadow-2xs' : 'bg-wood-light/50 text-wood-text/80 border border-wood-border/40' }}">
                                    @if ($isMaterialMatch)
                                        <span class="text-amber-700 font-bold">✨ Bahan:</span>
                                    @else
                                        <svg class="h-3 w-3 text-wood-primary/70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    @endif
                                    <span>{{ $product->material }}</span>
                                </span>
                            </div>
                        @endif

                        @if ($product->short_description)
                            <p class="mt-2 text-xs text-wood-muted line-clamp-2">{{ $product->short_description }}</p>
                        @elseif ($product->description)
                            <p class="mt-2 text-xs text-wood-muted line-clamp-2">{{ Str::limit(strip_tags($product->description), 80) }}</p>
                        @endif

                        <div class="mt-auto pt-4 flex items-center justify-between border-t border-wood-border/40">
                            <div>
                                <span class="text-xs text-wood-muted block">Harga</span>
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-base font-bold text-wood-primary">
                                        {{ $product->formatted_discount_price ?: $product->formatted_price }}
                                    </span>
                                    @if ($product->has_discount)
                                        <span class="text-[11px] text-wood-muted line-through">
                                            {{ $product->formatted_price }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Action Button --}}
                            <div class="flex items-center gap-1.5">
                                <button
                                    type="button"
                                    wire:click="addToCart({{ $product->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="addToCart({{ $product->id }})"
                                    title="Tambah ke Keranjang"
                                    class="group/btn relative flex h-9 w-9 items-center justify-center rounded-full bg-wood-primary/10 text-wood-primary transition-all duration-300 hover:bg-wood-primary hover:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                                >
                                    {{-- Normal Cart Icon --}}
                                    <svg wire:loading.remove wire:target="addToCart({{ $product->id }})" class="h-4 w-4 transition-transform group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    {{-- Loading Spinner --}}
                                    <svg wire:loading wire:target="addToCart({{ $product->id }})" class="h-4 w-4 animate-spin text-wood-primary group-hover/btn:text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                </button>
                                <a
                                    href="{{ route('products.show', $product->slug) }}"
                                    class="flex h-9 w-9 items-center justify-center rounded-full border border-wood-border/60 text-wood-muted transition-all duration-300 hover:border-wood-primary hover:text-wood-primary"
                                    title="Lihat Detail"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-12">
            {{ $products->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="my-16 flex flex-col items-center justify-center text-center">
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-wood-light/60 text-wood-muted">
                <svg class="h-10 w-10 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-wood-text">Produk Tidak Ditemukan</h3>
            <p class="mt-1 max-w-sm text-sm text-wood-muted">Maaf, kami tidak dapat menemukan produk yang sesuai dengan pencarian atau filter Anda.</p>
            <button
                type="button"
                wire:click="$set('q', ''); $set('selectedCategory', '');"
                class="mt-5 rounded-full bg-wood-primary px-6 py-2.5 text-xs font-semibold text-white shadow-md shadow-wood-primary/20 transition-all hover:bg-wood-primary-dark"
            >
                Reset Filter
            </button>
        </div>
    @endif
</div>
