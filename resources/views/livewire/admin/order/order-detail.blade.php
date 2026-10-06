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
                            {{-- Produk / Request Custom --}}
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-xs">
                                <div class="mb-3 flex items-center justify-between">
                                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-gray-500">
                                        {{ $order->is_custom ? 'Detail Custom Furniture' : 'Produk' }}
                                    </h3>
                                    @if ($order->is_custom)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-900 border border-amber-300">
                                            ✦ Request Custom
                                        </span>
                                    @endif
                                </div>

                                @if ($order->is_custom)
                                    <div class="space-y-4">
                                        <div class="flex items-start gap-4">
                                            {{-- Foto Referensi --}}
                                            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl border border-gray-200 bg-white">
                                                @if ($order->custom_design_image)
                                                    <a href="{{ asset('storage/' . $order->custom_design_image) }}" target="_blank" title="Klik untuk lihat ukuran penuh">
                                                        <img src="{{ asset('storage/' . $order->custom_design_image) }}" alt="Desain Custom" class="h-full w-full object-cover hover:scale-105 transition-transform duration-200">
                                                    </a>
                                                @else
                                                    <div class="flex h-full w-full flex-col items-center justify-center text-gray-400 bg-amber-50/50 p-1 text-center">
                                                        <svg class="h-6 w-6 stroke-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span class="text-[9px] font-bold text-gray-500">No Image</span>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Info Custom --}}
                                            <div class="flex-1 space-y-1 text-xs">
                                                <p class="text-sm font-bold text-gray-900">{{ $order->custom_furniture_type ?: 'Custom Furniture' }}</p>
                                                @if ($order->custom_dimensions)
                                                    <p class="text-gray-600 font-semibold">Ukuran: <span class="text-gray-900">{{ $order->custom_dimensions }}</span></p>
                                                @endif
                                                <div class="mt-2 text-xs">
                                                    <span class="text-gray-500 font-medium block">Deskripsi / Kebutuhan:</span>
                                                    <p class="text-gray-800 bg-white rounded-lg p-2.5 border border-gray-200 mt-1 whitespace-pre-line leading-relaxed">{{ $order->notes ?: '-' }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Panel Input / Update Estimasi Harga oleh Admin --}}
                                        <div class="rounded-xl border border-amber-300 bg-amber-50/60 p-3.5 space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-extrabold text-amber-950 uppercase tracking-wider">Kesepakatan / Estimasi Harga</span>
                                                <span class="text-xs font-bold text-amber-900">
                                                    Saat ini: {{ $order->formatted_total_price }}
                                                </span>
                                            </div>
                                            <div class="flex flex-col sm:flex-row gap-2">
                                                <div class="relative flex-1">
                                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs font-bold text-gray-500">Rp</span>
                                                    <input
                                                        type="number"
                                                        wire:model="customPriceInput"
                                                        placeholder="Masukkan nominal harga (contoh: 3500000)"
                                                        class="w-full rounded-lg border border-gray-300 bg-white pl-9 pr-3 py-1.5 text-xs font-semibold text-gray-900 focus:border-amber-600 focus:outline-none focus:ring-1 focus:ring-amber-600"
                                                    >
                                                </div>
                                                <button
                                                    type="button"
                                                    wire:click="saveCustomPrice"
                                                    class="inline-flex items-center justify-center rounded-lg bg-amber-700 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-amber-800 transition-colors cursor-pointer shrink-0"
                                                >
                                                    Simpan Harga
                                                </button>
                                            </div>
                                            <p class="text-[11px] text-amber-800">
                                                * Harga yang Anda simpan akan langsung muncul di tagihan dan halaman "Pesanan Saya" milik pelanggan.
                                            </p>
                                        </div>
                                    </div>
                                @elseif ($order->items && $order->items->count() > 0)
                                    <div class="divide-y divide-gray-200">
                                        @foreach ($order->items as $item)
                                            <div class="py-2 flex items-start gap-4 first:pt-0 last:pb-0">
                                                <div class="h-12 w-12 shrink-0 overflow-hidden rounded-lg border border-gray-200 bg-white">
                                                    @if ($item->product && $item->product->thumbnail)
                                                        <img src="{{ asset('storage/' . $item->product->thumbnail) }}" alt="{{ $item->product->name }}" class="h-full w-full object-cover">
                                                    @else
                                                        <div class="flex h-full w-full items-center justify-center text-gray-400">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-bold text-gray-900">{{ $item->product?->name ?? 'Produk' }}</p>
                                                    <div class="mt-1 flex items-center justify-between text-xs text-gray-600">
                                                        <span>{{ $item->quantity }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                                        <span class="font-bold text-gray-900">Rp {{ number_format($item->total_price, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
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
                                @endif
                            </div>

                            {{-- Pilihan Meubel & Packing (Hanya untuk pesanan katalog reguler) --}}
                            @if (!$order->is_custom)
                                <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-4 shadow-xs">
                                    <h3 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-amber-900">Pilihan Meubel & Packing</h3>
                                    <div class="space-y-2.5 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 font-medium">Jenis Meubel</span>
                                            <span class="font-bold text-gray-900">{{ $order->meubel_type_label }}</span>
                                        </div>

                                        @if (($order->meubel_type === 'matang' || $order->meubel_type === 'finished') && !empty($order->customization_details))
                                            @foreach ($order->customization_details as $pId => $selection)
                                                @php
                                                    $pModel = \App\Models\Product::find($pId);
                                                @endphp
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 font-medium">Bahan Dudukan {{ $pModel ? "({$pModel->name})" : '' }}</span>
                                                    <span class="font-bold text-gray-900">{{ $selection }}</span>
                                                </div>
                                            @endforeach
                                        @endif

                                        <div class="flex justify-between">
                                            <span class="text-gray-600 font-medium">Bahan Packing</span>
                                            <span class="font-bold text-gray-900">{{ $order->packing_type_label }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 font-medium">Biaya Customisasi</span>
                                            <span class="font-bold text-gray-900">Rp {{ number_format($order->customization_fee ?? 0, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600 font-medium">Biaya Packing</span>
                                            <span class="font-bold text-gray-900">Rp {{ number_format($order->packing_fee ?? 0, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Status & Pembayaran --}}
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-xs">
                                <h3 class="mb-3 text-xs font-extrabold uppercase tracking-wider text-gray-500">Status & Pembayaran</h3>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 font-medium">Status Pesanan</span>
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-950 border border-amber-300">
                                            <x-order-status-icon :status="$order->status" class="h-3.5 w-3.5 text-amber-900 shrink-0" />
                                            <span>{{ $order->status_label }}</span>
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600 font-medium">Status Pembayaran</span>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-{{ $order->payment_status_color }}-100 px-3 py-1 text-xs font-bold text-{{ $order->payment_status_color }}-900 border border-{{ $order->payment_status_color }}-300">
                                            {{ $order->payment_status_label }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-medium">Total Tagihan</span>
                                        <span class="font-bold text-gray-900">{{ $order->formatted_total_price }}</span>
                                    </div>
                                    @if ($order->down_payment_amount > 0)
                                        <div class="flex justify-between text-indigo-900 font-medium">
                                            <span>DP Diterima</span>
                                            <span class="font-bold">{{ $order->formatted_down_payment_amount }}</span>
                                        </div>
                                        <div class="flex justify-between {{ $order->remaining_payment > 0 ? 'text-rose-700' : 'text-emerald-700' }} font-medium">
                                            <span>Sisa Tagihan</span>
                                            <span class="font-bold">{{ $order->payment_status === 'paid' ? 'Rp 0 (LUNAS)' : $order->formatted_remaining_payment }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-medium">Metode Pembayaran</span>
                                        <span class="font-bold text-gray-900">{{ $order->payment_method_label }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 font-medium">Metode Pengiriman</span>
                                        <span class="font-bold text-gray-900">{{ $order->shipping_method_label }}</span>
                                    </div>

                                    @if ($order->payment_rejection_reason)
                                        <div class="rounded-lg bg-red-50 p-2.5 text-xs text-red-800 border border-red-200">
                                            <span class="font-bold block">Alasan Penolakan:</span>
                                            {{ $order->payment_rejection_reason }}
                                        </div>
                                    @endif

                                    {{-- Riwayat Bukti & Termin Pembayaran --}}
                                    @if ($order->payments->isNotEmpty())
                                        <div class="pt-2 border-t border-gray-200 space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-bold text-gray-700">Riwayat Pembayaran ({{ $order->payments->count() }}x):</span>
                                                <button
                                                    type="button"
                                                    wire:click="$dispatch('openPayment', { orderId: {{ $order->id }} })"
                                                    class="text-[11px] font-bold text-amber-800 hover:underline cursor-pointer"
                                                >
                                                    Kelola / Verifikasi
                                                </button>
                                            </div>
                                            <div class="space-y-1.5">
                                                @foreach ($order->payments as $pmt)
                                                    <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 border border-gray-200 text-xs">
                                                        <div class="flex items-center gap-1.5 min-w-0">
                                                            <span class="font-bold text-gray-900 text-[11px]">#{{ $pmt->payment_number }}</span>
                                                            <span class="text-[11px] text-gray-700 truncate max-w-[130px]" title="{{ $pmt->title }}">{{ $pmt->title }}</span>
                                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold shrink-0 {{ $pmt->status === 'verified' ? 'bg-emerald-100 text-emerald-800' : ($pmt->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                                                {{ $pmt->status_label }}
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center gap-2 shrink-0">
                                                            <span class="font-mono font-bold text-gray-900 text-xs">
                                                                {{ $pmt->status === 'verified' ? $pmt->formatted_amount : ($pmt->status === 'pending' ? 'Pending' : '-') }}
                                                            </span>
                                                            @if ($pmt->proof_file)
                                                                <a href="{{ $pmt->proof_url }}" target="_blank" class="text-amber-800 hover:text-amber-900" title="Buka bukti foto">
                                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                                    </svg>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @elseif ($order->has_payment_proof || $order->has_final_payment_proof)
                                        <div class="pt-2 border-t border-gray-200 space-y-2">
                                            <span class="text-xs font-bold text-gray-700 block">Bukti Transfer:</span>
                                            <div class="grid grid-cols-2 gap-2">
                                                @if ($order->has_payment_proof)
                                                    <div>
                                                        <span class="text-[10px] text-gray-500 block mb-1">Bukti Awal / DP:</span>
                                                        <a href="{{ $order->payment_proof_url }}" target="_blank" class="block aspect-video rounded-lg overflow-hidden border border-gray-300 hover:opacity-80 transition-opacity bg-black/5">
                                                            <img src="{{ $order->payment_proof_url }}" alt="Bukti Transfer" class="h-full w-full object-cover">
                                                        </a>
                                                    </div>
                                                @endif
                                                @if ($order->has_final_payment_proof)
                                                    <div>
                                                        <span class="text-[10px] text-gray-500 block mb-1">Bukti Pelunasan:</span>
                                                        <a href="{{ $order->final_payment_proof_url }}" target="_blank" class="block aspect-video rounded-lg overflow-hidden border border-emerald-300 hover:opacity-80 transition-opacity bg-black/5">
                                                            <img src="{{ $order->final_payment_proof_url }}" alt="Bukti Pelunasan" class="h-full w-full object-cover">
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
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
                                        @if ($history->photo)
                                            <div class="mt-2 flex items-center flex-wrap gap-2">
                                                <a href="{{ $history->photo_url }}" target="_blank"
                                                   class="inline-flex items-center gap-1.5 rounded-lg border border-amber-300 bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-900 hover:bg-amber-100 transition-colors shadow-2xs">
                                                    <svg class="h-3.5 w-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <span>Lihat Foto Progres</span>
                                                </a>
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
                                                       class="text-[11px] font-semibold text-gray-500 hover:text-amber-800 flex items-center gap-1">
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
