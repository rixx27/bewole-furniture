@php
    $title = 'Laporan Pesanan';
@endphp

<x-layouts::admin :title="$title">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-black">Laporan Pesanan</h2>
                <p class="mt-1 text-sm text-text-secondary">Lihat dan export laporan pesanan berdasarkan filter.</p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="window.print()"
                        class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-all">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2h2m2 4l2-2 2 2m-2-2v-6"/>
                    </svg>
                    Cetak PDF
                </a>
                <a href="{{ route('admin.reports.orders.pdf', request()->query()) }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 transition-all">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="mb-6 rounded-xl border border-border bg-card p-6 shadow-sm">
        <form method="GET" action="{{ route('admin.reports.orders') }}">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <div>
                    <label for="start_date" class="mb-1 block text-xs font-medium text-text-primary">Tanggal Awal</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                           class="w-full rounded-lg border border-border bg-card p-2.5 text-sm text-text-primary focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
                </div>
                <div>
                    <label for="end_date" class="mb-1 block text-xs font-medium text-text-primary">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                           class="w-full rounded-lg border border-border bg-card p-2.5 text-sm text-text-primary focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
                </div>
                <div>
                    <label for="status" class="mb-1 block text-xs font-medium text-text-primary">Status</label>
                    <select name="status" id="status"
                            class="w-full rounded-lg border border-border bg-card p-2.5 text-sm text-text-primary focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
                        <option value="">Semua Status</option>
                        @foreach (\App\Enums\OrderStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="shipping_method" class="mb-1 block text-xs font-medium text-text-primary">Pengiriman</label>
                    <select name="shipping_method" id="shipping_method"
                            class="w-full rounded-lg border border-border bg-card p-2.5 text-sm text-text-primary focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
                        <option value="">Semua Metode</option>
                        @foreach (\App\Enums\ShippingMethod::cases() as $method)
                            <option value="{{ $method->value }}" {{ request('shipping_method') == $method->value ? 'selected' : '' }}>{{ $method->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
                        Filter
                    </button>
                    @if (request()->anyFilled(['start_date', 'end_date', 'status', 'shipping_method']))
                        <a href="{{ route('admin.reports.orders') }}"
                           class="rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Jumlah Pesanan</p>
            <p class="mt-2 text-3xl font-bold text-text-primary dark:text-white">{{ $totalOrders }}</p>
        </div>
        <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Total Pendapatan</p>
            <p class="mt-2 text-3xl font-bold text-emerald-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-border bg-card p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Rata-rata Pesanan</p>
            <p class="mt-2 text-3xl font-bold text-text-primary dark:text-white">Rp {{ number_format($totalOrders > 0 ? $totalRevenue / $totalOrders : 0, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="report-table">
                <thead>
                    <tr class="border-b border-border bg-bg-secondary/50">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Kode</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Produk</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Qty</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Grand Total</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Pengiriman</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/50">
                    @forelse ($orders as $index => $order)
                        <tr class="transition-colors hover:bg-bg-secondary/30">
                            <td class="px-6 py-4 text-sm text-text-secondary">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-mono font-medium text-text-primary dark:text-white">{{ $order->order_code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p class="font-medium text-text-primary dark:text-white">{{ $order->customer_name }}</p>
                                    <p class="text-xs text-text-muted">{{ $order->customer_phone }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-text-secondary">{{ $order->product?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-text-secondary">{{ $order->quantity }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-text-primary dark:text-white">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-text-secondary">{{ $order->shipping_method_label }}</td>
                            <td class="px-6 py-4">
                                @php $color = $order->status_color; @endphp
                                <span class="inline-flex items-center gap-1 rounded-full bg-{{ $color }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $color }}-700 dark:bg-{{ $color }}-950 dark:text-{{ $color }}-300">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-text-muted">{{ $order->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <p class="text-sm text-text-muted">Tidak ada data pesanan untuk filter yang dipilih.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="border-t border-border px-6 py-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    {{-- Footer Info --}}
    <div class="mt-6 text-center text-xs text-text-muted">
        <p>Laporan dicetak pada {{ now()->format('d F Y H:i') }} oleh {{ auth()->user()->name }}</p>
    </div>

    <style>
        @media print {
            body { background: white; }
            .no-print { display: none !important; }
            @page { margin: 15mm; }
        }
    </style>

</x-layouts::admin>
