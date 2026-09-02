@extends('frontend.layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_code)

@section('content')
    {{-- ============================================================
         PAGE HERO (Brown Wood Theme)
         ============================================================ --}}
    <section class="relative overflow-hidden bg-wood-primary-dark pt-36 pb-20 sm:pt-40 lg:pt-44 lg:pb-24">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="animate-blob absolute -top-24 -left-24 h-96 w-96 rounded-full bg-wood-secondary/20 blur-3xl"></div>
            <div class="animate-blob absolute bottom-0 right-0 h-[28rem] w-[28rem] rounded-full bg-wood-primary/30 blur-3xl" style="animation-delay: 3s;"></div>
        </div>

        <div class="relative mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="mb-6 flex items-center gap-2 text-xs text-white/80">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('orders.index') }}" class="hover:text-white transition-colors">Pesanan Saya</a>
                <span>/</span>
                <span class="font-semibold text-white font-mono">#{{ $order->order_code }}</span>
            </nav>

            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <span class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-white backdrop-blur-sm">
                        <span class="text-wood-secondary-light">✦</span>
                        Informasi Pesanan
                    </span>
                    <h1 class="font-serif text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">
                        Detail Pesanan
                    </h1>
                    <p class="mt-2 font-mono text-sm font-semibold text-wood-secondary-light">
                        Kode Order: #{{ $order->order_code }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-5 py-2.5 text-xs font-semibold text-white backdrop-blur-sm transition-all hover:bg-white hover:text-wood-primary shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Pesanan Saya
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         CONTENT
         ============================================================ --}}
    <section class="bg-wood-bg py-16 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Main Content (2/3) --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Product Items List --}}
                <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between border-b border-wood-border/40 pb-3 mb-4">
                        <h2 class="text-base font-bold text-wood-text">Daftar Produk Pesanan</h2>
                        <span class="text-xs font-semibold text-wood-muted">
                            Total: {{ $order->items && $order->items->count() > 0 ? $order->items->count() : 1 }} Produk ({{ $order->quantity }} Unit)
                        </span>
                    </div>

                    @if ($order->items && $order->items->count() > 0)
                        <div class="divide-y divide-wood-border/40">
                            @foreach ($order->items as $item)
                                <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row items-start gap-4">
                                    <div class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-wood-light/40 border border-wood-border/40">
                                        @if ($item->product && $item->product->thumbnail)
                                            <img src="{{ asset('storage/' . $item->product->thumbnail) }}" alt="{{ $item->product->name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-wood-muted">
                                                <svg class="h-9 w-9 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0 w-full">
                                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-1">
                                            <div>
                                                <h3 class="text-base font-semibold text-wood-text">{{ $item->product?->name ?? 'Produk' }}</h3>
                                                <p class="text-xs text-wood-muted mt-0.5">
                                                    {{ $item->quantity }} pcs × Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                                </p>
                                            </div>
                                            <span class="text-sm font-bold text-wood-primary">
                                                Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        {{-- Variations & Customization details per item --}}
                                        <div class="mt-2.5 flex flex-wrap gap-2 text-xs">
                                            @if ($item->meubel_type)
                                                <span class="inline-flex items-center rounded-lg bg-amber-50 px-2.5 py-1 font-semibold text-amber-800 border border-amber-200">
                                                    {{ $item->meubel_type_label }}
                                                </span>
                                            @endif
                                            @if ($item->seat_material_name)
                                                <span class="inline-flex items-center rounded-lg bg-wood-light/60 px-2.5 py-1 font-medium text-wood-text border border-wood-border/60">
                                                    Dudukan: {{ $item->seat_material_name }}
                                                </span>
                                            @endif
                                            @if ($item->packing_material_name)
                                                <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 font-medium text-emerald-800 border border-emerald-200">
                                                    Packing: {{ $item->packing_material_name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Fallback for legacy single product order --}}
                        <div class="flex items-start gap-4">
                            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-wood-light/40 border border-wood-border/40">
                                @if ($order->product && $order->product->thumbnail)
                                    <img src="{{ asset('storage/' . $order->product->thumbnail) }}" alt="{{ $order->product->name }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-wood-muted">
                                        <svg class="h-9 w-9 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-semibold text-wood-text">{{ $order->product?->name ?? 'Produk' }}</h3>
                                <div class="mt-3 grid grid-cols-2 gap-4 text-xs">
                                    <div>
                                        <span class="text-wood-muted block mb-0.5">Harga Satuan</span>
                                        <span class="font-semibold text-wood-text">{{ $order->product?->formatted_price ?? 'Rp 0' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-wood-muted block mb-0.5">Jumlah</span>
                                        <span class="font-semibold text-wood-text">{{ $order->quantity }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Breakdown Summary inside card --}}
                    <div class="mt-5 pt-4 border-t border-wood-border/40 space-y-2 text-xs">
                        <div class="flex justify-between text-wood-muted">
                            <span>Subtotal Produk</span>
                            <span class="font-semibold text-wood-text">
                                Rp {{ number_format(($order->total_price - ($order->customization_fee ?? 0) - ($order->packing_fee ?? 0)), 0, ',', '.') }}
                            </span>
                        </div>
                        @if ($order->customization_fee > 0)
                            <div class="flex justify-between text-wood-muted">
                                <span>Biaya Customisasi Meubel Matang</span>
                                <span class="font-semibold text-wood-text">Rp {{ number_format($order->customization_fee, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if ($order->packing_fee > 0)
                            <div class="flex justify-between text-wood-muted">
                                <span>Biaya Bahan Packing</span>
                                <span class="font-semibold text-wood-text">Rp {{ number_format($order->packing_fee, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-wood-muted">
                            <span>Ongkos Kirim</span>
                            <span class="font-semibold text-emerald-700">Rp 0 (Termasuk / Sesuai Kesepakatan WA)</span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-bold text-wood-text pt-2 border-t border-wood-border/30">
                            <span>Grand Total</span>
                            <span class="text-base text-wood-primary">{{ $order->formatted_total_price }}</span>
                        </div>
                    </div>
                </div>

                {{-- Product Review Section for Completed Orders --}}
                @if ($order->review)
                    <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between border-b border-wood-border/40 pb-3 mb-4">
                            <h2 class="text-base font-bold text-wood-text flex items-center gap-2">
                                <span class="text-amber-500">★</span> Ulasan Anda
                            </h2>
                            @if ($order->review->is_visible)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Ditampilkan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 border border-amber-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Menunggu Moderasi
                                </span>
                            @endif
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="h-4 w-4 {{ $i <= $order->review->rating ? 'text-amber-400 fill-amber-400' : 'text-gray-200 fill-gray-200' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-xs font-bold text-wood-text">{{ $order->review->rating_label }}</span>
                            </div>

                            @if ($order->review->comment)
                                <p class="text-xs sm:text-sm text-wood-text/90 leading-relaxed bg-wood-light/20 p-3.5 rounded-2xl border border-wood-border/40">
                                    {{ $order->review->comment }}
                                </p>
                            @endif

                            @if ($order->review->images->isNotEmpty())
                                <div class="flex flex-wrap gap-2 pt-1">
                                    @foreach ($order->review->images as $img)
                                        <a href="{{ asset('storage/' . $img->image) }}" target="_blank" class="h-16 w-16 overflow-hidden rounded-xl border border-wood-border/60">
                                            <img src="{{ asset('storage/' . $img->image) }}" alt="Foto Ulasan" class="h-full w-full object-cover">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif ($order->status === 'completed')
                    <div class="rounded-3xl border-2 border-dashed border-wood-primary/40 bg-white p-6 shadow-sm">
                        <div class="flex items-start gap-3 mb-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-wood-primary/10 text-wood-primary">
                                <span class="text-lg">★</span>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-wood-text">Beri Ulasan Produk</h3>
                                <p class="text-xs text-wood-muted mt-0.5">Pesanan Anda telah selesai! Bagikan pengalaman Anda untuk membantu pembeli lain.</p>
                            </div>
                        </div>

                        @if ($order->items && $order->items->count() > 0)
                            <div class="space-y-2">
                                @foreach ($order->items as $item)
                                    @if ($item->product)
                                        <div class="flex items-center justify-between p-3 rounded-2xl bg-wood-light/20 border border-wood-border/40">
                                            <span class="text-xs font-semibold text-wood-text truncate">{{ $item->product->name }}</span>
                                            <a
                                                href="{{ route('products.show', $item->product->slug) }}#ulasan"
                                                class="shrink-0 inline-flex items-center gap-1.5 rounded-xl bg-wood-primary px-3.5 py-1.5 text-xs font-bold text-white hover:bg-wood-primary-dark transition-all shadow-xs"
                                            >
                                                <span>Ulas Produk</span>
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                                </svg>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @elseif ($order->product)
                            <a
                                href="{{ route('products.show', $order->product->slug) }}#ulasan"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-wood-primary px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-wood-primary/20 hover:bg-wood-primary-dark transition-all"
                            >
                                <span>Tulis Ulasan</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                @endif

                {{-- Catatan Pesanan --}}
                @if ($order->notes)
                    <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm">
                        <h2 class="mb-3 text-base font-bold text-wood-text border-b border-wood-border/40 pb-3">Catatan Pesanan</h2>
                        <p class="text-sm text-wood-text whitespace-pre-line leading-relaxed">{{ $order->notes }}</p>
                    </div>
                @endif

                {{-- Status History --}}
                <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-base font-bold text-wood-text border-b border-wood-border/40 pb-3">Riwayat Status Pesanan</h2>
                    <div class="space-y-0">
                        @forelse ($order->statusHistories as $history)
                            <div class="relative flex gap-4 pb-6">
                                @if (!$loop->last)
                                    <div class="absolute left-[11px] top-6 h-full w-0.5 bg-wood-border/50"></div>
                                @endif
                                <div class="relative flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-wood-primary">
                                    <div class="h-2 w-2 rounded-full bg-wood-primary"></div>
                                </div>
                                <div class="flex-1 pt-0.5">
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <p class="text-xs font-bold text-wood-text">{{ $history->status_label }}</p>
                                        <span class="text-[10px] text-wood-muted">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if ($history->notes)
                                        <p class="mt-0.5 text-xs text-wood-muted">{{ $history->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-wood-muted">Belum ada riwayat status.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Sidebar (1/3) --}}
            <div class="space-y-5">
                {{-- Status --}}
                <div class="rounded-3xl border border-wood-border/60 bg-white p-5 shadow-sm">
                    <h2 class="text-sm font-bold text-wood-text border-b border-wood-border/40 pb-3 mb-3">Status Pesanan</h2>
                    <span class="inline-flex items-center rounded-full px-4 py-1.5 text-xs font-bold
                        @if($order->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                        @elseif($order->status === 'confirmed') bg-blue-50 text-blue-700 border border-blue-200
                        @elseif($order->status === 'processing') bg-purple-50 text-purple-700 border border-purple-200
                        @elseif($order->status === 'shipping') bg-sky-50 text-sky-700 border border-sky-200
                        @elseif($order->status === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                        @elseif($order->status === 'cancelled') bg-rose-50 text-rose-700 border border-rose-200
                        @else bg-gray-50 text-gray-600 border border-gray-200
                        @endif
                    ">
                        {{ $order->status_label }}
                    </span>
                </div>

                {{-- Customer Info --}}
                <div class="rounded-3xl border border-wood-border/60 bg-white p-5 shadow-sm">
                    <h2 class="text-sm font-bold text-wood-text border-b border-wood-border/40 pb-3 mb-3">Informasi Pemesan</h2>
                    <div class="space-y-2.5 text-xs">
                        <div>
                            <span class="text-wood-muted block">Nama</span>
                            <span class="font-semibold text-wood-text">{{ $order->customer_name }}</span>
                        </div>
                        <div>
                            <span class="text-wood-muted block">WhatsApp</span>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer_phone) }}" target="_blank" class="font-semibold text-wood-text hover:text-wood-primary">
                                {{ $order->customer_phone }}
                            </a>
                        </div>
                        @if ($order->customer_email)
                        <div>
                            <span class="text-wood-muted block">Email</span>
                            <span class="font-semibold text-wood-text">{{ $order->customer_email }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Shipping Address --}}
                <div class="rounded-3xl border border-wood-border/60 bg-white p-5 shadow-sm">
                    <h2 class="text-sm font-bold text-wood-text border-b border-wood-border/40 pb-3 mb-3">Alamat Pengiriman</h2>
                    <p class="text-xs text-wood-text leading-relaxed">{{ $order->shipping_address }}</p>
                    <p class="mt-1 text-xs text-wood-muted">{{ $order->city }}{{ $order->postal_code ? ', ' . $order->postal_code : '' }}</p>
                </div>

                {{-- Shipping Info --}}
                @if ($order->shipping_method)
                    <div class="rounded-3xl border border-wood-border/60 bg-white p-5 shadow-sm">
                        <h2 class="text-sm font-bold text-wood-text border-b border-wood-border/40 pb-3 mb-3">Info Pengiriman</h2>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-wood-muted">Metode</span>
                                <span class="font-semibold text-wood-text">{{ $order->shipping_method_label }}</span>
                            </div>
                            @if ($order->courier)
                                <div class="flex justify-between">
                                    <span class="text-wood-muted">Kurir</span>
                                    <span class="font-semibold text-wood-text">{{ $order->courier }}</span>
                                </div>
                            @endif
                            @if ($order->tracking_number)
                                <div class="flex justify-between">
                                    <span class="text-wood-muted">No. Resi</span>
                                    <span class="font-bold font-mono text-wood-primary">{{ $order->tracking_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
