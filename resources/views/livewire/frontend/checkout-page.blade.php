<div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center gap-2 text-xs text-wood-muted">
        <a href="{{ route('home') }}" class="hover:text-wood-primary">Home</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-wood-primary">Produk</a>
        <span>/</span>
        <a href="{{ route('cart.index') }}" class="hover:text-wood-primary">Keranjang</a>
        <span>/</span>
        <span class="font-semibold text-wood-text">Checkout</span>
    </nav>

    <h1 class="text-3xl font-bold tracking-tight text-wood-text font-serif">Formulir Pembelian</h1>
    <p class="mt-1 text-sm text-wood-muted">Lengkapi data diri dan alamat pengiriman Anda untuk menyelesaikan pesanan.</p>

    <form wire:submit.prevent="placeOrder" class="mt-8 grid gap-8 lg:grid-cols-12">
        {{-- Form Pembelian (7 cols) --}}
        <div class="lg:col-span-7 space-y-6">
            <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-wood-text border-b border-wood-border/40 pb-3">1. Data Pemesan</h2>

                <div class="mt-5 space-y-4">
                    {{-- Nama Pembeli --}}
                    <div>
                        <label class="block text-xs font-semibold text-wood-text">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input
                            type="text"
                            wire:model="customer_name"
                            placeholder="Contoh: Muhammad Krisna"
                            class="mt-1 w-full rounded-2xl border border-wood-border/60 bg-white/80 py-2.5 px-4 text-xs font-medium text-wood-text focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                        />
                        @error('customer_name') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- WhatsApp --}}
                    <div>
                        <label class="block text-xs font-semibold text-wood-text">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                        <input
                            type="text"
                            wire:model="customer_phone"
                            placeholder="Contoh: 081234567890"
                            class="mt-1 w-full rounded-2xl border border-wood-border/60 bg-white/80 py-2.5 px-4 text-xs font-medium text-wood-text focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                        />
                        @error('customer_phone') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-semibold text-wood-text">Alamat Email (Opsional)</label>
                        <input
                            type="email"
                            wire:model="customer_email"
                            placeholder="nama@email.com"
                            class="mt-1 w-full rounded-2xl border border-wood-border/60 bg-white/80 py-2.5 px-4 text-xs font-medium text-wood-text focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                        />
                        @error('customer_email') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-wood-text border-b border-wood-border/40 pb-3">2. Alamat Pengiriman</h2>

                <div class="mt-5 space-y-4">
                    {{-- Alamat --}}
                    <div>
                        <label class="block text-xs font-semibold text-wood-text">Alamat Lengkap <span class="text-rose-500">*</span></label>
                        <textarea
                            wire:model="shipping_address"
                            rows="3"
                            placeholder="Jalan, No. Rumah, RT/RW, Kecamatan, Kelurahan..."
                            class="mt-1 w-full rounded-2xl border border-wood-border/60 bg-white/80 p-3 text-xs font-medium text-wood-text focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                        ></textarea>
                        @error('shipping_address') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Kota/Kabupaten --}}
                        <div>
                            <label class="block text-xs font-semibold text-wood-text">Kota / Kabupaten <span class="text-rose-500">*</span></label>
                            <input
                                type="text"
                                wire:model="city"
                                placeholder="Contoh: Jepara / Semarang"
                                class="mt-1 w-full rounded-2xl border border-wood-border/60 bg-white/80 py-2.5 px-4 text-xs font-medium text-wood-text focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                            />
                            @error('city') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        {{-- Kode Pos --}}
                        <div>
                            <label class="block text-xs font-semibold text-wood-text">Kode Pos (Opsional)</label>
                            <input
                                type="text"
                                wire:model="postal_code"
                                placeholder="59411"
                                class="mt-1 w-full rounded-2xl border border-wood-border/60 bg-white/80 py-2.5 px-4 text-xs font-medium text-wood-text focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                            />
                            @error('postal_code') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="block text-xs font-semibold text-wood-text">Catatan Pesanan (Opsional)</label>
                        <textarea
                            wire:model="notes"
                            rows="2"
                            placeholder="Catatan khusus mengenai warna kayu, finishing, atau instruksi pengiriman..."
                            class="mt-1 w-full rounded-2xl border border-wood-border/60 bg-white/80 p-3 text-xs font-medium text-wood-text focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                        ></textarea>
                        @error('notes') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Summary (5 cols) --}}
        <div class="lg:col-span-5">
            <div class="sticky top-24 rounded-3xl border border-wood-border/60 bg-white p-6 shadow-md shadow-wood-primary/5">
                <h2 class="text-lg font-bold text-wood-text border-b border-wood-border/40 pb-4">Ringkasan Pesanan</h2>

                {{-- Cart Items --}}
                <div class="mt-4 max-h-64 overflow-y-auto divide-y divide-wood-border/30 pr-1 scrollbar-thin">
                    @foreach ($cart as $item)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 shrink-0 overflow-hidden rounded-xl bg-wood-light/40 border border-wood-border/40">
                                    @if (!empty($item['thumbnail']))
                                        <img src="{{ asset('storage/' . $item['thumbnail']) }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-wood-muted">
                                            <svg class="h-5 w-5 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-semibold text-wood-text line-clamp-1">{{ $item['name'] }}</h4>
                                    <span class="text-wood-muted">Qty: {{ $item['quantity'] }} × {{ $item['formatted_price'] }}</span>
                                </div>
                            </div>
                            <span class="font-bold text-wood-text">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                {{-- Calculation --}}
                <div class="mt-6 border-t border-wood-border/40 pt-4 space-y-2 text-xs">
                    <div class="flex justify-between text-wood-muted">
                        <span>Subtotal Produk</span>
                        <span class="font-bold text-wood-text">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-wood-muted">
                        <span>Biaya Pengiriman</span>
                        <span class="font-semibold text-emerald-600">Dikonfirmasi via WhatsApp</span>
                    </div>
                </div>

                <div class="mt-6 border-t border-wood-border/40 pt-4">
                    <div class="flex justify-between text-base font-bold text-wood-text">
                        <span>Total Pembayaran</span>
                        <span class="text-lg text-wood-primary">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="mt-6 w-full rounded-2xl bg-emerald-700 py-4 text-center text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-emerald-700/25 transition-all hover:bg-emerald-800 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2"
                >
                    <svg wire:loading.remove class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                    </svg>
                    <span wire:loading.remove>Konfirmasi Pesanan via WhatsApp</span>
                    <span wire:loading>Memproses Pesanan...</span>
                </button>
            </div>
        </div>
    </form>
</div>
