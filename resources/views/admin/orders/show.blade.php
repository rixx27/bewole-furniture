@php
    $title = 'Detail Pesanan #' . $order->order_code;
@endphp

<x-layouts::admin :title="$title">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.orders.index') }}" class="text-text-muted hover:text-text-primary transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-black">Detail Pesanan</h2>
                        <p class="mt-1 text-sm text-text-muted font-mono">#{{ $order->order_code }}</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank"
                   class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-all">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2h2m2 4l2-2 2 2m-2-2v-6"/>
                    </svg>
                    Cetak Invoice
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Main Content (2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Informasi Produk --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm">
                <h3 class="mb-4 text-base font-semibold text-text-primary dark:text-white">Informasi Produk</h3>
                <div class="flex items-start gap-4">
                    <div class="h-20 w-20 shrink-0 overflow-hidden rounded-lg bg-bg-secondary">
                        @if ($order->product && $order->product->thumbnail)
                            <img src="{{ asset('storage/' . $order->product->thumbnail) }}" alt="{{ $order->product->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-text-muted">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="text-lg font-semibold text-text-primary dark:text-white">{{ $order->product?->name ?? 'Produk tidak tersedia' }}</p>
                        <div class="mt-2 grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-text-muted">Harga Satuan</span>
                                <p class="font-medium text-text-primary dark:text-white">{{ $order->product?->formatted_price ?? 'Rp 0' }}</p>
                            </div>
                            <div>
                                <span class="text-text-muted">Jumlah</span>
                                <p class="font-medium text-text-primary dark:text-white">{{ $order->quantity }}</p>
                            </div>
                            <div>
                                <span class="text-text-muted">Subtotal</span>
                                <p class="font-semibold text-text-primary dark:text-white">Rp {{ number_format($order->quantity * ($order->product?->price ?? 0), 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <span class="text-text-muted">Grand Total</span>
                                <p class="font-bold text-lg text-text-primary dark:text-white">{{ $order->formatted_total_price }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rincian Item Pesanan & Customisasi Bahan --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm">
                <h3 class="mb-4 text-base font-semibold text-text-primary dark:text-white">Rincian Item & Snapshot Bahan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-bg-secondary text-text-muted uppercase font-bold border-b border-border">
                            <tr>
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-4 py-3">Bahan Dudukan</th>
                                <th class="px-4 py-3">Bahan Packing</th>
                                <th class="px-4 py-3 text-right">Subtotal Item</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="font-bold text-text-primary dark:text-white">{{ $item->product?->name ?? 'Produk' }}</p>
                                        <p class="text-text-muted">Qty: {{ $item->quantity }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($item->seat_material_name)
                                            <p class="font-semibold text-amber-700 dark:text-amber-400">{{ $item->seat_material_name }}</p>
                                            <p class="text-[11px] text-text-muted">{{ $item->seat_usage_meter }}m @ Rp {{ number_format($item->seat_price_per_meter, 0, ',', '.') }}/m</p>
                                            <p class="font-bold text-text-primary dark:text-white">Biaya: Rp {{ number_format($item->seat_material_cost, 0, ',', '.') }}</p>
                                        @else
                                            <span class="text-text-muted italic">Tidak ada (Unfinished)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($item->packing_material_name)
                                            <p class="font-semibold text-emerald-700 dark:text-emerald-400">{{ $item->packing_material_name }}</p>
                                            <p class="text-[11px] text-text-muted">{{ $item->packing_usage_meter }}m @ Rp {{ number_format($item->packing_price_per_meter, 0, ',', '.') }}/m</p>
                                            <p class="font-bold text-text-primary dark:text-white">Biaya: Rp {{ number_format($item->packing_material_cost, 0, ',', '.') }}</p>
                                        @else
                                            <span class="text-text-muted italic">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-text-primary dark:text-white">
                                        Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Summary Breakdown --}}
                <div class="mt-4 pt-4 border-t border-border space-y-1.5 text-xs text-right">
                    <p class="text-text-muted">Total Biaya Custom Dudukan: <span class="font-bold text-text-primary dark:text-white">Rp {{ number_format($order->customization_fee, 0, ',', '.') }}</span></p>
                    <p class="text-text-muted">Total Biaya Packing: <span class="font-bold text-text-primary dark:text-white">Rp {{ number_format($order->packing_fee, 0, ',', '.') }}</span></p>
                    <p class="text-sm font-bold text-text-primary dark:text-white pt-1">Grand Total: {{ $order->formatted_total_price }}</p>
                </div>
            </div>

            {{-- Timeline Status --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm" x-data="{ activePhoto: null }">
                <h3 class="mb-4 text-base font-semibold text-text-primary dark:text-white">Riwayat Status</h3>
                <div class="space-y-0">
                    @forelse ($order->statusHistories as $history)
                        <div class="relative flex gap-4 pb-6">
                            @if (!$loop->last)
                                <div class="absolute left-[11px] top-6 h-full w-0.5 bg-border"></div>
                            @endif
                            <div class="relative flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2" style="border-color: var(--color-{{ $history->status_color }}-500);">
                                <div class="h-2 w-2 rounded-full" style="background-color: var(--color-{{ $history->status_color }}-500);"></div>
                            </div>
                            <div class="flex-1 pt-0.5">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-text-primary dark:text-white">{{ $history->status_label }}</p>
                                    <span class="text-xs text-text-muted">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if ($history->description)
                                    <p class="mt-0.5 text-xs text-text-muted">{{ $history->description }}</p>
                                @endif
                                @if ($history->changedBy)
                                    <p class="mt-0.5 text-xs text-text-muted">— {{ $history->changedBy->name }}</p>
                                @endif
                                @if ($history->photo)
                                    <div class="mt-2 flex items-center flex-wrap gap-2">
                                        <button type="button"
                                                @click="activePhoto = {
                                                    url: '{{ $history->photo_url }}',
                                                    downloadUrl: '{{ $history->download_url }}',
                                                    status: '{{ $history->status_label }}',
                                                    date: '{{ $history->created_at->translatedFormat('d F Y, H:i') }} WIB',
                                                    lat: '{{ $history->latitude }}',
                                                    lng: '{{ $history->longitude }}'
                                                }"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-amber-300 bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-900 hover:bg-amber-100 transition-colors shadow-2xs cursor-pointer">
                                            <svg class="h-3.5 w-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Lihat Foto Progres</span>
                                        </button>
                                        <a href="{{ $history->download_url }}"
                                           class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-2 py-1 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors shadow-2xs"
                                           title="Unduh Foto">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            <span>Unduh</span>
                                        </a>
                                        @if ($history->latitude && $history->longitude)
                                            <a href="https://www.google.com/maps?q={{ $history->latitude }},{{ $history->longitude }}" target="_blank"
                                               class="text-[11px] font-semibold text-text-muted hover:text-primary flex items-center gap-1">
                                                <svg class="h-3 w-3 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                </svg>
                                                <span>GPS: {{ $history->latitude }}, {{ $history->longitude }}</span>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-text-muted">Belum ada riwayat status.</p>
                    @endforelse
                </div>

                {{-- Modal Lightbox Admin --}}
                <template x-teleport="body">
                    <div x-show="activePhoto"
                         x-cloak
                         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0">

                        <div class="fixed inset-0 bg-black/75 backdrop-blur-sm" @click="activePhoto = null"></div>

                        <div class="relative w-full max-w-2xl sm:max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl border border-gray-200 z-10 flex flex-col"
                             style="max-height: 90vh; height: auto;"
                             @click.away="activePhoto = null">
                            <div class="flex items-center justify-between border-b border-gray-100 px-5 sm:px-6 py-3.5 bg-gray-50/90 shrink-0"
                                 style="flex-shrink: 0;">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100 text-amber-800">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900" x-text="'Foto Progres: ' + (activePhoto?.status || '')"></h4>
                                        <p class="text-xs text-gray-500 font-medium" x-text="activePhoto?.date || ''"></p>
                                    </div>
                                </div>

                                <button type="button" @click="activePhoto = null" class="rounded-xl p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors cursor-pointer">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex-1 min-h-0 bg-gray-950 flex items-center justify-center p-2 sm:p-4 overflow-hidden"
                                 style="flex: 1 1 auto; min-height: 0; background-color: #09090b; display: flex; align-items: center; justify-content: center;">
                                <img :src="activePhoto?.url"
                                     alt="Foto Progres"
                                     class="w-auto h-auto max-w-full rounded-lg object-contain shadow-2xl transition-all"
                                     style="max-height: calc(88vh - 140px); max-width: 100%; object-fit: contain; width: auto; height: auto; display: block;">
                            </div>

                            <div class="flex items-center justify-between border-t border-gray-100 px-5 sm:px-6 py-3 bg-white text-xs text-gray-600 flex-wrap gap-2 shrink-0"
                                 style="flex-shrink: 0; background-color: #ffffff;">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-lg text-[11px] sm:text-xs">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Dokumentasi Lapangan Terverifikasi
                                    </span>
                                    <template x-if="activePhoto?.lat && activePhoto?.lng">
                                        <a :href="'https://www.google.com/maps?q=' + activePhoto.lat + ',' + activePhoto.lng"
                                           target="_blank"
                                           class="inline-flex items-center gap-1 text-amber-800 hover:text-amber-900 font-semibold underline decoration-amber-300 ml-1 text-[11px] sm:text-xs">
                                            <svg class="h-3.5 w-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Titik Lokasi Maps
                                        </a>
                                    </template>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a :href="activePhoto?.url"
                                       target="_blank"
                                       title="Buka Foto Penuh di Tab Baru"
                                       class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-gray-50 px-3 py-1.5 font-semibold text-gray-700 hover:bg-gray-100 transition-colors shadow-2xs">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        <span>Lihat Penuh</span>
                                    </a>

                                    <a :href="activePhoto?.downloadUrl"
                                       class="inline-flex items-center gap-1.5 rounded-xl bg-amber-700 hover:bg-amber-800 text-white px-3.5 py-1.5 font-bold transition-colors shadow-2xs">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span>Unduh Foto</span>
                                    </a>

                                    <button type="button" @click="activePhoto = null" class="rounded-xl border border-gray-300 bg-white px-3.5 py-1.5 font-bold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Sidebar (1/3) --}}
        <div class="space-y-6">
            {{-- Informasi Customer --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm">
                <h3 class="mb-4 text-base font-semibold text-text-primary dark:text-white">Informasi Customer</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-xs text-text-muted">Nama</span>
                        <p class="font-medium text-text-primary dark:text-white">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-text-muted">Telepon</span>
                        <p class="font-medium text-text-primary dark:text-white">{{ $order->customer_phone }}</p>
                    </div>
                    @if ($order->customer_email)
                        <div>
                            <span class="text-xs text-text-muted">Email</span>
                            <p class="font-medium text-text-primary dark:text-white">{{ $order->customer_email }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Alamat --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm">
                <h3 class="mb-4 text-base font-semibold text-text-primary dark:text-white">Alamat Pengiriman</h3>
                <p class="text-sm text-text-primary dark:text-white">{{ $order->shipping_address }}</p>
                <p class="mt-1 text-xs text-text-muted">{{ $order->city }}{{ $order->postal_code ? ', ' . $order->postal_code : '' }}</p>
            </div>

            {{-- Status & Pembayaran --}}
            <div class="rounded-xl border border-border bg-card p-6 shadow-sm">
                <h3 class="mb-4 text-base font-semibold text-text-primary dark:text-white">Status & Pembayaran</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-text-muted">Pesanan</span>
                        @php $color = $order->status_color; @endphp
                        <span class="inline-flex items-center gap-1 rounded-full bg-{{ $color }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $color }}-700 dark:bg-{{ $color }}-950 dark:text-{{ $color }}-300">
                            {{ $order->status_label }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-text-muted">Pembayaran</span>
                        @php $color = $order->payment_status_color; @endphp
                        <span class="inline-flex items-center gap-1 rounded-full bg-{{ $color }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $color }}-700 dark:bg-{{ $color }}-950 dark:text-{{ $color }}-300">
                            {{ $order->payment_status_label }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-text-muted">Total Tagihan</span>
                        <span class="font-bold text-text-primary dark:text-white">{{ $order->formatted_total_price }}</span>
                    </div>
                    @if ($order->down_payment_amount > 0)
                        <div class="flex justify-between text-indigo-900 dark:text-indigo-400 font-medium">
                            <span>DP Diterima</span>
                            <span class="font-bold">{{ $order->formatted_down_payment_amount }}</span>
                        </div>
                        <div class="flex justify-between {{ $order->remaining_payment > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }} font-medium">
                            <span>Sisa Tagihan</span>
                            <span class="font-bold">{{ $order->payment_status === 'paid' ? 'Rp 0 (LUNAS)' : $order->formatted_remaining_payment }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-text-muted">Metode Bayar</span>
                        <span class="font-medium text-text-primary dark:text-white">{{ $order->payment_method_label }}</span>
                    </div>

                    @if ($order->payment_rejection_reason)
                        <div class="rounded-lg bg-red-50 dark:bg-red-950/40 p-2.5 text-xs text-red-800 dark:text-red-300 border border-red-200 dark:border-red-900">
                            <span class="font-bold block">Alasan Penolakan:</span>
                            {{ $order->payment_rejection_reason }}
                        </div>
                    @endif

                    {{-- Bukti Transfer Preview --}}
                    @if ($order->has_payment_proof || $order->has_final_payment_proof)
                        <div class="pt-2 border-t border-border space-y-2">
                            <span class="text-xs font-semibold text-text-muted block">Bukti Transfer:</span>
                            <div class="grid grid-cols-2 gap-2">
                                @if ($order->has_payment_proof)
                                    <div>
                                        <span class="text-[10px] text-text-muted block mb-1">Bukti Awal / DP:</span>
                                        <a href="{{ $order->payment_proof_url }}" target="_blank" class="block aspect-video rounded-lg overflow-hidden border border-border hover:opacity-80 transition-opacity bg-black/5">
                                            <img src="{{ $order->payment_proof_url }}" alt="Bukti Transfer" class="h-full w-full object-cover">
                                        </a>
                                    </div>
                                @endif
                                @if ($order->has_final_payment_proof)
                                    <div>
                                        <span class="text-[10px] text-text-muted block mb-1">Bukti Pelunasan:</span>
                                        <a href="{{ $order->final_payment_proof_url }}" target="_blank" class="block aspect-video rounded-lg overflow-hidden border border-border hover:opacity-80 transition-opacity bg-black/5">
                                            <img src="{{ $order->final_payment_proof_url }}" alt="Bukti Pelunasan" class="h-full w-full object-cover">
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Informasi Pengiriman --}}
            @if ($order->shipping_method)
                <div class="rounded-xl border border-border bg-card p-6 shadow-sm">
                    <h3 class="mb-4 text-base font-semibold text-text-primary dark:text-white">Informasi Pengiriman</h3>
                    <div class="space-y-3 text-sm">
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
                                <span class="font-medium font-mono text-text-primary dark:text-white">{{ $order->tracking_number }}</span>
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

            {{-- Catatan --}}
            @if ($order->notes)
                <div class="rounded-xl border border-border bg-card p-6 shadow-sm">
                    <h3 class="mb-4 text-base font-semibold text-text-primary dark:text-white">Catatan</h3>
                    <p class="text-sm text-text-primary dark:text-white">{{ $order->notes }}</p>
                </div>
            @endif
        </div>
    </div>

</x-layouts::admin>
