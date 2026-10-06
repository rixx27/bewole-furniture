<div class="rounded-3xl border border-wood-border/60 bg-white p-5 shadow-sm space-y-3">
    <div class="flex items-center justify-between border-b border-wood-border/40 pb-3">
        <h2 class="text-sm font-bold text-wood-text flex items-center gap-2">
            <svg class="h-4 w-4 text-wood-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Alamat Pengiriman</span>
        </h2>
        @if (!$isAddressEmpty)
            <button
                type="button"
                wire:click="openModal"
                class="text-[11px] font-bold text-wood-primary hover:text-wood-primary-dark hover:underline cursor-pointer"
            >
                Ubah Alamat
            </button>
        @endif
    </div>

    @if ($isAddressEmpty)
        {{-- Kondisi Alamat Belum Diisi --}}
        <div class="rounded-2xl border-2 border-dashed border-amber-300 bg-amber-50/70 p-4 text-center space-y-2.5">
            <div class="flex items-center justify-center">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-100 text-amber-800 shadow-2xs">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>
            </div>
            <div>
                <p class="text-xs font-bold text-amber-950">Alamat Belum Diisi</p>
                <p class="mt-0.5 text-[11px] text-amber-800 leading-relaxed">
                    Pesanan dan harga telah terkonfirmasi. Silakan lengkapi alamat tujuan pengiriman Anda.
                </p>
            </div>
            <button
                type="button"
                wire:click="openModal"
                class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-wood-primary px-3.5 py-2 text-xs font-bold text-white hover:bg-wood-primary-dark transition-all shadow-sm cursor-pointer"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Isi Alamat Lengkap</span>
            </button>
        </div>
    @else
        {{-- Kondisi Alamat Sudah Diisi --}}
        <div class="space-y-1 text-xs">
            <p class="text-wood-text font-medium leading-relaxed whitespace-pre-line">{{ $order->shipping_address }}</p>
            <p class="text-wood-muted font-semibold">
                {{ $order->city }}{{ $order->postal_code ? ', ' . $order->postal_code : '' }}
            </p>
            <div class="pt-2">
                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Alamat Tujuan Tersimpan
                </span>
            </div>
        </div>
    @endif

    {{-- MODAL FORM ALAMAT PENGIRIMAN --}}
    @if ($showModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
            x-data
            x-init="$el.style.display = 'flex'"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        >
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeModal"></div>

            {{-- Dialog Box --}}
            <div
                class="relative w-full max-w-lg rounded-3xl bg-white p-6 sm:p-7 shadow-2xl border border-wood-border/60 text-wood-text z-10 space-y-4 max-h-[90vh] overflow-y-auto sidebar-scroll"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            >
                <div class="flex items-start justify-between border-b border-wood-border/40 pb-3">
                    <div>
                        <div class="inline-flex items-center gap-1.5 rounded-full bg-wood-light/60 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider text-wood-secondary mb-1 border border-wood-border/40">
                            <svg class="h-3 w-3 text-wood-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Pengiriman Ekspedisi</span>
                        </div>
                        <h3 class="font-serif text-lg sm:text-xl font-bold text-wood-text">
                            Lengkapi Alamat Pengiriman
                        </h3>
                        <p class="text-xs text-wood-muted mt-0.5">
                            Pastikan alamat dapat dijangkau oleh armada truk ekspedisi meubel.
                        </p>
                    </div>
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="rounded-full p-1.5 text-wood-muted hover:bg-wood-bg hover:text-wood-text transition-colors cursor-pointer"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveAddress" class="space-y-4 pt-1">
                    {{-- Dropdown Provinsi & Kota/Kabupaten (Sesuai Formulir Pembelian) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        {{-- Provinsi --}}
                        <div>
                            <label for="select-province" class="block text-xs font-semibold text-wood-text">
                                Provinsi <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative mt-1">
                                <select
                                    id="select-province"
                                    wire:model.live="province"
                                    class="w-full appearance-none rounded-2xl border border-wood-border/60 bg-white/80 py-2.5 pl-3.5 pr-9 text-xs font-medium text-wood-text focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20 cursor-pointer"
                                >
                                    <option value="">Pilih Provinsi</option>
                                    @foreach($provinces as $prov)
                                        <option value="{{ $prov }}">{{ $prov }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-wood-muted">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            @error('province')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kota / Kabupaten --}}
                        <div>
                            <label for="select-city" class="block text-xs font-semibold text-wood-text">
                                Kota / Kabupaten <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative mt-1">
                                <select
                                    id="select-city"
                                    wire:model="city"
                                    @if(empty($province)) disabled @endif
                                    class="w-full appearance-none rounded-2xl border border-wood-border/60 bg-white/80 py-2.5 pl-3.5 pr-9 text-xs font-medium text-wood-text focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20 disabled:opacity-50 disabled:bg-gray-100 disabled:cursor-not-allowed cursor-pointer"
                                >
                                    <option value="">Pilih Kota / Kabupaten</option>
                                    @foreach($cities as $cty)
                                        <option value="{{ $cty }}">{{ $cty }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-wood-muted">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            @error('city')
                                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Alamat Lengkap --}}
                    <div>
                        <label for="input-shipping-address" class="block text-xs font-semibold text-wood-text">
                            Alamat Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            id="input-shipping-address"
                            wire:model="shipping_address"
                            rows="3"
                            placeholder="Jalan, No. Rumah, RT/RW, Kecamatan, Kelurahan..."
                            class="mt-1 w-full rounded-2xl border border-wood-border/60 bg-white/80 p-3 text-xs font-medium text-wood-text placeholder-wood-muted focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                        ></textarea>
                        @error('shipping_address')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kode Pos --}}
                    <div>
                        <label for="input-postal" class="block text-xs font-semibold text-wood-text">
                            Kode Pos <span class="text-xs font-normal text-wood-muted">(Opsional)</span>
                        </label>
                        <input
                            type="text"
                            id="input-postal"
                            wire:model="postal_code"
                            placeholder="Contoh: 59411"
                            class="mt-1 w-full sm:w-1/2 rounded-2xl border border-wood-border/60 bg-white/80 px-4 py-2.5 text-xs font-medium text-wood-text placeholder-wood-muted focus:border-wood-primary focus:outline-none focus:ring-2 focus:ring-wood-primary/20"
                        >
                        @error('postal_code')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-wood-border/40">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="rounded-xl border border-wood-border bg-white px-4 py-2.5 text-xs font-bold text-wood-muted hover:bg-wood-bg hover:text-wood-text transition-colors cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 rounded-xl bg-wood-primary px-5 py-2.5 text-xs font-bold text-white hover:bg-wood-primary-dark shadow-sm transition-all cursor-pointer disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="saveAddress">Simpan Alamat Pengiriman</span>
                            <span wire:loading wire:target="saveAddress">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
