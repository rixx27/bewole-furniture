@php
    use App\Services\OrderService;
    $user = auth()->user();
    $stats = app(OrderService::class)->getDashboardStats();
@endphp

<x-layouts::admin :title="'Dashboard'">

    {{-- Header Sambutan --}}
    <div class="mb-6 sm:mb-8">
        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-text-primary dark:text-black leading-snug">
            Selamat datang kembali, {{ $user->name }} 👋
        </h1>
        <p class="mt-1 text-xs sm:text-sm text-text-secondary">
            Berikut adalah ringkasan aktivitas toko furniture Anda hari ini.
        </p>
    </div>

    {{-- Grid Statistik --}}
    <div class="mb-6 sm:mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 sm:gap-5">
        {{-- Total Pesanan --}}
        <div class="stat-card rounded-xl border border-border bg-card p-4 sm:p-5 shadow-sm min-w-0">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Total Pesanan</p>
                    <p class="mt-1.5 text-2xl sm:text-3xl font-bold tracking-tight text-text-primary dark:text-black">{{ $stats['totalOrders'] }}</p>
                </div>
                <div class="flex h-10 w-10 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg bg-info/10 text-info">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-xs flex-wrap">
                <span class="inline-flex items-center gap-0.5 rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-600 shrink-0">
                    {{ $stats['pendingOrders'] }} pending
                </span>
                <span class="text-text-muted">pesanan</span>
            </div>
        </div>

        {{-- Total Pendapatan --}}
        <div class="stat-card rounded-xl border border-border bg-card p-4 sm:p-5 shadow-sm min-w-0">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Total Pendapatan</p>
                    <p class="mt-1.5 text-xl sm:text-2xl xl:text-3xl font-bold tracking-tight text-emerald-600 leading-tight" title="Rp {{ number_format($stats['totalRevenue'], 0, ',', '.') }}">
                        Rp {{ number_format($stats['totalRevenue'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex h-10 w-10 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-xs flex-wrap">
                <span class="inline-flex items-center gap-0.5 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-600 shrink-0">
                    {{ $stats['completedOrders'] }} selesai
                </span>
                <span class="text-text-muted">pesanan</span>
            </div>
        </div>

        {{-- Produk Terjual --}}
        <div class="stat-card rounded-xl border border-border bg-card p-4 sm:p-5 shadow-sm min-w-0">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Produk Terjual</p>
                    <p class="mt-1.5 text-2xl sm:text-3xl font-bold tracking-tight text-text-primary dark:text-black">{{ $stats['totalProductsSold'] }}</p>
                </div>
                <div class="flex h-10 w-10 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-xs flex-wrap">
                <span class="inline-flex items-center gap-0.5 rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-600 shrink-0">
                    {{ $stats['processingOrders'] }} diproses
                </span>
                <span class="text-text-muted">pesanan</span>
            </div>
        </div>

        {{-- Pesanan Dikirim --}}
        <div class="stat-card rounded-xl border border-border bg-card p-4 sm:p-5 shadow-sm min-w-0">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Dalam Pengiriman</p>
                    <p class="mt-1.5 text-2xl sm:text-3xl font-bold tracking-tight text-text-primary dark:text-black">{{ $stats['shippedOrders'] }}</p>
                </div>
                <div class="flex h-10 w-10 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-xs flex-wrap">
                <span class="text-text-muted">{{ $stats['completedOrders'] }} selesai</span>
            </div>
        </div>
    </div>

    {{-- Grafik --}}
    <div class="mb-6 sm:mb-8 grid grid-cols-1 gap-6 xl:grid-cols-2">
        {{-- Grafik Pesanan Bulanan --}}
        <div class="rounded-xl border border-border bg-card p-4 sm:p-6 shadow-sm min-w-0 w-full overflow-hidden">
            <div class="mb-4 sm:mb-5 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="text-base font-semibold text-text-primary dark:text-primary">Pesanan Bulanan</h3>
                    <p class="mt-0.5 text-xs text-text-muted">Gambaran pesanan tahun {{ now()->year }}</p>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-secondary-light">
                    <svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="flex h-56 sm:h-60 w-full min-w-0 items-center justify-center rounded-lg bg-bg-secondary overflow-hidden">
                @if (collect($stats['chartData'])->sum('total') > 0)
                    <div class="w-full h-full p-2 sm:p-4 overflow-hidden">
                        <div class="flex items-end justify-between gap-0.5 sm:gap-1.5 h-full w-full min-w-0">
                            @foreach ($stats['chartData'] as $data)
                                <div class="flex-1 min-w-0 flex flex-col items-center justify-end gap-1 h-full">
                                    <span class="text-[9px] sm:text-[10px] font-semibold text-text-muted text-center block w-full leading-none">{{ $data['count'] }}</span>
                                    <div class="w-full max-w-[14px] sm:max-w-[26px] rounded-t bg-primary/80 hover:bg-primary transition-colors"
                                         style="height: {{ max(4, ($data['count'] / max(1, collect($stats['chartData'])->max('count'))) * 160) }}px">
                                    </div>
                                    <span class="text-[9px] sm:text-[10px] font-medium text-text-muted text-center block w-full leading-none tracking-tighter">{{ $data['month'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center p-4">
                        <svg class="mx-auto mb-3 h-10 w-10 text-text-muted/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                        </svg>
                        <p class="text-xs sm:text-sm font-medium text-text-muted">Data akan muncul setelah ada pesanan masuk</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Grafik Pendapatan Bulanan --}}
        <div class="rounded-xl border border-border bg-card p-4 sm:p-6 shadow-sm min-w-0 w-full overflow-hidden">
            <div class="mb-4 sm:mb-5 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="text-base font-semibold text-text-primary dark:text-primary">Pendapatan Bulanan</h3>
                    <p class="mt-0.5 text-xs text-text-muted">Gambaran pendapatan tahun {{ now()->year }}</p>
                </div>
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-secondary-light">
                    <svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="flex h-56 sm:h-60 w-full min-w-0 items-center justify-center rounded-lg bg-bg-secondary overflow-hidden">
                @if (collect($stats['chartData'])->sum('total') > 0)
                    <div class="w-full h-full p-2 sm:p-4 overflow-hidden">
                        <div class="flex items-end justify-between gap-0.5 sm:gap-1.5 h-full w-full min-w-0">
                            @foreach ($stats['chartData'] as $data)
                                @php
                                    $revenueValue = $data['total'];
                                    if ($revenueValue >= 1000000) {
                                        $formattedRev = number_format($revenueValue / 1000000, 1) . 'M';
                                    } elseif ($revenueValue >= 1000) {
                                        $formattedRev = number_format($revenueValue / 1000, 0) . 'k';
                                    } elseif ($revenueValue > 0) {
                                        $formattedRev = (string)$revenueValue;
                                    } else {
                                        $formattedRev = '0';
                                    }
                                @endphp
                                <div class="flex-1 min-w-0 flex flex-col items-center justify-end gap-1 h-full">
                                    <span class="text-[8px] sm:text-[10px] font-semibold text-emerald-600 text-center block w-full leading-none tracking-tight" title="Rp {{ number_format($data['total'], 0, ',', '.') }}">
                                        {{ $formattedRev }}
                                    </span>
                                    <div class="w-full max-w-[14px] sm:max-w-[26px] rounded-t bg-emerald-400/80 hover:bg-emerald-500 transition-colors"
                                         style="height: {{ max(4, ($data['total'] / max(1, collect($stats['chartData'])->max('total'))) * 160) }}px">
                                    </div>
                                    <span class="text-[9px] sm:text-[10px] font-medium text-text-muted text-center block w-full leading-none tracking-tighter">{{ $data['month'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center p-4">
                        <svg class="mx-auto mb-3 h-10 w-10 text-text-muted/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
                        </svg>
                        <p class="text-xs sm:text-sm font-medium text-text-muted">Data akan muncul setelah ada pesanan masuk</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="mb-6 sm:mb-8 grid grid-cols-1 gap-6 xl:grid-cols-2">
        {{-- Pesanan Terbaru --}}
        <div class="rounded-xl border border-border bg-card shadow-sm min-w-0 overflow-hidden">
            <div class="flex items-center justify-between border-b border-border px-4 sm:px-6 py-3.5 sm:py-4">
                <div>
                    <h3 class="text-base font-semibold text-text-primary dark:text-primary">Pesanan Terbaru</h3>
                    <p class="text-xs text-text-muted">5 pesanan terakhir</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-medium text-primary hover:text-primary-light transition-colors shrink-0">
                    Lihat Semua &rarr;
                </a>
            </div>
            <div class="w-full overflow-x-auto min-w-0">
                <table class="w-full min-w-full divide-y divide-border/50 text-left">
                    <thead>
                        <tr class="bg-bg-secondary/50">
                            <th class="px-4 sm:px-6 py-3 text-xs font-semibold uppercase tracking-wider text-text-muted whitespace-nowrap">Kode Pesanan</th>
                            <th class="px-4 sm:px-6 py-3 text-xs font-semibold uppercase tracking-wider text-text-muted whitespace-nowrap">Pelanggan</th>
                            <th class="px-4 sm:px-6 py-3 text-xs font-semibold uppercase tracking-wider text-text-muted whitespace-nowrap">Produk</th>
                            <th class="px-4 sm:px-6 py-3 text-xs font-semibold uppercase tracking-wider text-text-muted whitespace-nowrap">Status</th>
                            <th class="px-4 sm:px-6 py-3 text-xs font-semibold uppercase tracking-wider text-text-muted whitespace-nowrap">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        @forelse ($stats['recentOrders'] as $order)
                            <tr class="transition-colors hover:bg-bg-secondary/30">
                                <td class="px-4 sm:px-6 py-3 text-xs sm:text-sm font-mono font-medium text-text-primary dark:text-black whitespace-nowrap">{{ $order->order_code }}</td>
                                <td class="px-4 sm:px-6 py-3 text-xs sm:text-sm text-text-secondary whitespace-nowrap">{{ $order->customer_name }}</td>
                                <td class="px-4 sm:px-6 py-3 text-xs sm:text-sm text-text-secondary whitespace-nowrap">{{ $order->product?->name ?? '-' }}</td>
                                <td class="px-4 sm:px-6 py-3 whitespace-nowrap">
                                    @php $color = $order->status_color; @endphp
                                    <span class="inline-flex items-center gap-1 rounded-full bg-{{ $color }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $color }}-700 dark:bg-{{ $color }}-950 dark:text-{{ $color }}-300">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-3 text-xs sm:text-sm text-text-muted whitespace-nowrap">{{ $order->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 sm:px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="mb-2 h-8 w-8 text-text-muted/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                                        </svg>
                                        <p class="text-sm text-text-muted">Belum ada pesanan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Ulasan Terbaru --}}
        <div class="rounded-xl border border-border bg-card shadow-sm min-w-0 overflow-hidden">
            <div class="flex items-center justify-between border-b border-border px-4 sm:px-6 py-3.5 sm:py-4">
                <div>
                    <h3 class="text-base font-semibold text-text-primary dark:text-primary">Ulasan Terbaru</h3>
                    <p class="text-xs text-text-muted">Umpan balik pelanggan terbaru</p>
                </div>
                <a href="{{ route('admin.product-reviews.index') }}" class="text-xs font-medium text-primary hover:text-primary-light transition-colors shrink-0">
                    Lihat Semua &rarr;
                </a>
            </div>
            <div class="w-full overflow-x-auto min-w-0">
                <table class="w-full min-w-full divide-y divide-border/50 text-left">
                    <thead>
                        <tr class="bg-bg-secondary/50">
                            <th class="px-4 sm:px-6 py-3 text-xs font-semibold uppercase tracking-wider text-text-muted whitespace-nowrap">Pelanggan</th>
                            <th class="px-4 sm:px-6 py-3 text-xs font-semibold uppercase tracking-wider text-text-muted whitespace-nowrap">Produk</th>
                            <th class="px-4 sm:px-6 py-3 text-xs font-semibold uppercase tracking-wider text-text-muted whitespace-nowrap">Rating</th>
                            <th class="px-4 sm:px-6 py-3 text-xs font-semibold uppercase tracking-wider text-text-muted whitespace-nowrap">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        <tr>
                            <td colspan="4" class="px-4 sm:px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="mb-2 h-8 w-8 text-text-muted/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                    </svg>
                                    <p class="text-sm text-text-muted">Belum ada ulasan</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Aksi Cepat --}}
    <div>
        <h3 class="mb-3 sm:mb-4 text-base font-semibold text-text-primary dark:text-black">Aksi Cepat</h3>
        <div class="grid grid-cols-1 gap-3.5 sm:gap-4 sm:grid-cols-2 xl:grid-cols-4">
            {{-- Tambah Produk --}}
            <a href="{{ route('admin.products.create') }}" class="group flex items-center gap-3.5 sm:gap-4 rounded-xl border border-border bg-card p-4 sm:p-5 min-w-0 shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-md">
                <div class="flex h-11 w-11 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary transition-colors group-hover:bg-primary group-hover:text-white">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-text-primary dark:text-primary truncate">Tambah Produk</p>
                    <p class="text-xs text-text-muted truncate">Buat produk furniture baru</p>
                </div>
            </a>

            {{-- Buat Kategori --}}
            <a href="{{ route('admin.categories.index') }}" class="group flex items-center gap-3.5 sm:gap-4 rounded-xl border border-border bg-card p-4 sm:p-5 min-w-0 shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-md">
                <div class="flex h-11 w-11 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-lg bg-info/10 text-info transition-colors group-hover:bg-info group-hover:text-white">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-text-primary dark:text-info truncate">Buat Kategori</p>
                    <p class="text-xs text-text-muted truncate">Atur kategori produk</p>
                </div>
            </a>

            {{-- Lihat Pesanan --}}
            <a href="{{ route('admin.orders.index') }}" class="group flex items-center gap-3.5 sm:gap-4 rounded-xl border border-border bg-card p-4 sm:p-5 min-w-0 shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-md">
                <div class="flex h-11 w-11 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-lg bg-warning/10 text-warning transition-colors group-hover:bg-warning group-hover:text-white">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-text-primary dark:text-warning truncate">Lihat Pesanan</p>
                    <p class="text-xs text-text-muted truncate">Kelola pesanan pelanggan</p>
                </div>
            </a>

            {{-- Pengaturan --}}
            <a href="{{ route('admin.settings.index') }}" class="group flex items-center gap-3.5 sm:gap-4 rounded-xl border border-border bg-card p-4 sm:p-5 min-w-0 shadow-sm transition-all duration-200 hover:border-primary/30 hover:shadow-md">
                <div class="flex h-11 w-11 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-lg bg-success/10 text-success transition-colors group-hover:bg-success group-hover:text-white">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-text-primary dark:text-success truncate">Pengaturan Website</p>
                    <p class="text-xs text-text-muted truncate">Konfigurasi toko Anda</p>
                </div>
            </a>
        </div>
    </div>

</x-layouts::admin>
