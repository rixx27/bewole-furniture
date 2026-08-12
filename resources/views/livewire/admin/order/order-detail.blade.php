<div>
    @if ($show && $order)
        <div class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto p-4 pt-8"
             x-data x-init="$el.style.display = 'flex'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$dispatch('closeModal')"></div>
            <div class="relative w-full max-w-4xl rounded-xl bg-card p-6 shadow-2xl border border-border my-8"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                
                {{-- Header --}}
                <div class="mb-6 flex items-start justify-between border-b border-border pb-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl font-bold text-text-primary dark:text-white">Detail Pesanan</h2>
                            <span class="font-mono text-sm font-medium text-text-muted">#{{ $order->order_code }}</span>
                        </div>
                        <p class="mt-1 text-sm text-text-muted">Dibuat pada {{ $order->created_at->format('d F Y H:i') }}</p>
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
                        {{-- Informasi Customer --}}
                        <div class="rounded-lg border border-border bg-bg-secondary/50 p-4">
                            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-text-muted">Informasi Customer</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-text-muted">Nama</span>
                                    <span class="font-medium text-text-primary dark:text-white">{{ $order->customer_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-text-muted">Telepon</span>
                                    <span class="font-medium text-text-primary dark:text-white">{{ $order->customer_phone }}</span>
                                </div>
                                @if ($order->customer_email)
                                    <div class="flex justify-between">
                                        <span class="text-text-muted">Email</span>
                                        <span class="font-medium text-text-primary dark:text-white">{{ $order->customer_email }}</span>
                                    </div>
                                @endif
                                @if ($order->user)
                                    <div class="flex justify-between">
                                        <span class="text-text-muted">Akun</span>
                                        <span class="font-medium text-text-primary dark:text-white">{{ $order->user->name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div class="rounded-lg border border-border bg-bg-secondary/50 p-4">
                            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-text-muted">Alamat Pengiriman</h3>
                            <p class="text-sm text-text-primary dark:text-white">{{ $order->shipping_address }}</p>
                            <p class="mt-1 text-xs text-text-muted">{{ $order->city }}{{ $order->postal_code ? ', ' . $order->postal_code : '' }}</p>
                        </div>

                        {{-- Informasi Pengiriman --}}
                        @if ($order->shipping_method)
                            <div class="rounded-lg border border-border bg-bg-secondary/50 p-4">
                                <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-text-muted">Informasi Pengiriman</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-text-muted">Metode</span>
                                        <span class="font-medium text-text-primary dark:text-white">{{ $order->shipping_method_label }}</span>
                                    </div>
                                    @if ($order->courier)
                                        <div class="flex justify-between">
                                            <span class="text-text-muted">Kurir</span>
                                            <span class="font-medium text-text-primary dark:text-white">{{ $order->courier }}</span>
                                        </div>
                                    @endif
                                    @if ($order->tracking_number)
                                        <div class="flex justify-between">
                                            <span class="text-text-muted">No. Resi</span>
                                            <span class="font-medium text-text-primary dark:text-white">{{ $order->tracking_number }}</span>
                                        </div>
                                    @endif
                                    @if ($order->driver_name)
                                        <div class="flex justify-between">
                                            <span class="text-text-muted">Driver</span>
                                            <span class="font-medium text-text-primary dark:text-white">{{ $order->driver_name }}</span>
                                        </div>
                                    @endif
                                    @if ($order->vehicle_number)
                                        <div class="flex justify-between">
                                            <span class="text-text-muted">Kendaraan</span>
                                            <span class="font-medium text-text-primary dark:text-white">{{ $order->vehicle_number }}</span>
                                        </div>
                                    @endif
                                    @if ($order->shipping_date)
                                        <div class="flex justify-between">
                                            <span class="text-text-muted">Tgl. Kirim</span>
                                            <span class="font-medium text-text-primary dark:text-white">{{ $order->shipping_date->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                    @if ($order->pickup_date)
                                        <div class="flex justify-between">
                                            <span class="text-text-muted">Tgl. Ambil</span>
                                            <span class="font-medium text-text-primary dark:text-white">{{ $order->pickup_date->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Right Column --}}
                    <div class="space-y-6">
                        {{-- Produk --}}
                        <div class="rounded-lg border border-border bg-bg-secondary/50 p-4">
                            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-text-muted">Produk</h3>
                            <div class="flex items-start gap-4">
                                <div class="h-16 w-16 shrink-0 overflow-hidden rounded-lg bg-bg-secondary">
                                    @if ($order->product && $order->product->thumbnail)
                                        <img src="{{ asset('storage/' . $order->product->thumbnail) }}" alt="{{ $order->product->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-text-muted">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-text-primary dark:text-white">{{ $order->product?->name ?? 'Produk tidak tersedia' }}</p>
                                    <div class="mt-2 flex items-center justify-between text-sm">
                                        <span class="text-text-muted">{{ $order->quantity }} x {{ $order->product?->formatted_price ?? 'Rp 0' }}</span>
                                        <span class="font-semibold text-text-primary dark:text-white">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Status & Pembayaran --}}
                        <div class="rounded-lg border border-border bg-bg-secondary/50 p-4">
                            <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-text-muted">Status & Pembayaran</h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-text-muted">Status Pesanan</span>
                                    @php $color = $order->status_color; @endphp
                                    <span class="inline-flex items-center gap-1 rounded-full bg-{{ $color }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $color }}-700 dark:bg-{{ $color }}-950 dark:text-{{ $color }}-300">
                                        {{ $order->status_label }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-text-muted">Status Pembayaran</span>
                                    @php $color = $order->payment_status_color; @endphp
                                    <span class="inline-flex items-center gap-1 rounded-full bg-{{ $color }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $color }}-700 dark:bg-{{ $color }}-950 dark:text-{{ $color }}-300">
                                        {{ $order->payment_status_label }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-text-muted">Metode Pembayaran</span>
                                    <span class="font-medium text-text-primary dark:text-white">{{ $order->payment_method ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-text-muted">Metode Pengiriman</span>
                                    <span class="font-medium text-text-primary dark:text-white">{{ $order->shipping_method_label }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Catatan --}}
                        @if ($order->notes)
                            <div class="rounded-lg border border-border bg-bg-secondary/50 p-4">
                                <h3 class="mb-2 text-sm font-semibold uppercase tracking-wider text-text-muted">Catatan</h3>
                                <p class="text-sm text-text-primary dark:text-white">{{ $order->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Timeline --}}
                <div class="mt-6 rounded-lg border border-border bg-bg-secondary/50 p-4">
                    <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-text-muted">Riwayat Status</h3>
                    <div class="space-y-0">
                        @forelse ($order->statusHistories as $history)
                            <div class="relative flex gap-4 pb-4">
                                @if (!$loop->last)
                                    <div class="absolute left-[11px] top-6 h-full w-0.5 bg-border"></div>
                                @endif
                                <div class="relative flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-{{ $history->status_color }}-500 bg-{{ $history->status_color }}-50">
                                    <div class="h-2 w-2 rounded-full bg-{{ $history->status_color }}-500"></div>
                                </div>
                                <div class="flex-1 pt-0.5">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-text-primary dark:text-white">{{ $history->status_label }}</p>
                                        <span class="text-xs text-text-muted">{{ $history->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if ($history->description)
                                        <p class="mt-0.5 text-xs text-text-muted">{{ $history->description }}</p>
                                    @endif
                                    @if ($history->changedBy)
                                        <p class="mt-0.5 text-xs text-text-muted">oleh {{ $history->changedBy->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-text-muted">Belum ada riwayat status.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Footer --}}
                <div class="mt-6 flex items-center justify-end gap-3 border-t border-border pt-4">
                    <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank"
                       class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2h2m2 4l2-2 2 2m-2-2v-6"/>
                        </svg>
                        Cetak Invoice
                    </a>
                    <button wire:click="$dispatch('closeModal')"
                            class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
