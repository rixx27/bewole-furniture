<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <form wire:submit.prevent="placeOrder" class="grid gap-8 lg:grid-cols-12">
        {{-- Form Pembelian (7 cols) --}}
        <div class="lg:col-span-7 space-y-6">
            {{-- 1. Data Pemesan --}}
            <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-wood-text border-b border-wood-border/40 pb-3">1. Data Pemesan</h2>

                <div class="mt-5 space-y-4">
                    {{-- Nama Pembeli --}}
                    <div>
                        <label class="block text-xs font-semibold text-wood-text">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input
                            type="text"
                            wire:model="customer_name"
                            placeholder="Contoh: Muhammad Krisna"
                            class="mt-1 w-full rounded-2xl border border-wood-border/60 bg-white/80 py-2.5 px-4 text-xs font-medium text-wood-text focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                        />
                        @error('customer_name') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- WhatsApp --}}
                    <div>
                        <label class="block text-xs font-semibold text-wood-text">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                        <input
                            type="text"
                            wire:model="customer_phone"
                            placeholder="Contoh: 081234567890"
                            class="mt-1 w-full rounded-2xl border border-wood-border/60 bg-white/80 py-2.5 px-4 text-xs font-medium text-wood-text focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                        />
                        @error('customer_phone') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-semibold text-wood-text">Alamat Email (Opsional)</label>
                        <input
                            type="email"
                            wire:model="customer_email"
                            placeholder="nama@email.com"
                            class="mt-1 w-full rounded-2xl border border-wood-border/60 bg-white/80 py-2.5 px-4 text-xs font-medium text-wood-text focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                        />
                        @error('customer_email') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- 2. Data Pengiriman --}}
            <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-wood-text border-b border-wood-border/40 pb-3">2. Data Pengiriman</h2>

                <div class="mt-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Provinsi --}}
                        <div>
                            <label class="block text-xs font-semibold text-wood-text">Provinsi <span class="text-rose-500">*</span></label>
                            <select
                                wire:model.live="province"
                                class="mt-1 w-full rounded-2xl border border-wood-border/60 bg-white/80 py-2.5 px-4 text-xs font-medium text-wood-text focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                            >
                                <option value="">Pilih Provinsi</option>
                                @foreach($provinces as $prov)
                                    <option value="{{ $prov }}">{{ $prov }}</option>
                                @endforeach
                            </select>
                            @error('province') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        {{-- Kota/Kabupaten --}}
                        <div>
                            <label class="block text-xs font-semibold text-wood-text">Kota / Kabupaten <span class="text-rose-500">*</span></label>
                            <select
                                wire:model="city"
                                @if(empty($province)) disabled @endif
                                class="mt-1 w-full rounded-2xl border border-wood-border/60 bg-white/80 py-2.5 px-4 text-xs font-medium text-wood-text focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20 disabled:opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
                            >
                                <option value="">Pilih Kota / Kabupaten</option>
                                @foreach($cities as $cty)
                                    <option value="{{ $cty }}">{{ $cty }}</option>
                                @endforeach
                            </select>
                            @error('city') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label class="block text-xs font-semibold text-wood-text">Alamat Lengkap <span class="text-rose-500">*</span></label>
                        <textarea
                            wire:model="shipping_address"
                            rows="3"
                            placeholder="Jalan, No. Rumah, RT/RW, Kecamatan, Kelurahan..."
                            class="mt-1 w-full rounded-2xl border border-wood-border/60 bg-white/80 p-3 text-xs font-medium text-wood-text focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                        ></textarea>
                        @error('shipping_address') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="block text-xs font-semibold text-wood-text">Catatan Pesanan (Opsional)</label>
                        <textarea
                            wire:model="notes"
                            rows="2"
                            placeholder="Catatan khusus mengenai warna kayu, finishing, atau instruksi pengiriman..."
                            class="mt-1 w-full rounded-2xl border border-wood-border/60 bg-white/80 p-3 text-xs font-medium text-wood-text focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                        ></textarea>
                        @error('notes') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- 3. Pilihan Meubel & Packing --}}
            <div class="rounded-3xl border border-wood-border/60 bg-white p-6 shadow-sm space-y-6">
                <h2 class="text-lg font-bold text-wood-text border-b border-wood-border/40 pb-3">3. Pilihan Meubel & Packing</h2>

                {{-- Jenis Meubel --}}
                <div>
                    <h3 class="text-sm font-bold text-wood-text mb-1">Jenis Meubel</h3>
                    <label class="block text-xs font-semibold text-wood-muted mb-3">Jenis Meubel <span class="text-rose-500">*</span></label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Meubel Mentah --}}
                        <label class="relative flex flex-col cursor-pointer rounded-2xl border p-4 transition-all focus-within:ring-2 focus-within:ring-wood-primary/20 {{ $meubel_type === 'mentah' ? 'border-wood-primary bg-wood-primary/5 shadow-sm' : 'border-wood-border/60 bg-white hover:border-wood-primary/40' }}">
                            <div class="flex items-center gap-3">
                                <input
                                    type="radio"
                                    name="meubel_type"
                                    value="mentah"
                                    wire:model.live="meubel_type"
                                    class="h-4 w-4 text-wood-primary focus:ring-wood-primary border-wood-border"
                                >
                                <span class="text-xs font-bold text-wood-text">Meubel Mentah</span>
                            </div>
                            <span class="mt-2 text-[11px] text-wood-muted pl-7">Kondisi kayu mentah</span>
                        </label>

                        {{-- Meubel Matang --}}
                        <label class="relative flex flex-col cursor-pointer rounded-2xl border p-4 transition-all focus-within:ring-2 focus-within:ring-wood-primary/20 {{ $meubel_type === 'matang' ? 'border-wood-primary bg-wood-primary/5 shadow-sm' : 'border-wood-border/60 bg-white hover:border-wood-primary/40' }}">
                            <div class="flex items-center gap-3">
                                <input
                                    type="radio"
                                    name="meubel_type"
                                    value="matang"
                                    wire:model.live="meubel_type"
                                    class="h-4 w-4 text-wood-primary focus:ring-wood-primary border-wood-border"
                                >
                                <span class="text-xs font-bold text-wood-text">Meubel Matang</span>
                            </div>
                            <span class="mt-2 text-[11px] text-wood-muted pl-7">Siap finishing/custom</span>
                        </label>
                    </div>
                    @error('meubel_type') <span class="mt-1.5 block text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                {{-- Detail Meubel Matang (Conditional Customization Form per Product) --}}
                @if ($meubel_type === 'matang')
                    <div class="border-t border-wood-border/30 pt-5 space-y-5 transition-all duration-300">
                        @foreach ($cart as $productId => $item)
                            @php
                                $productModel = \App\Models\Product::active()->find($productId);
                                $customization = $productModel?->getCustomizationOptions();
                            @endphp

                            @if ($customization)
                                <div class="rounded-2xl border border-wood-border/40 bg-wood-light/10 p-4 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-xs font-bold text-wood-text uppercase tracking-wider">
                                            Pilihan {{ $customization['label'] }} ({{ $item['name'] }})
                                        </h3>
                                        <span class="text-[10px] font-medium text-wood-muted">Qty: {{ $item['quantity'] }}</span>
                                    </div>

                                    <label class="block text-xs font-semibold text-wood-muted">
                                        {{ $customization['label'] }} <span class="text-rose-500">*</span>
                                    </label>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        @foreach ($customization['options'] as $opt)
                                            @php
                                                $isSelected = isset($customization_selections[$productId]) && $customization_selections[$productId] === $opt['value'];
                                            @endphp
                                            <label class="relative flex items-center cursor-pointer rounded-xl border p-3 transition-all focus-within:ring-2 focus-within:ring-wood-primary/20 {{ $isSelected ? 'border-wood-primary bg-wood-primary/10 font-semibold' : 'border-wood-border/60 bg-white hover:border-wood-primary/40' }}">
                                                <input
                                                    type="radio"
                                                    name="customization_{{ $productId }}"
                                                    value="{{ $opt['value'] }}"
                                                    wire:model.live="customization_selections.{{ $productId }}"
                                                    class="h-3.5 w-3.5 text-wood-primary focus:ring-wood-primary border-wood-border"
                                                >
                                                <span class="ml-2.5 text-xs text-wood-text">{{ $opt['label'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error("customization_selections.{$productId}")
                                        <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- Bahan Packing --}}
                <div class="border-t border-wood-border/30 pt-5">
                    <h3 class="text-sm font-bold text-wood-text mb-1">Bahan Packing</h3>
                    <label class="block text-xs font-semibold text-wood-muted mb-3">Bahan Packing <span class="text-rose-500">*</span></label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Kardus --}}
                        <label class="relative flex items-center cursor-pointer rounded-2xl border p-4 transition-all focus-within:ring-2 focus-within:ring-wood-primary/20 {{ $packing_type === 'kardus' ? 'border-wood-primary bg-wood-primary/5 shadow-sm' : 'border-wood-border/60 bg-white hover:border-wood-primary/40' }}">
                            <input
                                type="radio"
                                name="packing_type"
                                value="kardus"
                                wire:model.live="packing_type"
                                class="h-4 w-4 text-wood-primary focus:ring-wood-primary border-wood-border"
                            >
                            <span class="ml-3 text-xs font-bold text-wood-text">Kardus</span>
                        </label>

                        {{-- Plastik --}}
                        <label class="relative flex items-center cursor-pointer rounded-2xl border p-4 transition-all focus-within:ring-2 focus-within:ring-wood-primary/20 {{ $packing_type === 'plastik' ? 'border-wood-primary bg-wood-primary/5 shadow-sm' : 'border-wood-border/60 bg-white hover:border-wood-primary/40' }}">
                            <input
                                type="radio"
                                name="packing_type"
                                value="plastik"
                                wire:model.live="packing_type"
                                class="h-4 w-4 text-wood-primary focus:ring-wood-primary border-wood-border"
                            >
                            <span class="ml-3 text-xs font-bold text-wood-text">Plastik</span>
                        </label>
                    </div>
                    @error('packing_type') <span class="mt-1.5 block text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Order Summary (5 cols) --}}
        <div class="lg:col-span-5">
            <div class="sticky top-24 rounded-3xl border border-wood-border/60 bg-white p-6 shadow-md shadow-wood-primary/5">
                <h2 class="text-lg font-bold text-wood-text border-b border-wood-border/40 pb-4">Ringkasan Pesanan</h2>

                {{-- Cart Items --}}
                <div class="mt-4 max-h-64 overflow-y-auto divide-y divide-wood-border/30 pr-1 scrollbar-thin">
                    @foreach ($cart as $item)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 shrink-0 overflow-hidden rounded-xl bg-wood-light/40 border border-wood-border/40">
                                    @if (!empty($item['thumbnail']))
                                        <img src="{{ asset('storage/' . $item['thumbnail']) }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-wood-muted">
                                            <svg class="h-5 w-5 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-semibold text-wood-text line-clamp-1">{{ $item['name'] }}</h4>
                                    <span class="text-wood-muted">Qty: {{ $item['quantity'] }} × {{ $item['formatted_price'] }}</span>
                                </div>
                            </div>
                            <span class="font-bold text-wood-text">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                {{-- Detail Customisasi Summary --}}
                <div class="mt-4 rounded-2xl bg-wood-light/20 p-3.5 border border-wood-border/40 space-y-2 text-xs">
                    <h4 class="font-bold text-wood-text text-[11px] uppercase tracking-wider border-b border-wood-border/30 pb-1 mb-2">Detail Customisasi & Packing</h4>
                    
                    <div class="flex justify-between text-wood-muted">
                        <span>Meubel:</span>
                        <span class="font-bold text-wood-text">
                            @if($meubel_type === 'mentah')
                                Meubel Mentah
                            @elseif($meubel_type === 'matang')
                                Meubel Matang
                            @else
                                <span class="font-normal italic text-wood-muted/70">Belum dipilih</span>
                            @endif
                        </span>
                    </div>

                    @if ($meubel_type === 'matang')
                        @foreach ($cart as $productId => $item)
                            @php
                                $breakdown = $itemBreakdowns[$productId] ?? null;
                            @endphp

                            @if ($breakdown && !empty($breakdown['seat_material_name']))
                                <div class="space-y-0.5 border-l-2 border-amber-600/40 pl-2.5 my-1">
                                    <div class="flex justify-between font-semibold text-wood-text">
                                        <span>Bahan Dudukan ({{ $item['name'] }}):</span>
                                        <span>{{ $breakdown['seat_material_name'] }}</span>
                                    </div>
                                    <div class="flex justify-between text-[11px] text-wood-muted">
                                        <span>Penggunaan: {{ $breakdown['seat_usage_meter'] }}m @ Rp {{ number_format($breakdown['seat_price_per_meter'], 0, ',', '.') }}/m</span>
                                        <span class="font-bold text-wood-text">+ Rp {{ number_format($breakdown['seat_material_cost'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif

                    <div class="flex justify-between text-wood-muted pt-1 border-t border-wood-border/20">
                        <span>Packing:</span>
                        <span class="font-bold text-wood-text">
                            @if($packing_type === 'kardus')
                                Kardus
                            @elseif($packing_type === 'plastik')
                                Plastik
                            @else
                                <span class="font-normal italic text-wood-muted/70">Belum dipilih</span>
                            @endif
                        </span>
                    </div>

                    @if (!empty($packing_type))
                        @foreach ($cart as $productId => $item)
                            @php
                                $breakdown = $itemBreakdowns[$productId] ?? null;
                            @endphp

                            @if ($breakdown && !empty($breakdown['packing_material_name']))
                                <div class="space-y-0.5 border-l-2 border-emerald-600/40 pl-2.5 my-1">
                                    <div class="flex justify-between text-[11px] text-wood-muted">
                                        <span>{{ $breakdown['product_name'] ?? $item['name'] }} ({{ $breakdown['packing_usage_meter'] }}m @ Rp {{ number_format($breakdown['packing_price_per_meter'], 0, ',', '.') }}/m):</span>
                                        <span class="font-bold text-wood-text">+ Rp {{ number_format($breakdown['packing_material_cost'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>

                {{-- Calculation --}}
                <div class="mt-6 border-t border-wood-border/40 pt-4 space-y-2 text-xs">
                    <div class="flex justify-between text-wood-muted">
                        <span>Subtotal Produk</span>
                        <span class="font-bold text-wood-text">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-wood-muted">
                        <span>Biaya Customisasi</span>
                        <span class="font-bold text-wood-text">
                            {{ $customization_fee > 0 ? 'Rp ' . number_format($customization_fee, 0, ',', '.') : 'Rp 0' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-wood-muted">
                        <span>Biaya Packing</span>
                        <span class="font-bold text-wood-text">
                            {{ $packing_fee > 0 ? 'Rp ' . number_format($packing_fee, 0, ',', '.') : 'Rp 0' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-wood-muted">
                        <span>Biaya Pengiriman</span>
                        <span class="font-semibold text-emerald-600">Dikonfirmasi via WhatsApp</span>
                    </div>
                </div>

                <div class="mt-6 border-t border-wood-border/40 pt-4">
                    <div class="flex justify-between text-base font-bold text-wood-text">
                        <span>Total Pembayaran</span>
                        <span class="text-lg text-wood-primary">
                            Rp {{ number_format($subtotal + $customization_fee + $packing_fee, 0, ',', '.') }}
                        </span>
                    </div>
                    <p class="mt-1.5 text-[11px] leading-tight text-wood-muted">
                        * Total pembayaran di atas <span class="font-semibold text-rose-500">belum termasuk ongkos kirim</span>. Biaya tersebut akan dikonfirmasi lebih lanjut via WhatsApp.
                    </p>
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="mt-6 w-full rounded-2xl bg-emerald-700 py-4 text-center text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-emerald-700/25 transition-all hover:bg-emerald-800 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2"
                >
                    <svg wire:loading.remove class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                    </svg>
                    <span wire:loading.remove>Konfirmasi Pesanan via WhatsApp</span>
                    <span wire:loading>Memproses Pesanan...</span>
                </button>
            </div>
        </div>
    </form>
</div>
