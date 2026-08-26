<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    @if (count($cart) > 0)
        <div class="grid gap-8 lg:grid-cols-12">
            {{-- Cart Items List (8 cols) --}}
            <div class="lg:col-span-8">
                <div class="overflow-hidden rounded-3xl border border-wood-border/60 bg-white shadow-sm">
                    <div class="divide-y divide-wood-border/40">
                        @foreach ($cart as $key => $item)
                            <div wire:key="cart-row-{{ $item['product_id'] ?? $key }}" class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between transition-opacity duration-200">
                                {{-- Image + Title --}}
                                <div class="flex items-center gap-4">
                                    <div class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-wood-light/40 border border-wood-border/40">
                                        @if (!empty($item['thumbnail']))
                                            <img src="{{ asset('storage/' . $item['thumbnail']) }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-wood-muted">
                                                <svg class="h-8 w-8 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-wood-primary">{{ $item['category_name'] ?? 'Bewole' }}</span>
                                        <h3 class="text-base font-semibold text-wood-text">
                                            <a href="{{ route('products.show', $item['slug']) }}" class="hover:text-wood-primary transition-colors">
                                                {{ $item['name'] }}
                                            </a>
                                        </h3>
                                        <p class="mt-0.5 text-xs text-wood-muted">Harga: {{ $item['formatted_price'] }}</p>
                                    </div>
                                </div>

                                {{-- Quantity Selector & Subtotal --}}
                                <div class="flex items-center justify-between sm:justify-end gap-6 pt-3 sm:pt-0 border-t sm:border-t-0 border-wood-border/30">
                                    {{-- Quantity controls --}}
                                    <div class="flex items-center rounded-full border border-wood-border/60 bg-wood-light/20 p-1">
                                        <button
                                            type="button"
                                            wire:click="updateQuantity({{ (int) $item['product_id'] }}, {{ (int) $item['quantity'] - 1 }})"
                                            wire:loading.attr="disabled"
                                            class="flex h-7 w-7 items-center justify-center rounded-full text-wood-text hover:bg-white hover:text-wood-primary transition-all disabled:opacity-50"
                                            aria-label="Kurangi jumlah"
                                        >
                                            -
                                        </button>
                                        <span class="w-8 text-center text-xs font-bold text-wood-text">{{ $item['quantity'] }}</span>
                                        <button
                                            type="button"
                                            wire:click="updateQuantity({{ (int) $item['product_id'] }}, {{ (int) $item['quantity'] + 1 }})"
                                            wire:loading.attr="disabled"
                                            class="flex h-7 w-7 items-center justify-center rounded-full text-wood-text hover:bg-white hover:text-wood-primary transition-all disabled:opacity-50"
                                            aria-label="Tambah jumlah"
                                        >
                                            +
                                        </button>
                                    </div>

                                    {{-- Subtotal item --}}
                                    <div class="text-right">
                                        <span class="text-[10px] uppercase text-wood-muted block">Subtotal</span>
                                        <span class="text-sm font-bold text-wood-primary">
                                            Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                        </span>
                                    </div>

                                    {{-- Delete Button --}}
                                    <button
                                        type="button"
                                        wire:click="removeItem({{ (int) $item['product_id'] }})"
                                        wire:loading.attr="disabled"
                                        class="text-wood-muted hover:text-rose-600 transition-colors p-1 disabled:opacity-50"
                                        title="Hapus Produk"
                                        aria-label="Hapus produk"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-wood-primary hover:underline">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Lanjutkan Belanja
                    </a>
                </div>
            </div>

            {{-- Summary Sidebar (4 cols) --}}
            <div class="lg:col-span-4">
                <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-md shadow-wood-primary/5">
                    <h2 class="text-lg font-bold text-wood-text border-b border-wood-border/40 pb-4">Ringkasan Pesanan</h2>

                    <div class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between text-wood-muted">
                            <span>Total Item</span>
                            <span class="font-medium text-wood-text">{{ count($cart) }} macam</span>
                        </div>
                        <div class="flex justify-between text-wood-muted">
                            <span>Estimasi Subtotal</span>
                            <span class="font-bold text-wood-text">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-wood-border/40 pt-4">
                        <div class="flex justify-between text-base font-bold text-wood-text">
                            <span>Total Pembayaran</span>
                            <span class="text-lg text-wood-primary">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <p class="mt-1.5 text-[11px] leading-tight text-wood-muted">
                            * Total belum termasuk <span class="font-semibold text-rose-500">ongkos kirim dan biaya packing</span> (serta biaya kustom khusus jika ada). Biaya ini akan diinformasikan saat konfirmasi pesanan.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="proceedToCheckout"
                        class="mt-6 w-full rounded-2xl bg-wood-primary py-3.5 text-center text-sm font-semibold text-white shadow-lg shadow-wood-primary/25 transition-all hover:bg-wood-primary-dark hover:-translate-y-0.5 active:translate-y-0"
                    >
                        Lanjut ke Pembayaran (Checkout)
                    </button>
                </div>
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="my-16 flex flex-col items-center justify-center rounded-3xl border border-wood-border/60 bg-white/70 p-12 text-center shadow-sm backdrop-blur-md">
            <div class="flex h-24 w-24 items-center justify-center rounded-full bg-wood-light/60 text-wood-muted">
                <svg class="h-12 w-12 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <h2 class="mt-6 text-xl font-bold tracking-tight text-wood-text uppercase">KERANJANG ANDA KOSONG</h2>
            <p class="mt-2 max-w-md text-sm text-wood-muted">Jelajahi produk kerajinan kayu premium kami dan temukan furnitur terbaik yang dirancang khusus untuk ruangan Anda.</p>
            <a
                href="{{ route('products.index') }}"
                class="mt-6 rounded-full bg-wood-primary px-8 py-3.5 text-xs font-semibold uppercase tracking-wider text-white shadow-lg shadow-wood-primary/25 transition-all hover:bg-wood-primary-dark hover:-translate-y-0.5"
            >
                Jelajahi Produk
            </a>
        </div>
    @endif
</div>
