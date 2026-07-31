<div>
    @if ($show && $order)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-init="$el.style.display = 'flex'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$dispatch('closeModal')"></div>
            <div class="relative w-full max-w-lg rounded-xl bg-card p-6 shadow-2xl border border-border"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-text-primary dark:text-white">Atur Pengiriman</h3>
                        <p class="text-sm text-text-muted">#{{ $order->order_code }} - {{ $order->customer_name }}</p>
                    </div>
                    <button wire:click="$dispatch('closeModal')" class="rounded-lg p-1.5 text-text-muted hover:bg-bg-secondary transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="saveShipping">
                    {{-- Shipping Method --}}
                    <div class="mb-4">
                        <label class="mb-2 block text-sm font-medium text-text-primary dark:text-white">Metode Pengiriman</label>
                        <div class="grid grid-cols-3 gap-2">
                            @php
                                $methods = [
                                    'expedition' => ['label' => 'Ekspedisi', 'icon' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0'],
                                    'internal_delivery' => ['label' => 'Antar Sendiri', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                                    'self_pickup' => ['label' => 'Ambil Sendiri', 'icon' => 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z'],
                                ];
                            @endphp
                            @foreach ($methods as $value => $method)
                                <label class="flex cursor-pointer flex-col items-center gap-1.5 rounded-lg border border-border p-3 hover:bg-bg-secondary transition-colors {{ $shipping_method === $value ? 'border-primary bg-primary/5 ring-1 ring-primary' : '' }}">
                                    <input type="radio" wire:model.live="shipping_method" name="shipping_method" value="{{ $value }}" class="sr-only">
                                    <svg class="h-5 w-5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $method['icon'] }}"/>
                                    </svg>
                                    <span class="text-xs font-medium text-text-primary">{{ $method['label'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('shipping_method') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Expedition Fields --}}
                    @if ($shipping_method === 'expedition')
                        <div class="space-y-3">
                            <div>
                                <label for="courier" class="mb-1 block text-sm font-medium text-text-primary dark:text-white">Nama Kurir</label>
                                <input type="text" wire:model="courier" id="courier"
                                       class="w-full rounded-lg border border-border bg-card p-2.5 text-sm text-text-primary placeholder-text-muted focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary"
                                       placeholder="Contoh: JNE, J&T, SiCepat">
                                @error('courier') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="tracking_number" class="mb-1 block text-sm font-medium text-text-primary dark:text-white">Nomor Resi</label>
                                <input type="text" wire:model="tracking_number" id="tracking_number"
                                       class="w-full rounded-lg border border-border bg-card p-2.5 text-sm text-text-primary placeholder-text-muted focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary"
                                       placeholder="Masukkan nomor resi">
                                @error('tracking_number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="shipping_date" class="mb-1 block text-sm font-medium text-text-primary dark:text-white">Tanggal Kirim</label>
                                <input type="date" wire:model="shipping_date" id="shipping_date"
                                       class="w-full rounded-lg border border-border bg-card p-2.5 text-sm text-text-primary focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
                                @error('shipping_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    {{-- Internal Delivery Fields --}}
                    @elseif ($shipping_method === 'internal_delivery')
                        <div class="space-y-3">
                            <div>
                                <label for="driver_name" class="mb-1 block text-sm font-medium text-text-primary dark:text-white">Nama Driver</label>
                                <input type="text" wire:model="driver_name" id="driver_name"
                                       class="w-full rounded-lg border border-border bg-card p-2.5 text-sm text-text-primary placeholder-text-muted focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary"
                                       placeholder="Nama driver pengiriman">
                                @error('driver_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="vehicle_number" class="mb-1 block text-sm font-medium text-text-primary dark:text-white">Nomor Kendaraan</label>
                                <input type="text" wire:model="vehicle_number" id="vehicle_number"
                                       class="w-full rounded-lg border border-border bg-card p-2.5 text-sm text-text-primary placeholder-text-muted focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary"
                                       placeholder="Contoh: B 1234 XYZ">
                                @error('vehicle_number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="shipping_date" class="mb-1 block text-sm font-medium text-text-primary dark:text-white">Tanggal Kirim</label>
                                <input type="date" wire:model="shipping_date" id="shipping_date"
                                       class="w-full rounded-lg border border-border bg-card p-2.5 text-sm text-text-primary focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
                                @error('shipping_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    {{-- Self Pickup Fields --}}
                    @elseif ($shipping_method === 'self_pickup')
                        <div class="space-y-3">
                            <div>
                                <label for="pickup_date" class="mb-1 block text-sm font-medium text-text-primary dark:text-white">Tanggal Pengambilan</label>
                                <input type="date" wire:model="pickup_date" id="pickup_date"
                                       class="w-full rounded-lg border border-border bg-card p-2.5 text-sm text-text-primary focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
                                @error('pickup_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-border pt-4">
                        <button type="button" wire:click="$dispatch('closeModal')"
                                class="rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                            Batal
                        </button>
                        @if ($shipping_method)
                            <button type="submit"
                                    class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
                                Simpan Pengiriman
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
