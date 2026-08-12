<div>
    {{-- Filter & Search --}}
    <div class="mb-6">
        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari kode pesanan, nama, atau telepon..."
                       class="w-full rounded-lg border border-border bg-card py-2.5 pl-10 pr-4 text-sm text-text-primary placeholder-text-muted focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
            </div>
            <select wire:model.live="statusFilter"
                    class="rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
                <option value="">Semua Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
            <select wire:model.live="paymentFilter"
                    class="rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
                <option value="">Semua Pembayaran</option>
                @foreach($paymentStatuses as $paymentStatus)
                    <option value="{{ $paymentStatus->value }}">{{ $paymentStatus->label() }}</option>
                @endforeach
            </select>
            <select wire:model.live="shippingFilter"
                    class="rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
                <option value="">Semua Pengiriman</option>
                @foreach($shippingMethods as $method)
                    <option value="{{ $method->value }}">{{ $method->label() }}</option>
                @endforeach
            </select>
            @if ($search || $statusFilter || $paymentFilter || $shippingFilter)
                <button wire:click="resetFilters"
                        class="rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                    Reset
                </button>
            @endif
        </div>
    </div>

    {{-- Table Card --}}
    <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-bg-secondary/50">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">
                            <button wire:click="sortBy('order_code')" class="flex items-center gap-1 hover:text-text-primary">
                                Kode Order
                                @if ($sortField === 'order_code')
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        @endif
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Produk</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Qty</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">
                            <button wire:click="sortBy('total_price')" class="flex items-center gap-1 hover:text-text-primary">
                                Grand Total
                                @if ($sortField === 'total_price')
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        @endif
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Pengiriman</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Pembayaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">
                            <button wire:click="sortBy('created_at')" class="flex items-center gap-1 hover:text-text-primary">
                                Tanggal
                                @if ($sortField === 'created_at')
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        @endif
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-text-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/50">
                    @forelse ($orders as $order)
                        <tr class="transition-colors hover:bg-bg-secondary/30">
                            <td class="px-6 py-4">
                                <span class="text-sm font-mono font-medium text-text-primary dark:text-black">{{ $order->order_code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p class="font-medium text-text-primary dark:text-black">{{ $order->customer_name }}</p>
                                    <p class="text-xs text-text-muted">{{ $order->customer_phone }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-text-secondary">{{ $order->product?->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-text-secondary">{{ $order->quantity }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-text-primary dark:text-black">{{ $order->formatted_total_price }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-text-secondary">{{ $order->shipping_method_label }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $color = $order->status_color;
                                @endphp
                                <span class="inline-flex items-center gap-1 rounded-full bg-{{ $color }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $color }}-700 dark:bg-{{ $color }}-950 dark:text-{{ $color }}-300">
                                    <span class="h-1.5 w-1.5 rounded-full bg-{{ $color }}-500"></span>
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $color = $order->payment_status_color;
                                @endphp
                                <span class="inline-flex items-center gap-1 rounded-full bg-{{ $color }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $color }}-700 dark:bg-{{ $color }}-950 dark:text-{{ $color }}-300">
                                    {{ $order->payment_status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Detail --}}
                                    <button wire:click="openDetail({{ $order->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs font-medium text-text-secondary hover:bg-bg-secondary transition-colors"
                                            title="Lihat detail">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Detail
                                    </button>

                                    {{-- Status --}}
                                    @if (in_array($order->status, ['pending', 'confirmed', 'processing', 'ready_to_ship']))
                                        <button wire:click="openStatus({{ $order->id }})"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs font-medium text-indigo-600 hover:bg-indigo-50 transition-colors"
                                                title="Ubah status">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Status
                                        </button>
                                    @endif

                                    {{-- Shipping --}}
                                    @if ($order->status === 'ready_to_ship')
                                        <button wire:click="openShipping({{ $order->id }})"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs font-medium text-cyan-600 hover:bg-cyan-50 transition-colors"
                                                title="Atur pengiriman">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                            </svg>
                                            Kirim
                                        </button>
                                    @endif

                                    {{-- Payment --}}
                                    <button wire:click="openPayment({{ $order->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs font-medium text-emerald-600 hover:bg-emerald-50 transition-colors"
                                            title="Ubah pembayaran">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Bayar
                                    </button>

                                    {{-- Hapus --}}
                                    <button wire:click="confirmDelete({{ $order->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors"
                                            title="Hapus pesanan">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-bg-secondary">
                                        <svg class="h-8 w-8 text-text-muted/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-text-primary dark:text-white">Belum ada pesanan</p>
                                    <p class="mt-1 text-xs text-text-muted">Pesanan akan muncul setelah customer melakukan checkout.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($orders->hasPages())
            <div class="border-t border-border px-6 py-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    @if ($showDetailModal && $selectedOrderId)
        <livewire:admin.order.order-detail :key="'detail-' . $selectedOrderId" />
    @endif

    {{-- Status Modal --}}
    @if ($showStatusModal && $selectedOrderId)
        <livewire:admin.order.order-status-manager :key="'status-' . $selectedOrderId" />
    @endif

    {{-- Shipping Modal --}}
    @if ($showShippingModal && $selectedOrderId)
        <livewire:admin.order.order-shipping :key="'shipping-' . $selectedOrderId" />
    @endif

    {{-- Payment Modal --}}
    @if ($showPaymentModal && $selectedOrderId)
        <livewire:admin.order.order-payment :key="'payment-' . $selectedOrderId" />
    @endif

    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-init="$el.style.display = 'flex'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeAllModals"></div>
            <div class="relative w-full max-w-md rounded-xl bg-card p-6 shadow-2xl border border-border"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                <div class="mb-5 flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-600 dark:bg-red-950 dark:text-red-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-text-primary dark:text-white">Konfirmasi Hapus</h3>
                        <p class="text-sm text-text-secondary">Apakah Anda yakin ingin menghapus pesanan ini?</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button wire:click="closeAllModals"
                            class="rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                        Batal
                    </button>
                    <button wire:click="deleteOrder"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
