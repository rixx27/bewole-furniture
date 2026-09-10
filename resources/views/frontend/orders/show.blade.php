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
                                <span>Biaya Tambahan Meubel Finished</span>
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
                            <span class="font-semibold text-danger">Belum Termasuk Biaya Ongkir</span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-bold text-wood-text pt-2 border-t border-wood-border/30">
                            <span>Grand Total</span>
                            <span class="text-base text-wood-primary">{{ $order->formatted_total_price }}</span>
                        </div>
                    </div>
                </div>

                {{-- Payment Info & Proof Upload Section --}}
                <livewire:frontend.order-payment-upload :order-id="$order->id" />

                {{-- Product Review Section for Completed Orders --}}
                @if ($order->status === 'completed')
                    @php
                        $existingReviews = $order->reviews && $order->reviews->isNotEmpty() 
                            ? $order->reviews 
                            : ($order->review ? collect([$order->review]) : collect());
                        $reviewedProductIds = $existingReviews->pluck('product_id')->toArray();
                    @endphp

                    {{-- Existing Reviews List (if any) --}}
                    @if ($existingReviews->isNotEmpty())
                        @foreach ($existingReviews as $rev)
                            <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm">
                                <div class="flex items-center justify-between border-b border-wood-border/40 pb-3 mb-4">
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-base font-bold text-wood-text flex items-center gap-2">
                                            <span class="text-amber-500">★</span> Ulasan Anda
                                        </h2>
                                        @if ($rev->product)
                                            <span class="text-xs font-semibold text-wood-muted">({{ $rev->product->name }})</span>
                                        @endif
                                    </div>
                                    @if ($rev->is_visible)
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
                                                <svg class="h-4 w-4 {{ $i <= $rev->rating ? 'text-amber-400 fill-amber-400' : 'text-gray-200 fill-gray-200' }}" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="text-xs font-bold text-wood-text">{{ $rev->rating_label }}</span>
                                    </div>

                                    @if ($rev->comment)
                                        <p class="text-xs sm:text-sm text-wood-text/90 leading-relaxed bg-wood-light/20 p-3.5 rounded-2xl border border-wood-border/40">
                                            {{ $rev->comment }}
                                        </p>
                                    @endif

                                    @if ($rev->images->isNotEmpty())
                                        <div class="flex flex-wrap gap-2 pt-1">
                                            @foreach ($rev->images as $img)
                                                <a href="{{ asset('storage/' . $img->image) }}" target="_blank" class="h-16 w-16 overflow-hidden rounded-xl border border-wood-border/60">
                                                    <img src="{{ asset('storage/' . $img->image) }}" alt="Foto Ulasan" class="h-full w-full object-cover">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif

                    {{-- Products Pending Review in this Completed Order --}}
                    @php
                        $unreviewedItems = collect();
                        if ($order->items && $order->items->count() > 0) {
                            $unreviewedItems = $order->items->filter(function ($item) use ($reviewedProductIds) {
                                return $item->product && !in_array($item->product_id, $reviewedProductIds);
                            });
                        } elseif ($order->product && !in_array($order->product_id, $reviewedProductIds)) {
                            $unreviewedItems = collect([$order]);
                        }
                    @endphp

                    @if ($unreviewedItems->isNotEmpty())
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

                            <div class="space-y-2">
                                @foreach ($unreviewedItems as $unrev)
                                    @php
                                        $pModel = $unrev instanceof \App\Models\OrderItem ? $unrev->product : ($unrev->product ?? null);
                                    @endphp
                                    @if ($pModel)
                                        <div class="flex items-center justify-between p-3 rounded-2xl bg-wood-light/20 border border-wood-border/40">
                                            <span class="text-xs font-semibold text-wood-text truncate">{{ $pModel->name }}</span>
                                            <a
                                                href="{{ route('products.show', $pModel->slug) }}#ulasan"
                                                class="shrink-0 inline-flex items-center gap-1.5 rounded-xl bg-wood-primary px-3.5 py-1.5 text-xs font-bold text-white hover:bg-wood-primary-dark transition-all shadow-xs"
                                            >
                                                <span>Tulis Ulasan</span>
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                                </svg>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
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

                {{-- Info Pengiriman & Ongkir --}}
                <div class="rounded-3xl border border-wood-border/60 bg-white p-5 shadow-sm space-y-3 text-xs">
                    <div class="flex items-center justify-between border-b border-wood-border/40 pb-3">
                        <h2 class="text-sm font-bold text-wood-text flex items-center gap-2">
                            <svg class="h-4 w-4 text-wood-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                            <span>Pengiriman & Ongkir</span>
                        </h2>
                        <span class="inline-flex items-center rounded-full bg-wood-light/40 px-2.5 py-0.5 text-[10px] font-bold text-wood-text border border-wood-border/60">
                            Ekspedisi Truk Jepara
                        </span>
                    </div>

                    <div class="rounded-2xl border border-amber-200 bg-amber-50/70 p-3 text-[11px] text-amber-800 leading-relaxed">
                        Pengiriman meubel diproses via truk ekspedisi lokal. Biaya ongkir dan jadwal pengantaran dikoordinasikan langsung melalui WhatsApp.
                    </div>

                    @php
                        $rawWa = App\Helpers\WebsiteSettings::whatsapp();
                        $cleanWa = $rawWa ? preg_replace('/[^0-9]/', '', (string) $rawWa) : '';
                        if (str_starts_with($cleanWa, '0')) {
                            $cleanWa = '62' . substr($cleanWa, 1);
                        }
                    @endphp
                    @if ($cleanWa)
                        <a
                            href="https://wa.me/{{ $cleanWa }}?text=Halo%20Bewole%20Furniture%2C%20saya%20ingin%20koordinasi%20ongkir%20dan%20pengiriman%20untuk%20pesanan%20%23{{ $order->order_code }}"
                            target="_blank"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-bold text-white hover:bg-emerald-700 transition-colors shadow-xs"
                        >
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <span>Koordinasi Pengiriman via WhatsApp</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
