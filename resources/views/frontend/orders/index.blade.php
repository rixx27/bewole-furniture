@extends('frontend.layouts.app')

@section('title', 'Pesanan Saya')

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
                <span class="font-semibold text-white">Pesanan Saya</span>
            </nav>
            <span class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-white backdrop-blur-sm">
                <span class="text-wood-secondary-light">✦</span>
                Riwayat Transaksi
            </span>
            <h1 class="font-serif text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                Pesanan Saya
            </h1>
            <p class="mt-4 max-w-2xl text-sm leading-relaxed text-white/85 sm:text-base">
                Riwayat semua pesanan furniture yang telah Anda buat di Bewole Furniture.
            </p>
        </div>
    </section>

    {{-- ============================================================
         CONTENT
         ============================================================ --}}
    <section class="bg-wood-bg py-16 sm:py-20 lg:py-24">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

        {{-- Orders List --}}
        @if ($orders->count() > 0)
            <div class="space-y-4">
                @foreach ($orders as $order)
                    <div class="overflow-hidden rounded-3xl border border-wood-border/60 bg-white shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5 duration-300">
                        <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                            {{-- Order Info --}}
                            <div class="flex items-center gap-4">
                                {{-- Product Thumbnail --}}
                                @php
                                    $firstItem = $order->items?->first();
                                    $displayProduct = $firstItem?->product ?? $order->product;
                                    $itemCount = $order->items?->count() ?: 1;
                                @endphp
                                <div class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl bg-wood-light/50 border border-wood-border/40">
                                    @if ($displayProduct && $displayProduct->thumbnail)
                                        <img src="{{ asset('storage/' . $displayProduct->thumbnail) }}" alt="{{ $displayProduct->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-wood-muted">
                                            <svg class="h-8 w-8 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <span class="font-mono text-xs font-bold text-wood-primary">{{ $order->order_code }}</span>
                                    <h3 class="mt-0.5 text-sm font-semibold text-wood-text">
                                        {{ $displayProduct?->name ?? 'Produk' }}
                                        @if ($itemCount > 1)
                                            <span class="font-normal text-wood-muted">(+{{ $itemCount - 1 }} produk lain)</span>
                                        @endif
                                    </h3>
                                    <p class="mt-0.5 text-xs text-wood-muted">
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Status & Price --}}
                            <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end sm:justify-center">
                                <div class="text-right">
                                    <span class="text-xs text-wood-muted block">Total Pembayaran</span>
                                    <span class="text-base font-bold text-wood-primary">{{ $order->formatted_total_price }}</span>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold
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
                        </div>

                        {{-- Footer --}}
                        <div class="border-t border-wood-border/40 bg-wood-light/20 px-5 py-3 flex items-center justify-between">
                            <span class="text-xs text-wood-muted">
                                Qty: {{ $order->quantity }} unit
                            </span>
                            <a
                                href="{{ route('orders.show', $order->order_code) }}"
                                class="inline-flex items-center gap-1.5 rounded-full bg-wood-primary/10 px-4 py-1.5 text-xs font-semibold text-wood-primary transition-all hover:bg-wood-primary hover:text-white"
                            >
                                Lihat Detail
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center rounded-3xl border border-wood-border/60 bg-white/70 p-14 text-center shadow-sm">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-wood-light/60 text-wood-muted">
                    <svg class="h-10 w-10 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h2 class="mt-5 text-xl font-bold text-wood-text">Belum Ada Pesanan</h2>
                <p class="mt-1.5 max-w-sm text-sm text-wood-muted">Anda belum memiliki riwayat pesanan. Mulai jelajahi koleksi furniture kami!</p>
                <a
                    href="{{ route('products.index') }}"
                    class="mt-6 rounded-full bg-wood-primary px-8 py-3 text-xs font-semibold uppercase tracking-wider text-white shadow-lg shadow-wood-primary/25 hover:bg-wood-primary-dark transition-all hover:-translate-y-0.5"
                >
                    Jelajahi Produk
                </a>
            </div>
        @endif
    </div>
</section>
@endsection
