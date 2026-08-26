<div>
    {{-- Search Form Box --}}
    <div class="mx-auto max-w-3xl">
        <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-xl shadow-wood-primary/5 sm:p-8">
            <form wire:submit.prevent="trackOrder" class="space-y-4">
                <label for="tracking-code-input" class="block text-sm font-bold text-wood-text">
                    Lacak Status Pesanan Anda
                </label>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-wood-muted">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            id="tracking-code-input"
                            type="text"
                            wire:model.defer="searchCode"
                            placeholder="Masukkan Kode Pesanan (Contoh: BWL-20260826-0001)"
                            class="w-full rounded-2xl border border-wood-border/70 bg-wood-light/20 py-3.5 pl-11 pr-4 font-mono text-sm font-medium text-wood-text uppercase placeholder:normal-case placeholder:font-sans placeholder:text-wood-muted/70 focus:border-wood-primary focus:bg-white focus:outline-none focus:ring-4 focus:ring-wood-primary/10 transition-all"
                            required
                        >
                    </div>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-2xl bg-wood-primary px-7 py-3.5 text-sm font-bold text-white shadow-lg shadow-wood-primary/20 transition-all hover:bg-wood-primary-dark hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-75"
                    >
                        <span wire:loading.remove wire:target="trackOrder">Lacak Pesanan</span>
                        <span wire:loading wire:target="trackOrder" class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mencari...
                        </span>
                        <svg wire:loading.remove wire:target="trackOrder" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-wood-muted">
                    <span class="font-semibold text-wood-text">Tips:</span> Kode pesanan tertera pada invoice pembelian dan pesan konfirmasi WhatsApp Anda.
                </p>
            </form>
        </div>
    </div>

    {{-- Order Not Found Alert --}}
    @if ($searched && !$order)
        <div class="mx-auto mt-10 max-w-3xl">
            <div class="rounded-3xl border border-rose-200 bg-rose-50/70 p-8 text-center shadow-sm backdrop-blur-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-100 text-rose-600 mb-4">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-rose-900 sm:text-lg">Pesanan Tidak Ditemukan</h3>
                <p class="mt-2 text-sm text-rose-700 max-w-md mx-auto leading-relaxed">
                    Kode pesanan <span class="font-mono font-bold text-rose-900">"{{ $searchCode }}"</span> tidak ditemukan dalam sistem kami. Pastikan format dan kode pesanan Anda sudah benar.
                </p>
                @php
                    $rawWa = App\Helpers\WebsiteSettings::whatsapp();
                    $cleanWa = $rawWa ? preg_replace('/[^0-9]/', '', (string) $rawWa) : '';
                    if (str_starts_with($cleanWa, '0')) {
                        $cleanWa = '62' . substr($cleanWa, 1);
                    }
                @endphp
                @if ($cleanWa)
                    <div class="mt-6">
                        <a
                            href="https://wa.me/{{ $cleanWa }}?text=Halo%20Bewole%20Furniture%2C%20saya%20butuh%20bantuan%20mencari%20pesanan%20dengan%20kode%20{{ urlencode($searchCode) }}"
                            target="_blank"
                            class="inline-flex items-center gap-2 rounded-full border border-rose-300 bg-white px-5 py-2.5 text-xs font-semibold text-rose-800 shadow-sm transition-all hover:bg-rose-100/50"
                        >
                            <svg class="h-4 w-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Hubungi Bantuan via WhatsApp
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Order Found Content --}}
    @if ($order)
        @php
            $status = $order->status;
            $step = match($status) {
                'pending', 'confirmed' => 1,
                'awaiting_payment', 'payment_received' => 2,
                'in_production', 'quality_control' => 3,
                'ready_to_ship', 'shipped' => 4,
                'completed' => 5,
                default => 1,
            };
            $isCancelled = $status === 'cancelled';
        @endphp

        <div class="mx-auto mt-10 max-w-4xl space-y-8">

            {{-- 1. Status Overview Header Card --}}
            <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-xl shadow-wood-primary/5 sm:p-8">
                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between border-b border-wood-border/40 pb-6">
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="text-xs font-semibold uppercase tracking-wider text-wood-muted">Kode Pesanan</span>
                            <span class="rounded-full bg-wood-light/40 px-3 py-1 font-mono text-sm font-bold text-wood-text border border-wood-border/50">
                                #{{ $order->order_code }}
                            </span>
                        </div>
                        <h2 class="mt-2 text-xl font-bold text-wood-text sm:text-2xl">
                            {{ $order->product?->name ?? 'Detail Pesanan' }}
                        </h2>
                        <p class="mt-1 text-xs text-wood-muted">
                            Dipesan pada <span class="font-medium text-wood-text">{{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                        </p>
                    </div>

                    <div class="flex flex-col items-start sm:items-end">
                        <span class="text-xs text-wood-muted mb-1">Status Terkini</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 text-xs font-bold shadow-sm
                            @if($order->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                            @elseif($order->status === 'confirmed') bg-blue-50 text-blue-700 border border-blue-200
                            @elseif($order->status === 'awaiting_payment') bg-yellow-50 text-yellow-800 border border-yellow-300
                            @elseif($order->status === 'payment_received') bg-purple-50 text-purple-700 border border-purple-200
                            @elseif($order->status === 'in_production') bg-stone-100 text-stone-700 border border-stone-300
                            @elseif($order->status === 'quality_control') bg-orange-50 text-orange-700 border border-orange-200
                            @elseif($order->status === 'ready_to_ship') bg-cyan-50 text-cyan-700 border border-cyan-200
                            @elseif($order->status === 'shipped') bg-indigo-50 text-indigo-700 border border-indigo-200
                            @elseif($order->status === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                            @elseif($order->status === 'cancelled') bg-rose-50 text-rose-700 border border-rose-200
                            @else bg-gray-50 text-gray-700 border border-gray-200
                            @endif
                        ">
                            <span>{{ $order->status_emoji }}</span>
                            <span>{{ $order->status_label }}</span>
                        </span>
                    </div>
                </div>

                {{-- 2. Progress Bar / Stepper --}}
                @if ($isCancelled)
                    <div class="mt-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-center">
                        <p class="text-sm font-bold text-rose-800">Pesanan ini telah dibatalkan</p>
                        <p class="text-xs text-rose-600 mt-1">Silakan hubungi customer service kami jika Anda memerlukan informasi lebih lanjut.</p>
                    </div>
                @else
                    <div class="mt-8">
                        <div class="relative">
                            {{-- Step indicator dots & connectors --}}
                            @php
                                $isCompleted = ($status === 'completed');
                                $steps = [
                                    ['num' => 1, 'title' => 'Pesanan Dikonfirmasi', 'desc' => 'Menunggu / Konfirmasi'],
                                    ['num' => 2, 'title' => 'Pembayaran', 'desc' => 'Verifikasi Bayar'],
                                    ['num' => 3, 'title' => 'Produksi & QC', 'desc' => 'Pengerjaan Kayu'],
                                    ['num' => 4, 'title' => 'Pengiriman', 'desc' => 'Siap / Dikirim'],
                                    ['num' => 5, 'title' => 'Selesai', 'desc' => 'Barang Diterima'],
                                ];
                            @endphp

                            <div class="grid grid-cols-5 gap-2 sm:gap-4 relative z-10">
                                @foreach ($steps as $s)
                                    @php
                                        $isDone = $isCompleted || ($step > $s['num']);
                                        $isCurrent = !$isCompleted && ($step === $s['num']);
                                        $isFuture = !$isCompleted && ($step < $s['num']);
                                    @endphp
                                    <div class="flex flex-col items-center text-center">
                                        <div class="flex h-9 w-9 sm:h-11 sm:w-11 items-center justify-center rounded-full border-2 transition-all
                                            @if($isDone) bg-emerald-500 border-emerald-500 text-white shadow-md shadow-emerald-500/20
                                            @elseif($isCurrent) bg-wood-primary border-wood-primary text-white ring-4 ring-wood-primary/20 shadow-md shadow-wood-primary/30
                                            @else bg-white border-wood-border/60 text-wood-muted/60
                                            @endif
                                        ">
                                            @if ($isDone)
                                                <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @else
                                                <span class="text-xs sm:text-sm font-bold">{{ $s['num'] }}</span>
                                            @endif
                                        </div>
                                        <span class="mt-2.5 text-[11px] sm:text-xs font-bold leading-tight line-clamp-2
                                            @if($isCompleted) text-emerald-700
                                            @elseif($isCurrent) text-wood-primary
                                            @elseif($isDone) text-wood-text
                                            @else text-wood-muted
                                            @endif
                                        ">
                                            {{ $s['title'] }}
                                        </span>
                                        <span class="hidden sm:block mt-0.5 text-[10px] {{ $isCompleted ? 'text-emerald-600/80 font-medium' : 'text-wood-muted' }}">
                                            {{ $s['desc'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Connector Line --}}
                            <div class="absolute left-[10%] right-[10%] top-4 sm:top-5 -translate-y-1/2 h-1 bg-wood-border/40 z-0">
                                <div class="h-full bg-emerald-500 transition-all duration-500" style="width: {{ $isCompleted ? 100 : ((($step - 1) / 4) * 100) }}%;"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- 3. Detail Grid: Product & Timeline --}}
            <div class="grid gap-8 lg:grid-cols-3">
                
                {{-- Left: Status Timeline (2 Cols) --}}
                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm sm:p-7">
                        <h3 class="text-base font-bold text-wood-text border-b border-wood-border/40 pb-4 mb-6 flex items-center justify-between">
                            <span>Riwayat Status Pesanan</span>
                            <span class="text-xs font-normal text-wood-muted">Total: {{ $order->statusHistories->count() }} Update</span>
                        </h3>

                        <div class="relative pl-2">
                            @forelse ($order->statusHistories as $history)
                                <div class="relative flex gap-4 pb-8 last:pb-2">
                                    @if (!$loop->last)
                                        <div class="absolute left-[11px] top-6 h-full w-0.5 bg-wood-border/50"></div>
                                    @endif
                                    <div class="relative flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 {{ $loop->first ? 'border-wood-primary bg-wood-primary text-white ring-4 ring-wood-primary/10' : 'border-wood-border/80 bg-white' }}">
                                        @if ($loop->first)
                                            <div class="h-2 w-2 rounded-full bg-white"></div>
                                        @else
                                            <div class="h-2 w-2 rounded-full bg-wood-muted/50"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1 pt-0.5">
                                        <div class="flex items-center justify-between gap-2 flex-wrap">
                                            <p class="text-xs sm:text-sm font-bold text-wood-text">{{ $history->status_label }}</p>
                                            <span class="text-[10px] sm:text-xs text-wood-muted font-medium">
                                                {{ $history->created_at->translatedFormat('d M Y, H:i') }} WIB
                                            </span>
                                        </div>
                                        @if ($history->description || $history->notes)
                                            <p class="mt-1 text-xs text-wood-muted/90 leading-relaxed bg-wood-light/20 rounded-xl p-3 border border-wood-border/30">
                                                {{ $history->description ?: $history->notes }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-xs text-wood-muted">
                                    Belum ada catatan riwayat status untuk pesanan ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Right: Product & Shipping Summary (1 Col) --}}
                <div class="space-y-6">
                    {{-- Product Card --}}
                    <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-wood-text border-b border-wood-border/40 pb-3 mb-4">Informasi Produk</h3>
                        <div class="flex gap-4">
                            <div class="h-16 w-16 shrink-0 overflow-hidden rounded-2xl bg-wood-light/40 border border-wood-border/40">
                                @if ($order->product && $order->product->thumbnail)
                                    <img src="{{ asset('storage/' . $order->product->thumbnail) }}" alt="{{ $order->product->name }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-wood-muted">
                                        <svg class="h-7 w-7 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs sm:text-sm font-semibold text-wood-text truncate">{{ $order->product?->name ?? 'Produk Bewole' }}</h4>
                                <p class="text-xs text-wood-muted mt-0.5">{{ $order->quantity }} pcs × {{ $order->product?->formatted_price ?? 'Rp 0' }}</p>
                                <p class="text-xs font-bold text-wood-primary mt-1">{{ $order->formatted_total_price }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Shipping Details Card --}}
                    <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm space-y-3 text-xs">
                        <h3 class="text-sm font-bold text-wood-text border-b border-wood-border/40 pb-3 mb-2">Informasi Pengiriman</h3>
                        
                        <div>
                            <span class="text-wood-muted block mb-0.5">Penerima:</span>
                            <span class="font-semibold text-wood-text">{{ $order->customer_name }}</span>
                        </div>

                        <div>
                            <span class="text-wood-muted block mb-0.5">Alamat Tujuan:</span>
                            <p class="font-medium text-wood-text leading-relaxed">{{ $order->shipping_address }}</p>
                            <p class="text-wood-muted mt-0.5">{{ $order->city }}{{ $order->postal_code ? ', ' . $order->postal_code : '' }}</p>
                        </div>

                        @if ($order->shipping_method)
                            <div class="border-t border-wood-border/30 pt-2.5">
                                <span class="text-wood-muted block mb-0.5">Metode Pengiriman:</span>
                                <span class="font-semibold text-wood-text">{{ $order->shipping_method_label }}</span>
                            </div>
                        @endif

                        @if ($order->courier)
                            <div>
                                <span class="text-wood-muted block mb-0.5">Kurir:</span>
                                <span class="font-semibold text-wood-text">{{ $order->courier }}</span>
                            </div>
                        @endif

                        @if ($order->tracking_number)
                            <div>
                                <span class="text-wood-muted block mb-0.5">No. Resi Pengiriman:</span>
                                <span class="font-mono font-bold text-wood-primary bg-wood-light/40 px-2 py-1 rounded-lg inline-block border border-wood-border/50">
                                    {{ $order->tracking_number }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- WhatsApp Help Card --}}
                    @php
                        $orderWa = App\Helpers\WebsiteSettings::whatsapp();
                        $cleanOrderWa = $orderWa ? preg_replace('/[^0-9]/', '', (string) $orderWa) : '';
                        if (str_starts_with($cleanOrderWa, '0')) {
                            $cleanOrderWa = '62' . substr($cleanOrderWa, 1);
                        }
                    @endphp
                    @if ($cleanOrderWa)
                        <div class="rounded-3xl border border-emerald-200 bg-emerald-50/50 p-5 text-center space-y-3">
                            <p class="text-xs font-bold text-emerald-900">Ada Pertanyaan Mengenai Pesanan?</p>
                            <p class="text-[11px] text-emerald-700 leading-relaxed">
                                Hubungi tim customer care Bewole Furniture untuk konfirmasi atau informasi pengiriman.
                            </p>
                            <a
                                href="https://wa.me/{{ $cleanOrderWa }}?text=Halo%20Bewole%20Furniture%2C%20saya%20ingin%20bertanya%20tentang%20pesanan%20nomor%20%23{{ $order->order_code }}"
                                target="_blank"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition-all hover:bg-emerald-700"
                            >
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                Tanya CS via WhatsApp
                            </a>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    @endif
</div>
