@php
    $title = 'Detail Ulasan';
@endphp

<x-layouts::admin :title="$title">

    {{-- Alert Success --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mb-4 flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="flex-1">{{ session('success') }}</span>
            <button x-on:click="show = false" class="text-emerald-400 hover:text-emerald-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.product-reviews.index') }}" class="rounded-lg border border-border bg-card p-2 text-text-secondary hover:bg-bg-secondary transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-black">Detail Ulasan</h2>
                    <p class="mt-1 text-sm text-text-secondary">Ulasan #{{ $productReview->id }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Left Column --}}
        <div class="space-y-6">
            {{-- Produk --}}
            <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-text-muted">Produk</h3>
                <div class="flex items-start gap-4">
                    <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-bg-secondary">
                        @if ($productReview->product && $productReview->product->thumbnail)
                            <img src="{{ asset('storage/' . $productReview->product->thumbnail) }}" alt="{{ $productReview->product->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-text-muted">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="text-base font-semibold text-text-primary dark:text-white">
                            <a href="{{ route('admin.products.show', $productReview->product_id) }}" class="hover:text-primary transition-colors">
                                {{ $productReview->product?->name ?? 'Produk tidak tersedia' }}
                            </a>
                        </p>
                        @if ($productReview->product)
                            <p class="mt-1 text-sm text-text-muted">{{ $productReview->product->status_label }}</p>
                            <p class="mt-1 text-sm font-medium text-text-primary dark:text-white">{{ $productReview->product->formatted_price }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Informasi Customer --}}
            <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-text-muted">Customer</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary/10 text-base font-bold text-primary">
                            {{ $productReview->user?->initials() ?? '?' }}
                        </span>
                        <div>
                            <p class="font-medium text-text-primary dark:text-white">{{ $productReview->user?->name ?? 'Guest' }}</p>
                            <p class="text-sm text-text-muted">{{ $productReview->user?->email ?? '-' }}</p>
                        </div>
                    </div>
                    @if ($productReview->user && $productReview->user->phone)
                        <div class="flex items-center justify-between rounded-lg bg-bg-secondary/50 px-4 py-3">
                            <span class="text-sm text-text-muted">No. WhatsApp</span>
                            <a href="https://wa.me/{{ $productReview->user->phone }}" target="_blank" class="text-sm font-medium text-primary hover:text-primary-dark transition-colors">
                                {{ $productReview->user->phone }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Informasi Pesanan --}}
            <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-text-muted">Pesanan</h3>
                @if ($productReview->order)
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-text-muted">Kode Order</span>
                            <a href="{{ route('admin.orders.show', $productReview->order_id) }}" class="text-sm font-mono font-medium text-primary hover:text-primary-dark transition-colors">
                                #{{ $productReview->order->order_code }}
                            </a>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-text-muted">Status Pesanan</span>
                            @php $color = $productReview->order->status_color; @endphp
                            <span class="inline-flex items-center gap-1 rounded-full bg-{{ $color }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $color }}-700 dark:bg-{{ $color }}-950 dark:text-{{ $color }}-300">
                                {{ $productReview->order->status_label }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-text-muted">Total Pesanan</span>
                            <span class="text-sm font-semibold text-text-primary dark:text-white">{{ $productReview->order->formatted_total_price }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-text-muted">Data pesanan tidak tersedia</p>
                @endif
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-6">
            {{-- Rating & Comment --}}
            <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-text-muted">Rating & Ulasan</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-0.5">
                            {!! $productReview->rating_stars !!}
                        </div>
                        <span class="text-lg font-bold text-text-primary dark:text-white">{{ $productReview->rating }}/5</span>
                        <span class="text-sm text-text-muted">({{ $productReview->rating_label }})</span>
                    </div>

                    <div class="rounded-lg bg-bg-secondary/50 p-4">
                        <p class="text-sm font-medium text-text-muted mb-2">Komentar:</p>
                        <p class="text-sm text-text-primary dark:text-white leading-relaxed">{{ $productReview->comment ?? 'Tidak ada komentar.' }}</p>
                    </div>

                    <div class="text-xs text-text-muted">
                        Dibuat pada {{ $productReview->created_at->format('d F Y H:i') }}
                    </div>
                </div>
            </div>

            {{-- Review Images --}}
            @if ($productReview->images->count() > 0)
                <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
                    <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-text-muted">
                        Foto Review ({{ $productReview->images->count() }})
                    </h3>
                    <div class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                        @foreach ($productReview->images as $image)
                            <a href="{{ asset('storage/' . $image->image) }}" target="_blank"
                               class="group relative block aspect-square overflow-hidden rounded-lg bg-bg-secondary">
                                <img src="{{ asset('storage/' . $image->image) }}"
                                     alt="Review image {{ $loop->iteration }}"
                                     class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-110">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/0 transition-colors group-hover:bg-black/30">
                                    <svg class="h-6 w-6 text-white opacity-0 transition-opacity group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Status --}}
            <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-text-muted">Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-text-muted">Ditampilkan</span>
                        @if ($productReview->is_visible)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Ditampilkan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                Disembunyikan
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-text-muted">Terverifikasi</span>
                        @if ($productReview->is_verified)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                Terverifikasi
                            </span>
                        @else
                            <span class="text-sm text-text-muted">Tidak</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="mt-6">
        <a href="{{ route('admin.product-reviews.index') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Ulasan
        </a>
    </div>

</x-layouts::admin>
