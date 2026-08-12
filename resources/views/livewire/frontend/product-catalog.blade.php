<div>
    {{-- Search & Filter Section --}}
    <div class="mb-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        {{-- Category Pills --}}
        <div class="flex flex-wrap items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            <button
                type="button"
                wire:click="selectCategory('')"
                class="rounded-full px-5 py-2.5 text-xs font-semibold uppercase tracking-wider transition-all duration-300 {{ empty($selectedCategory) ? 'bg-wood-primary text-white shadow-md shadow-wood-primary/20' : 'border border-wood-border/60 bg-white/60 text-wood-muted hover:border-wood-primary hover:text-wood-primary' }}"
            >
                Semua Produk
            </button>
            @foreach ($categories as $cat)
                <button
                    type="button"
                    wire:click="selectCategory('{{ $cat->slug }}')"
                    class="rounded-full px-5 py-2.5 text-xs font-semibold uppercase tracking-wider transition-all duration-300 {{ $selectedCategory === $cat->slug ? 'bg-wood-primary text-white shadow-md shadow-wood-primary/20' : 'border border-wood-border/60 bg-white/60 text-wood-muted hover:border-wood-primary hover:text-wood-primary' }}"
                >
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>

        {{-- Search Input & Sort Selector --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            {{-- Search Input --}}
            <div class="relative min-w-[260px]">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari produk kayu..."
                    class="w-full rounded-full border border-wood-border/60 bg-white/80 py-2.5 pl-11 pr-4 text-xs font-medium text-wood-text placeholder-wood-muted shadow-sm transition-all focus:border-wood-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                />
                <svg class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-wood-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            {{-- Sort Dropdown --}}
            <div class="shrink-0">
                <select
                    wire:model.live="sort"
                    class="w-full rounded-full border border-wood-border/60 bg-white/80 py-2.5 px-4 text-xs font-medium text-wood-text shadow-sm transition-all focus:border-wood-primary focus:bg-white focus:outline-none"
                >
                    <option value="latest">Terbaru</option>
                    <option value="price_low">Harga: Terendah</option>
                    <option value="price_high">Harga: Tertinggi</option>
                    <option value="name">Nama: A - Z</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Notification Banner (alpine/livewire event) --}}
    <div
        x-data="{ show: false, message: '' }"
        x-on:notify.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="fixed bottom-6 right-6 z-50 rounded-2xl bg-wood-primary px-5 py-3.5 text-xs font-semibold text-white shadow-2xl shadow-wood-primary/40 flex items-center gap-3"
    >
        <svg class="h-5 w-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span x-text="message"></span>
    </div>

    {{-- Product Grid --}}
    @if ($products->count() > 0)
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($products as $product)
                <div class="group relative flex flex-col overflow-hidden rounded-3xl border border-wood-border/50 bg-white shadow-md shadow-wood-primary/5 transition-all duration-500 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-wood-primary/15">
                    {{-- Image Container --}}
                    <a href="{{ route('products.show', $product) }}" class="relative block aspect-[4/3] overflow-hidden bg-wood-light/40">
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
                        <a href="{{ route('products.show', $product) }}" class="group-hover:text-wood-primary transition-colors">
                            <h3 class="text-base font-semibold text-wood-text line-clamp-1">{{ $product->name }}</h3>
                        </a>

                        @if ($product->short_description)
                            <p class="mt-1 text-xs text-wood-muted line-clamp-2">{{ $product->short_description }}</p>
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
                                    title="Tambah ke Keranjang"
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-wood-primary/10 text-wood-primary transition-all duration-300 hover:bg-wood-primary hover:text-white"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </button>
                                <a
                                    href="{{ route('products.show', $product) }}"
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
                wire:click="$set('search', ''); $set('selectedCategory', '');"
                class="mt-5 rounded-full bg-wood-primary px-6 py-2.5 text-xs font-semibold text-white shadow-md shadow-wood-primary/20 transition-all hover:bg-wood-primary-dark"
            >
                Reset Filter
            </button>
        </div>
    @endif
</div>
