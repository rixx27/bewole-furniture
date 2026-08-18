<div>
    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">Laporan</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola dan export data transaksi Bewole.</p>
            </div>
            <div>
                <button type="button"
                        wire:click="exportExcel"
                        wire:loading.attr="disabled"
                        class="inline-flex w-full sm:w-auto items-center justify-center gap-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                    <span wire:loading.remove wire:target="exportExcel" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-file-excel text-base"></i>
                        <span>Export Excel</span>
                    </span>
                    <span wire:loading.inline-flex wire:target="exportExcel" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-base"></i>
                        <span>Menyiapkan laporan...</span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-4 text-xs font-bold uppercase tracking-wider text-gray-500">
            <i class="fa-solid fa-filter text-amber-700"></i>
            <span>Filter Transaksi</span>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Tanggal Mulai --}}
            <div>
                <label for="startDate" class="mb-1.5 block text-xs font-bold text-gray-700">
                    <i class="fa-regular fa-calendar mr-1 text-gray-400"></i>
                    Tanggal Mulai
                </label>
                <input type="date"
                       id="startDate"
                       wire:model.live="startDate"
                       class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm font-medium text-gray-900 focus:border-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500/20 transition-colors shadow-2xs">
            </div>

            {{-- Tanggal Selesai --}}
            <div>
                <label for="endDate" class="mb-1.5 block text-xs font-bold text-gray-700">
                    <i class="fa-regular fa-calendar mr-1 text-gray-400"></i>
                    Tanggal Selesai
                </label>
                <input type="date"
                       id="endDate"
                       wire:model.live="endDate"
                       class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm font-medium text-gray-900 focus:border-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500/20 transition-colors shadow-2xs">
            </div>

            {{-- Status Pesanan --}}
            <div>
                <label for="status" class="mb-1.5 block text-xs font-bold text-gray-700">
                    <i class="fa-solid fa-tag mr-1 text-gray-400"></i>
                    Status Pesanan
                </label>
                <select id="status"
                        wire:model.live="status"
                        class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2.5 text-sm font-medium text-gray-900 focus:border-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500/20 transition-colors shadow-2xs">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Pencarian --}}
            <div>
                <label for="search" class="mb-1.5 block text-xs font-bold text-gray-700">
                    <i class="fa-solid fa-magnifying-glass mr-1 text-gray-400"></i>
                    Pencarian
                </label>
                <div class="relative">
                    <input type="text"
                           id="search"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Kode order, customer, WA..."
                           class="w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-3.5 pr-8 text-sm font-medium text-gray-900 placeholder-gray-400 focus:border-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500/20 transition-colors shadow-2xs">
                    @if($search)
                        <button type="button"
                                wire:click="$set('search', '')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Date Validation Error Alert --}}
        @error('dateRange')
            <div class="mt-4 flex items-center gap-2 rounded-xl bg-red-50 p-3 text-xs font-semibold text-red-700 border border-red-200">
                <i class="fa-solid fa-triangle-exclamation text-sm text-red-600 shrink-0"></i>
                <span>{{ $message }}</span>
            </div>
        @enderror

        {{-- Filter Actions Bar --}}
        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <div class="text-xs text-gray-500">
                @if($startDate || $endDate || $status || $search)
                    <span class="inline-flex items-center gap-1.5 font-medium text-amber-800">
                        <span class="h-2 w-2 rounded-full bg-amber-600 animate-pulse"></span>
                        Filter aktif diterapkan
                    </span>
                @else
                    <span>Menampilkan seluruh data tanpa filter.</span>
                @endif
            </div>

            @if($startDate || $endDate || $status || $search)
                <button type="button"
                        wire:click="resetFilters"
                        class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-gray-50 hover:bg-gray-100 px-3.5 py-1.5 text-xs font-bold text-gray-700 transition-colors shadow-2xs cursor-pointer">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                    <span>Reset Filter</span>
                </button>
            @endif
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total Pesanan --}}
        <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Pesanan</p>
                    <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                        {{ number_format($totalOrders, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-700 border border-amber-100">
                    <i class="fa-solid fa-cart-shopping text-xl"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500">
                <span>Semua pesanan sesuai filter</span>
            </div>
        </div>

        {{-- Total Pendapatan --}}
        <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Pendapatan</p>
                    <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-emerald-700 tracking-tight">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-100">
                    <i class="fa-solid fa-money-bill-wave text-xl"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500">
                <span>Transaksi lunas / selesai</span>
            </div>
        </div>

        {{-- Pesanan Selesai --}}
        <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Pesanan Selesai</p>
                    <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-blue-700 tracking-tight">
                        {{ number_format($completedOrders, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-700 border border-blue-100">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500">
                <span>Status selesai dikirim</span>
            </div>
        </div>

        {{-- Pesanan Dibatalkan --}}
        <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Pesanan Dibatalkan</p>
                    <p class="mt-2 text-2xl sm:text-3xl font-extrabold text-red-600 tracking-tight">
                        {{ number_format($cancelledOrders, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-600 border border-red-100">
                    <i class="fa-solid fa-ban text-xl"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500">
                <span>Pesanan tidak dilanjutkan</span>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/80 text-gray-700">
                        <th class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider w-14">No</th>
                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider">Kode Order</th>
                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider">Tanggal</th>
                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider">Customer</th>
                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider">Produk</th>
                        <th class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider">Qty</th>
                        <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider">Total</th>
                        <th class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider">Status</th>
                        <th class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider">Payment Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $index => $order)
                        <tr class="transition-colors hover:bg-amber-50/30">
                            {{-- No --}}
                            <td class="px-5 py-4 text-center text-xs font-bold text-gray-500">
                                {{ $orders->firstItem() + $index }}
                            </td>

                            {{-- Kode Order --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="font-mono text-xs font-extrabold text-amber-900 bg-amber-50/80 border border-amber-200/80 px-2.5 py-1 rounded-lg">
                                    {{ $order->order_code }}
                                </span>
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-5 py-4 whitespace-nowrap text-xs font-medium text-gray-600">
                                {{ $order->created_at ? $order->created_at->format('d/m/Y') : '-' }}
                            </td>

                            {{-- Customer --}}
                            <td class="px-5 py-4">
                                <div class="min-w-[140px]">
                                    <p class="text-sm font-bold text-gray-900">{{ $order->customer_name }}</p>
                                    @php $phone = $order->whatsapp_number ?: $order->customer_phone; @endphp
                                    @if($phone)
                                        <p class="text-xs text-gray-500 font-medium">
                                            <i class="fa-brands fa-whatsapp text-emerald-600 mr-0.5"></i>
                                            {{ $phone }}
                                        </p>
                                    @endif
                                </div>
                            </td>

                            {{-- Produk --}}
                            <td class="px-5 py-4">
                                <div class="min-w-[150px] text-sm font-semibold text-gray-800">
                                    {{ $order->product?->name ?? 'Produk Dihapus' }}
                                </div>
                            </td>

                            {{-- Qty --}}
                            <td class="px-5 py-4 text-center whitespace-nowrap text-xs font-bold text-gray-700">
                                {{ $order->quantity }}
                            </td>

                            {{-- Total --}}
                            <td class="px-5 py-4 text-right whitespace-nowrap text-sm font-extrabold text-gray-900">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </td>

                            {{-- Status Pesanan --}}
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                @php
                                    $st = \App\Enums\OrderStatus::tryFrom($order->status);
                                    $statusLabel = $st ? $st->label() : $order->status;
                                    $badgeClass = match($order->status) {
                                        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'confirmed' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'awaiting_payment' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                        'payment_received' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'in_production' => 'bg-stone-100 text-stone-700 border-stone-200',
                                        'quality_control' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'ready_to_ship' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                        'shipped' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        default => 'bg-gray-100 text-gray-700 border-gray-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-bold {{ $badgeClass }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $statusLabel }}
                                </span>
                            </td>

                            {{-- Payment Status --}}
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                @php
                                    $pay = \App\Enums\PaymentStatus::tryFrom($order->payment_status);
                                    $payLabel = $pay ? $pay->label() : $order->payment_status;
                                    $payBadgeClass = match($order->payment_status) {
                                        'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'unpaid' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'failed' => 'bg-red-50 text-red-700 border-red-200',
                                        'refunded' => 'bg-gray-100 text-gray-700 border-gray-200',
                                        default => 'bg-gray-50 text-gray-600 border-gray-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-lg border px-2 py-0.5 text-xs font-bold {{ $payBadgeClass }}">
                                    {{ $payLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-700 border border-amber-100 mb-3">
                                    <i class="fa-solid fa-inbox text-2xl"></i>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900">Tidak ada data transaksi</h3>
                                <p class="mt-1 text-xs text-gray-500">
                                    Tidak ada transaksi yang cocok dengan filter yang Anda tentukan.
                                </p>
                                @if($startDate || $endDate || $status || $search)
                                    <button type="button"
                                            wire:click="resetFilters"
                                            class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-amber-700 px-4 py-2 text-xs font-bold text-white hover:bg-amber-800 transition-colors">
                                        <i class="fa-solid fa-rotate-left"></i>
                                        Reset Semua Filter
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
            <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
