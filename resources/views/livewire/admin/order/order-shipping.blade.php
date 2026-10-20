<div>
    @if ($show && $order)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-init="$el.style.display = 'flex'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="$dispatch('closeModal')"></div>

            {{-- Modal Box --}}
            <div class="relative flex flex-col w-full max-w-lg max-h-[90vh] rounded-2xl bg-white shadow-2xl border border-gray-200 text-gray-900 overflow-hidden z-10"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                
                {{-- Header (Sticky) --}}
                <div class="flex items-start justify-between border-b border-gray-200 p-5 shrink-0 bg-white">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Atur Pengiriman</h3>
                        <p class="mt-1 text-sm font-semibold text-gray-600">#{{ $order->order_code }} — <span class="text-amber-800">{{ $order->customer_name }}</span></p>
                    </div>
                    <button wire:click="$dispatch('closeModal')" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Scrollable Body --}}
                <div class="flex-1 overflow-y-auto p-5">

                <form id="shipping-form-{{ $order->id }}" wire:submit="saveShipping">
                    {{-- Destination Address Preview --}}
                    <div class="mb-4 rounded-xl border border-amber-200/80 bg-amber-50/50 p-3 text-xs">
                        <div class="flex items-center gap-1.5 font-bold text-amber-950 mb-1">
                            <svg class="h-3.5 w-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Alamat Tujuan Pelanggan:</span>
                        </div>
                        <p class="text-gray-900 font-medium leading-relaxed">{{ $order->shipping_address ?: 'Belum diisi oleh pelanggan' }}</p>
                        <p class="text-gray-600 font-semibold mt-0.5">{{ $order->city }}{{ $order->postal_code ? ', ' . $order->postal_code : '' }}</p>
                    </div>

                    {{-- Shipping Method --}}
                    <div class="mb-5">
                        <label class="mb-2 block text-sm font-bold text-gray-900">Metode Pengiriman</label>
                        <div class="grid grid-cols-3 gap-2.5">
                            @php
                                $methods = [
                                    'expedition' => ['label' => 'Ekspedisi', 'icon' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0'],
                                    'internal_delivery' => ['label' => 'Antar Sendiri', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                                    'self_pickup' => ['label' => 'Ambil Sendiri', 'icon' => 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z'],
                                ];
                            @endphp
                            @foreach ($methods as $value => $method)
                                <label class="flex cursor-pointer flex-col items-center gap-2 rounded-xl border p-3.5 transition-all duration-150 {{ $shipping_method === $value ? 'border-amber-600 bg-amber-50 ring-2 ring-amber-500 shadow-sm' : 'border-gray-200 bg-white hover:border-amber-300 hover:bg-gray-50' }}">
                                    <input type="radio" wire:model.live="shipping_method" name="shipping_method" value="{{ $value }}" class="sr-only">
                                    <svg class="h-6 w-6 {{ $shipping_method === $value ? 'text-amber-700' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $method['icon'] }}"/>
                                    </svg>
                                    <span class="text-xs font-bold text-gray-900 text-center">{{ $method['label'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('shipping_method') <p class="mt-1.5 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Expedition Fields --}}
                    @if ($shipping_method === 'expedition')
                        <div class="space-y-3.5">
                            <div>
                                <label for="courier" class="mb-1 block text-sm font-bold text-gray-900">Nama Kurir / Truk Kargo Ekspedisi</label>
                                <input type="text" wire:model="courier" id="courier"
                                       class="w-full rounded-xl border border-gray-300 bg-white p-3 text-sm font-medium text-gray-900 placeholder-gray-400 focus:border-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                                       placeholder="Contoh: Aji Jaya Cargo, Kargo Jepara Indah, Dakota">
                                @error('courier') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="tracking_number" class="mb-1 block text-sm font-bold text-gray-900">Nomor Resi / No. Surat Jalan</label>
                                <input type="text" wire:model="tracking_number" id="tracking_number"
                                       class="w-full rounded-xl border border-gray-300 bg-white p-3 text-sm font-medium text-gray-900 placeholder-gray-400 focus:border-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                                       placeholder="Masukkan nomor resi kargo / no. surat jalan">
                                @error('tracking_number') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="shipping_date" class="mb-1 block text-sm font-bold text-gray-900">Tanggal Kirim</label>
                                <input type="date" wire:model="shipping_date" id="shipping_date"
                                       class="w-full rounded-xl border border-gray-300 bg-white p-3 text-sm font-medium text-gray-900 focus:border-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500">
                                @error('shipping_date') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    {{-- Internal Delivery Fields --}}
                    @elseif ($shipping_method === 'internal_delivery')
                        <div class="space-y-3.5">
                            <div>
                                <label for="driver_name" class="mb-1 block text-sm font-bold text-gray-900">Nama Driver / Pengantar</label>
                                <input type="text" wire:model="driver_name" id="driver_name"
                                       class="w-full rounded-xl border border-gray-300 bg-white p-3 text-sm font-medium text-gray-900 placeholder-gray-400 focus:border-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                                       placeholder="Nama driver armada toko">
                                @error('driver_name') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="vehicle_number" class="mb-1 block text-sm font-bold text-gray-900">Nomor Plat Kendaraan</label>
                                <input type="text" wire:model="vehicle_number" id="vehicle_number"
                                       class="w-full rounded-xl border border-gray-300 bg-white p-3 text-sm font-medium text-gray-900 placeholder-gray-400 focus:border-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                                       placeholder="Contoh: K 1234 XY">
                                @error('vehicle_number') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="shipping_date" class="mb-1 block text-sm font-bold text-gray-900">Tanggal Pengantaran</label>
                                <input type="date" wire:model="shipping_date" id="shipping_date"
                                       class="w-full rounded-xl border border-gray-300 bg-white p-3 text-sm font-medium text-gray-900 focus:border-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500">
                                @error('shipping_date') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    {{-- Self Pickup Fields --}}
                    @elseif ($shipping_method === 'self_pickup')
                        <div class="space-y-3.5">
                            <div>
                                <label for="pickup_date" class="mb-1 block text-sm font-bold text-gray-900">Tanggal Pengambilan</label>
                                <input type="date" wire:model="pickup_date" id="pickup_date"
                                       class="w-full rounded-xl border border-gray-300 bg-white p-3 text-sm font-medium text-gray-900 focus:border-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500">
                                @error('pickup_date') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @endif

                    {{-- Biaya Ongkir & Skema Pembayaran --}}
                    @if ($shipping_method && $shipping_method !== 'self_pickup')
                        <div class="mt-5 pt-4 border-t border-gray-200 space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Biaya Ongkos Kirim (Ongkir)</span>
                                </label>
                                <span class="text-[11px] font-semibold text-gray-500">Transparansi ke Pelanggan</span>
                            </div>

                            <div>
                                <label for="shipping_cost" class="mb-1 block text-xs font-bold text-gray-700">Nominal Ongkir (Rp)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-sm font-bold text-gray-500">Rp</span>
                                    <input type="text"
                                           wire:model="shipping_cost"
                                           id="shipping_cost"
                                           class="w-full rounded-xl border border-gray-300 bg-white pl-11 pr-3 py-2.5 text-sm font-bold text-gray-900 placeholder-gray-400 focus:border-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                                           placeholder="Contoh: 350.000"
                                           {{ $shipping_cost_type === 'free_shipping' ? 'disabled' : '' }}>
                                </div>
                                @error('shipping_cost') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-xs font-bold text-gray-700">Skema Pembayaran Ongkir</label>
                                <div class="space-y-2">
                                    <label class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition-all {{ $shipping_cost_type === 'buyer_to_driver' ? 'border-amber-600 bg-amber-50/70 ring-1 ring-amber-500' : 'border-gray-200 bg-white hover:bg-gray-50' }}">
                                        <input type="radio" wire:model.live="shipping_cost_type" name="shipping_cost_type" value="buyer_to_driver" class="mt-1 text-amber-700 focus:ring-amber-500">
                                        <div>
                                            <span class="text-xs font-bold text-gray-900 block">Bayar di Tempat ke Sopir / COD Ongkir (Umum untuk Meubel)</span>
                                            <p class="text-[11px] text-gray-600 mt-0.5 leading-relaxed">
                                                Pelanggan membayar ongkir tunai langsung ke sopir truk ekspedisi saat barang tiba. Total tagihan meubel di sistem tidak bertambah.
                                            </p>
                                        </div>
                                    </label>

                                    <label class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition-all {{ $shipping_cost_type === 'billed_to_order' ? 'border-amber-600 bg-amber-50/70 ring-1 ring-amber-500' : 'border-gray-200 bg-white hover:bg-gray-50' }}">
                                        <input type="radio" wire:model.live="shipping_cost_type" name="shipping_cost_type" value="billed_to_order" class="mt-1 text-amber-700 focus:ring-amber-500">
                                        <div>
                                            <span class="text-xs font-bold text-gray-900 block">Masuk ke Tagihan Toko (Ditransfer Pelanggan)</span>
                                            <p class="text-[11px] text-gray-600 mt-0.5 leading-relaxed">
                                                Biaya ongkir otomatis ditambahkan ke total tagihan pesanan. Pelanggan mentransfer total meubel + ongkir ke rekening toko.
                                            </p>
                                        </div>
                                    </label>

                                    <label class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition-all {{ $shipping_cost_type === 'free_shipping' ? 'border-amber-600 bg-amber-50/70 ring-1 ring-amber-500' : 'border-gray-200 bg-white hover:bg-gray-50' }}">
                                        <input type="radio" wire:model.live="shipping_cost_type" name="shipping_cost_type" value="free_shipping" class="mt-1 text-amber-700 focus:ring-amber-500">
                                        <div>
                                            <span class="text-xs font-bold text-gray-900 block">Gratis Ongkir (Ditanggung Toko / Promo)</span>
                                            <p class="text-[11px] text-gray-600 mt-0.5 leading-relaxed">
                                                Toko menanggung biaya ongkir meubel. Pelanggan tidak dibebankan biaya pengiriman sama sekali.
                                            </p>
                                        </div>
                                    </label>
                                </div>
                                @error('shipping_cost_type') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @endif

                </form>
                </div>

                {{-- Footer (Sticky) --}}
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 p-4 shrink-0 bg-white">
                    <button type="button" wire:click="$dispatch('closeModal')"
                            class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-100 transition-colors shadow-xs">
                        Batal
                    </button>
                    @if ($shipping_method)
                        <button type="submit" form="shipping-form-{{ $order->id }}"
                                class="rounded-xl bg-amber-700 hover:bg-amber-800 px-5 py-2.5 text-sm font-bold text-white shadow-md transition-colors">
                            Simpan Pengiriman
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
