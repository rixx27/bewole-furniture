<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    {{-- Notification Toast --}}
    <div
        x-data="{ show: false, message: '' }"
        x-on:notify.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3500)"
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

    <div class="grid gap-12 lg:grid-cols-12">
        {{-- Gallery (6 cols) --}}
        <div class="lg:col-span-6 space-y-4">
            {{-- Main Active Image --}}
            <div class="relative aspect-[4/3] w-full overflow-hidden rounded-3xl border border-wood-border/60 bg-wood-light/40 shadow-sm">
                @if ($selectedImage)
                    <img
                        src="{{ asset('storage/' . $selectedImage) }}"
                        alt="{{ $product->name }}"
                        class="h-full w-full object-cover transition-all duration-300"
                    />
                @else
                    <div class="flex h-full w-full items-center justify-center text-wood-muted">
                        <svg class="h-16 w-16 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                @endif

                @if ($product->has_discount)
                    <div class="absolute right-4 top-4">
                        <span class="rounded-full bg-amber-600 px-3 py-1 text-xs font-bold text-white shadow-md">
                            DISKON {{ $product->discount_percentage }}%
                        </span>
                    </div>
                @endif
            </div>

            {{-- Thumbnail Grid --}}
            @if ($product->images->count() > 0 || $product->thumbnail)
                <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-none">
                    @if ($product->thumbnail)
                        <button
                            type="button"
                            wire:click="selectImage('{{ $product->thumbnail }}')"
                            class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl border-2 transition-all {{ $selectedImage === $product->thumbnail ? 'border-wood-primary ring-2 ring-wood-primary/20' : 'border-wood-border/60 opacity-70 hover:opacity-100' }}"
                        >
                            <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="Thumbnail" class="h-full w-full object-cover">
                        </button>
                    @endif

                    @foreach ($product->images as $img)
                        <button
                            type="button"
                            wire:click="selectImage('{{ $img->image }}')"
                            class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl border-2 transition-all {{ $selectedImage === $img->image ? 'border-wood-primary ring-2 ring-wood-primary/20' : 'border-wood-border/60 opacity-70 hover:opacity-100' }}"
                        >
                            <img src="{{ asset('storage/' . $img->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Product Details Info (6 cols) --}}
        <div class="lg:col-span-6 flex flex-col">
            {{-- Category & Status --}}
            <div class="flex items-center gap-3">
                <span class="rounded-full bg-wood-primary/10 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-wood-primary">
                    {{ $product->category?->name ?? 'Bewole Furniture' }}
                </span>
                <span class="rounded-full px-3.5 py-1 text-xs font-semibold
                    {{ $product->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                    {{ $product->status === 'pre_order' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                    {{ $product->status === 'sold_out' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                ">
                    {{ $product->status_label }}
                </span>
            </div>

            {{-- Title --}}
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-wood-text font-serif lg:text-4xl">
                {{ $product->name }}
            </h1>

            {{-- Price --}}
            <div class="mt-4 flex items-baseline gap-3">
                <span class="text-2xl font-bold text-wood-primary lg:text-3xl">
                    {{ $product->formatted_discount_price ?: $product->formatted_price }}
                </span>
                @if ($product->has_discount)
                    <span class="text-base text-wood-muted line-through">
                        {{ $product->formatted_price }}
                    </span>
                @endif
            </div>

            {{-- Short Description --}}
            @if ($product->short_description)
                <p class="mt-4 text-sm text-wood-muted leading-relaxed">
                    {{ $product->short_description }}
                </p>
            @endif

            {{-- Specifications Table --}}
            <div class="mt-6 rounded-2xl border border-wood-border/50 bg-wood-light/20 p-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-wood-text border-b border-wood-border/40 pb-2 mb-3">Spesifikasi Produk</h3>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    @if ($product->sku)
                        <div>
                            <span class="text-wood-muted block">SKU / Kode</span>
                            <span class="font-medium text-wood-text font-mono">{{ $product->sku }}</span>
                        </div>
                    @endif
                    @if ($product->material)
                        <div>
                            <span class="text-wood-muted block">Material</span>
                            <span class="font-medium text-wood-text">{{ $product->material }}</span>
                        </div>
                    @endif
                    @if ($product->dimensions)
                        <div>
                            <span class="text-wood-muted block">Dimensi (PxLxT)</span>
                            <span class="font-medium text-wood-text">{{ $product->dimensions }}</span>
                        </div>
                    @endif
                    @if ($product->weight)
                        <div>
                            <span class="text-wood-muted block">Estimasi Berat</span>
                            <span class="font-medium text-wood-text">{{ $product->weight }} kg</span>
                        </div>
                    @endif
                    <div>
                        <span class="text-wood-muted block">Ketersediaan Stok</span>
                        <span class="font-medium text-wood-text">{{ $product->stock > 0 ? $product->stock . ' unit' : 'Stok Kosong / Pre-Order' }}</span>
                    </div>
                </div>
            </div>

            {{-- Quantity & Action Controls --}}
            <div class="mt-8 space-y-4">
                <div class="flex items-center gap-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-wood-text">Jumlah:</span>
                    <div class="flex items-center rounded-full border border-wood-border/60 bg-white p-1 shadow-sm">
                        <button
                            type="button"
                            wire:click="decrementQuantity"
                            class="flex h-8 w-8 items-center justify-center rounded-full text-wood-text hover:bg-wood-light transition-all"
                        >
                            -
                        </button>
                        <span class="w-10 text-center text-xs font-bold text-wood-text">{{ $quantity }}</span>
                        <button
                            type="button"
                            wire:click="incrementQuantity"
                            class="flex h-8 w-8 items-center justify-center rounded-full text-wood-text hover:bg-wood-light transition-all"
                        >
                            +
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <button
                        type="button"
                        wire:click="addToCart"
                        class="rounded-2xl border-2 border-wood-primary bg-white py-3.5 text-center text-xs font-bold uppercase tracking-wider text-wood-primary transition-all hover:bg-wood-primary/5 hover:shadow-md flex items-center justify-center gap-2"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Tambah ke Keranjang
                    </button>

                    <button
                        type="button"
                        wire:click="buyNow"
                        class="rounded-2xl bg-wood-primary py-3.5 text-center text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-wood-primary/25 transition-all hover:bg-wood-primary-dark hover:-translate-y-0.5"
                    >
                        Beli Sekarang
                    </button>
                </div>
            </div>

            {{-- Full Description --}}
            @if ($product->description)
                <div class="mt-10 border-t border-wood-border/40 pt-6">
                    <h3 class="text-base font-bold text-wood-text font-serif mb-3">Deskripsi Lengkap</h3>
                    <div class="prose prose-sm text-wood-muted max-w-none leading-relaxed">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
