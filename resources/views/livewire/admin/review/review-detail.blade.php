<div>
    @if ($show && $review)
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto p-4 pt-8"
             x-data x-init="$el.style.display = 'flex'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$dispatch('closeModal')"></div>
            <div class="relative w-full max-w-3xl rounded-xl bg-card p-6 shadow-2xl border border-border my-8"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">

                {{-- Header --}}
                <div class="mb-6 flex items-start justify-between border-b border-border pb-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl font-bold text-text-primary dark:text-white">Detail Ulasan</h2>
                            <span class="text-sm text-text-muted">#{{ $review->id }}</span>
                        </div>
                        <p class="mt-1 text-sm text-text-muted">Dibuat pada {{ $review->created_at->format('d F Y H:i') }}</p>
                    </div>
                    <button wire:click="$dispatch('closeModal')" class="rounded-lg p-1.5 text-text-muted hover:bg-bg-secondary transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    {{-- Left Column --}}
                    <div class="space-y-6">
                        {{-- Produk --}}
                        <div class="rounded-lg border border-border bg-bg-secondary/50 p-4">
                            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-text-muted">Produk</h3>
                            <div class="flex items-start gap-4">
                                <div class="h-16 w-16 shrink-0 overflow-hidden rounded-lg bg-bg-secondary">
                                    @if ($review->product && $review->product->thumbnail)
                                        <img src="{{ asset('storage/' . $review->product->thumbnail) }}" alt="{{ $review->product->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-text-muted">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-text-primary dark:text-white">
                                        <a href="{{ route('admin.products.show', $review->product_id) }}" class="hover:text-primary transition-colors">
                                            {{ $review->product?->name ?? 'Produk tidak tersedia' }}
                                        </a>
                                    </p>
                                    @if ($review->product)
                                        <p class="mt-1 text-xs text-text-muted">{{ $review->product->status_label }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Informasi Customer --}}
                        <div class="rounded-lg border border-border bg-bg-secondary/50 p-4">
                            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-text-muted">Customer</h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-sm font-bold text-primary">
                                        {{ $review->user?->initials() ?? '?' }}
                                    </span>
                                    <div>
                                        <p class="font-medium text-text-primary dark:text-white">{{ $review->user?->name ?? 'Guest' }}</p>
                                        <p class="text-xs text-text-muted">{{ $review->user?->email ?? '-' }}</p>
                                    </div>
                                </div>
                                @if ($review->user && $review->user->phone)
                                    <div class="flex justify-between">
                                        <span class="text-text-muted">No. WA</span>
                                        <span class="font-medium text-text-primary dark:text-white">
                                            <a href="https://wa.me/{{ $review->user->phone }}" target="_blank" class="hover:text-primary transition-colors">
                                                {{ $review->user->phone }}
                                            </a>
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Informasi Pesanan --}}
                        <div class="rounded-lg border border-border bg-bg-secondary/50 p-4">
                            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-text-muted">Pesanan</h3>
                            <div class="space-y-2 text-sm">
                                @if ($review->order)
                                    <div class="flex justify-between">
                                        <span class="text-text-muted">Kode Order</span>
                                        <a href="{{ route('admin.orders.show', $review->order_id) }}" class="font-mono font-medium text-primary hover:text-primary-dark transition-colors">
                                            #{{ $review->order->order_code }}
                                        </a>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-text-muted">Status</span>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-{{ $review->order->status_color }}-50 px-2 py-0.5 text-xs font-medium text-{{ $review->order->status_color }}-700 dark:bg-{{ $review->order->status_color }}-950 dark:text-{{ $review->order->status_color }}-300">
                                            {{ $review->order->status_label }}
                                        </span>
                                    </div>
                                @else
                                    <p class="text-text-muted">Data pesanan tidak tersedia</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="space-y-6">
                        {{-- Rating --}}
                        <div class="rounded-lg border border-border bg-bg-secondary/50 p-4">
                            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-text-muted">Rating & Review</h3>
                            <div class="space-y-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-0.5">
                                        {!! $review->rating_stars !!}
                                    </div>
                                    <span class="text-sm font-medium text-text-primary dark:text-white">{{ $review->rating }}/5</span>
                                    <span class="text-xs text-text-muted">({{ $review->rating_label }})</span>
                                </div>

                                {{-- Comment --}}
                                <div>
                                    <p class="text-xs font-medium text-text-muted mb-1">Komentar:</p>
                                    <p class="text-sm text-text-primary dark:text-white leading-relaxed">{{ $review->comment ?? 'Tidak ada komentar.' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Review Images Gallery --}}
                        @if ($review->images->count() > 0)
                            <div class="rounded-lg border border-border bg-bg-secondary/50 p-4">
                                <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-text-muted">
                                    Foto Review ({{ $review->images->count() }})
                                </h3>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach ($review->images as $image)
                                        <a href="{{ asset('storage/' . $image->image) }}" target="_blank"
                                           class="group relative block aspect-square overflow-hidden rounded-lg bg-bg-secondary">
                                            <img src="{{ asset('storage/' . $image->image) }}"
                                                 alt="Review image {{ $loop->iteration }}"
                                                 class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-110">
                                            <div class="absolute inset-0 flex items-center justify-center bg-black/0 transition-colors group-hover:bg-black/20">
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
                        <div class="rounded-lg border border-border bg-bg-secondary/50 p-4">
                            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-text-muted">Status</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-text-muted">Ditampilkan</span>
                                    @if ($review->is_visible)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Ditampilkan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                            Disembunyikan
                                        </span>
                                    @endif
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-text-muted">Terverifikasi</span>
                                    @if ($review->is_verified)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                                            Terverifikasi
                                        </span>
                                    @else
                                        <span class="text-xs text-text-muted">Tidak</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="mt-6 flex items-center justify-end gap-3 border-t border-border pt-4">
                    {{-- Toggle Visibility --}}
                    @if ($review->is_visible)
                        <button wire:click="$parent.toggleVisibility({{ $review->id }})"
                                wire:confirm="Apakah Anda yakin ingin menyembunyikan ulasan ini?"
                                class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-amber-600 hover:bg-amber-50 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                            Sembunyikan
                        </button>
                    @else
                        <button wire:click="$parent.toggleVisibility({{ $review->id }})"
                                wire:confirm="Apakah Anda yakin ingin menampilkan ulasan ini?"
                                class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-emerald-600 hover:bg-emerald-50 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Tampilkan
                        </button>
                    @endif
                    <button wire:click="$dispatch('closeModal')"
                            class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

