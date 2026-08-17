<div>
    @if ($show && $order)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
             x-data x-init="$el.style.display = 'flex'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="$dispatch('closeModal')"></div>
            
            {{-- Modal Box Container --}}
            <div class="relative flex flex-col w-full max-w-4xl max-h-[85vh] rounded-2xl bg-white shadow-2xl border border-gray-200 text-gray-900 overflow-hidden z-10"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                
                {{-- Header (Fixed Top) --}}
                <div class="flex items-start justify-between border-b border-gray-200 p-5 sm:p-6 pb-4 bg-white shrink-0">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl font-bold text-gray-900">Detail Pesanan</h2>
                            <span class="font-mono text-sm font-bold text-amber-800">#{{ $order->order_code }}</span>
                        </div>
                        <p class="mt-1 text-xs font-medium text-gray-500">Dibuat pada {{ $order->created_at->format('d F Y H:i') }}</p>
                    </div>
                    <button wire:click="$dispatch('closeModal')" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Scrollable Content Body --}}
                <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-6">
                    <div class="grid gap-6 lg:grid-cols-2">
                        {{-- Left Column --}}
                        <div class="space-y-6">
                            {{-- Informasi Customer --}}
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-xs">
                                <h3 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-gray-500">Informasi Customer</h3>
                                <div class="space-y-2.5 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-medium">Nama</span>
                                        <span class="font-bold text-gray-900">{{ $order->customer_name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-medium">Telepon</span>
                                        <span class="font-bold text-gray-900">{{ $order->customer_phone }}</span>
                                    </div>
                                    @if ($order->customer_email)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 font-medium">Email</span>
                                            <span class="font-bold text-gray-900">{{ $order->customer_email }}</span>
                                        </div>
                                    @endif
                                    @if ($order->user)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 font-medium">Akun</span>
                                            <span class="font-bold text-gray-900">{{ $order->user->name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Alamat --}}
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-xs">
                                <h3 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-gray-500">Alamat Pengiriman</h3>
                                <p class="text-sm font-bold text-gray-900 leading-relaxed">{{ $order->shipping_address }}</p>
                                <p class="mt-1 text-xs font-semibold text-gray-600">{{ $order->city }}{{ $order->postal_code ? ', ' . $order->postal_code : '' }}</p>
                            </div>

                            {{-- Informasi Pengiriman --}}
                            @if ($order->shipping_method)
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-xs">
                                    <h3 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-gray-500">Informasi Pengiriman</h3>
                                    <div class="space-y-2.5 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 font-medium">Metode</span>
                                            <span class="font-bold text-gray-900">{{ $order->shipping_method_label }}</span>
                                        </div>
                                        @if ($order->courier)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 font-medium">Kurir</span>
                                                <span class="font-bold text-gray-900">{{ $order->courier }}</span>
                                            </div>
                                        @endif
                                        @if ($order->tracking_number)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 font-medium">No. Resi</span>
                                                <span class="font-bold text-gray-900">{{ $order->tracking_number }}</span>
                                            </div>
                                        @endif
                                        @if ($order->driver_name)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 font-medium">Driver</span>
                                                <span class="font-bold text-gray-900">{{ $order->driver_name }}</span>
                                            </div>
                                        @endif
                                        @if ($order->vehicle_number)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 font-medium">Kendaraan</span>
                                                <span class="font-bold text-gray-900">{{ $order->vehicle_number }}</span>
                                            </div>
                                        @endif
                                        @if ($order->shipping_date)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 font-medium">Tgl. Kirim</span>
                                                <span class="font-bold text-gray-900">{{ $order->shipping_date->format('d/m/Y') }}</span>
                                            </div>
                                        @endif
                                        @if ($order->pickup_date)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 font-medium">Tgl. Ambil</span>
                                                <span class="font-bold text-gray-900">{{ $order->pickup_date->format('d/m/Y') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Right Column --}}
                        <div class="space-y-6">
                            {{-- Produk --}}
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-xs">
                                <h3 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-gray-500">Produk</h3>
                                <div class="flex items-start gap-4">
                                    <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl border border-gray-200 bg-white">
                                        @if ($order->product && $order->product->thumbnail)
                                            <img src="{{ asset('storage/' . $order->product->thumbnail) }}" alt="{{ $order->product->name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-gray-400">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-900">{{ $order->product?->name ?? 'Produk tidak tersedia' }}</p>
                                        <div class="mt-2 flex items-center justify-between text-sm">
                                            <span class="text-gray-600 font-medium">{{ $order->quantity }} x {{ $order->product?->formatted_price ?? 'Rp 0' }}</span>
                                            <span class="font-extrabold text-amber-900 text-base">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Status & Pembayaran --}}
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-xs">
                                <h3 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-gray-500">Status & Pembayaran</h3>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 font-medium">Status Pesanan</span>
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900 border border-amber-300">
                                            <span>{{ $order->status_emoji }}</span>
                                            <span>{{ $order->status_label }}</span>
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 font-medium">Status Pembayaran</span>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-900 border border-emerald-300">
                                            {{ $order->payment_status_label }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-medium">Metode Pembayaran</span>
                                        <span class="font-bold text-gray-900">{{ $order->payment_method ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-medium">Metode Pengiriman</span>
                                        <span class="font-bold text-gray-900">{{ $order->shipping_method_label }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Catatan --}}
                            @if ($order->notes)
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-xs">
                                    <h3 class="mb-2 text-xs font-extrabold uppercase tracking-wider text-gray-500">Catatan</h3>
                                    <p class="text-sm font-medium text-gray-900 leading-relaxed">{{ $order->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Timeline --}}
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-xs">
                        <h3 class="mb-4 text-xs font-extrabold uppercase tracking-wider text-gray-500">Riwayat Status</h3>
                        <div class="space-y-0">
                            @forelse ($order->statusHistories as $history)
                                <div class="relative flex gap-4 pb-4">
                                    @if (!$loop->last)
                                        <div class="absolute left-[11px] top-6 h-full w-0.5 bg-gray-300"></div>
                                    @endif
                                    <div class="relative flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-amber-600 bg-amber-100">
                                        <div class="h-2 w-2 rounded-full bg-amber-600"></div>
                                    </div>
                                    <div class="flex-1 pt-0.5">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-bold text-gray-900">{{ $history->status_label }}</p>
                                            <span class="text-xs font-semibold text-gray-500">{{ $history->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if ($history->description)
                                            <p class="mt-0.5 text-xs font-medium text-gray-700 leading-relaxed">{{ $history->description }}</p>
                                        @endif
                                        @if ($history->changedBy)
                                            <p class="mt-0.5 text-xs font-semibold text-gray-500">oleh {{ $history->changedBy->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm font-medium text-gray-500">Belum ada riwayat status.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Footer (Fixed Bottom) --}}
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 p-4 sm:p-5 bg-white shrink-0">
                    <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank"
                       class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-100 transition-colors shadow-xs">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2h2m2 4l2-2 2 2m-2-2v-6"/>
                        </svg>
                        Cetak Invoice
                    </a>
                    <button wire:click="$dispatch('closeModal')"
                            class="rounded-xl bg-amber-700 hover:bg-amber-800 px-5 py-2.5 text-sm font-bold text-white shadow-md transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
