@extends('frontend.layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_code)

@section('content')
<div class="min-h-screen bg-wood-bg pt-24 sm:pt-28">
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Breadcrumb + Back --}}
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('orders.index') }}" class="flex h-8 w-8 items-center justify-center rounded-full border border-wood-border/60 text-wood-muted transition-colors hover:text-wood-primary hover:border-wood-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-wood-text font-serif">Detail Pesanan</h1>
                <p class="text-xs font-mono text-wood-primary font-bold">#{{ $order->order_code }}</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Main Content (2/3) --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Product Info --}}
                <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-base font-bold text-wood-text border-b border-wood-border/40 pb-3">Informasi Produk</h2>
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
                            <h3 class="text-lg font-semibold text-wood-text">{{ $order->product?->name ?? 'Produk' }}</h3>
                            <div class="mt-3 grid grid-cols-2 gap-4 text-xs">
                                <div>
                                    <span class="text-wood-muted block mb-0.5">Harga Satuan</span>
                                    <span class="font-semibold text-wood-text">{{ $order->product?->formatted_price ?? 'Rp 0' }}</span>
                                </div>
                                <div>
                                    <span class="text-wood-muted block mb-0.5">Jumlah</span>
                                    <span class="font-semibold text-wood-text">{{ $order->quantity }}</span>
                                </div>
                                <div>
                                    <span class="text-wood-muted block mb-0.5">Grand Total</span>
                                    <span class="font-bold text-wood-primary text-sm">{{ $order->formatted_total_price }}</span>
                                </div>
                                <div>
                                    <span class="text-wood-muted block mb-0.5">Tanggal Pesan</span>
                                    <span class="font-semibold text-wood-text">{{ $order->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
</div>
@endsection
