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
                        <h3 class="text-xl font-bold text-gray-900">Ubah Status Pesanan</h3>
                        <p class="mt-1 text-sm font-semibold text-gray-600">#{{ $order->order_code }} — <span class="text-amber-800">{{ $order->customer_name }}</span></p>
                    </div>
                    <button wire:click="$dispatch('closeModal')" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Scrollable Body --}}
                <div class="flex-1 overflow-y-auto p-5 space-y-4">
                    {{-- Status Saat Ini --}}
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-3.5 shadow-xs">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-600">Status Saat Ini:</span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-950 border border-amber-300">
                                <x-order-status-icon :status="$order->status" class="h-4 w-4 text-amber-900" />
                                <span>{{ $order->status_label }}</span>
                            </span>
                        </div>
                    </div>

                    <form id="status-form-{{ $order->id }}" wire:submit="updateStatus" class="space-y-4">
                        {{-- Status Selection --}}
                        <div>
                            <label class="mb-2 block text-sm font-bold text-gray-900">Pilih Status Baru</label>
                            @if (empty($availableStatuses))
                                <p class="text-sm font-medium text-gray-500">Tidak ada perubahan status yang tersedia.</p>
                            @else
                                <div class="space-y-2.5">
                                    @foreach ($availableStatuses as $status)
                                        @php
                                            $canSelect = $status['canSelect'];
                                            $isCurrent = $status['isCurrent'];
                                            $isSelected = $newStatus === $status['value'];

                                            $cardClass = 'flex items-start gap-3 rounded-xl border p-3.5 transition-all duration-150 ';
                                            if ($canSelect) {
                                                $cardClass .= 'cursor-pointer ' . ($isSelected ? 'border-amber-600 bg-amber-50 ring-2 ring-amber-500 shadow-sm' : 'border-gray-200 bg-white hover:border-amber-300 hover:bg-gray-50');
                                            } else {
                                                $cardClass .= 'opacity-60 cursor-not-allowed bg-gray-100/70 border-gray-200 select-none';
                                            }
                                        @endphp

                                        <label class="{{ $cardClass }}">
                                            <input type="radio"
                                                   name="newStatus"
                                                   wire:model.live="newStatus"
                                                   value="{{ $status['value'] }}"
                                                   @disabled(!$canSelect)
                                                   class="mt-1 h-4 w-4 text-amber-700 border-gray-300 focus:ring-amber-600 disabled:opacity-50 disabled:cursor-not-allowed">
                                            
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between gap-2">
                                                    <div class="flex items-center gap-2 font-bold text-sm">
                                                        <x-order-status-icon :status="$status['value']" class="h-4 w-4 shrink-0 {{ $canSelect ? ($isSelected ? 'text-amber-800' : 'text-amber-600') : ($isCurrent ? 'text-amber-700' : 'text-gray-400') }}" />
                                                        <span class="{{ $canSelect ? 'text-gray-900 font-bold' : ($isCurrent ? 'text-gray-800 font-bold' : 'text-gray-500 font-medium') }}">
                                                            {{ $status['label'] }}
                                                        </span>
                                                    </div>

                                                    @if ($isCurrent)
                                                        <span class="text-[10px] uppercase font-bold text-amber-900 bg-amber-100 px-2 py-0.5 rounded-full border border-amber-300 shrink-0">
                                                            Status Saat Ini
                                                        </span>
                                                    @elseif (!$canSelect)
                                                        <span class="text-[10px] uppercase font-bold text-gray-400 bg-gray-200/60 px-2 py-0.5 rounded-full shrink-0">
                                                            Tidak Tersedia
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="mt-1 text-xs font-medium {{ $canSelect ? 'text-gray-600' : 'text-gray-400' }} leading-relaxed">
                                                    {{ $status['description'] }}
                                                </p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            @error('newStatus') <p class="mt-1.5 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Foto Dokumentasi Progres (Wajib untuk status pengerjaan) --}}
                        @if ($this->requiresPhoto)
                            <div wire:key="photo-section-{{ $newStatus }}"
                                 class="rounded-xl border border-amber-200 bg-amber-50/60 p-4 shadow-xs"
                                 data-order-code="{{ $order->order_code }}"
                                 x-data="orderProgressCapture('{{ $order->order_code }}')"
                                 @close-modal.window="stopCamera()">

                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <label class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                                        <svg class="h-4 w-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>Foto Progres Pengerjaan</span>
                                        <span class="rounded-sm bg-red-100 px-1.5 py-0.5 text-[10px] font-bold uppercase text-red-700">Wajib</span>
                                    </label>

                                    <span class="text-[11px] font-medium text-amber-900 bg-amber-100/80 px-2 py-0.5 rounded-md">
                                        Otomatis Waktu & GPS
                                    </span>
                                </div>

                                <p class="text-xs text-gray-600 mb-3 leading-relaxed">
                                    Ambil foto fisik barang lewat kamera atau pilih file. Sistem otomatis menyematkan tanggal, waktu, dan titik koordinat GPS sebagai bukti autentik bagi pelanggan.
                                </p>

                                {{-- Hidden file input --}}
                                <input type="file"
                                       accept="image/*"
                                       x-ref="fileInput"
                                       @change="processFile($event)"
                                       class="hidden">

                                {{-- 1. Mode Kamera Live Aktif (Hanya muncul saat Buka Kamera diklik) --}}
                                <div x-show="cameraActive" style="display: none;" class="space-y-3">
                                    {{-- Video Viewfinder Container --}}
                                    <div class="relative overflow-hidden rounded-2xl bg-black border border-gray-800 shadow-md">
                                        <video x-ref="videoEl" autoplay playsinline muted class="w-full h-56 sm:h-64 object-cover"></video>

                                        {{-- Overlay Frame Helper --}}
                                        <div class="pointer-events-none absolute inset-0 border-2 border-dashed border-amber-400/50 m-3 rounded-xl flex items-center justify-center">
                                            <span class="text-[11px] font-bold text-white bg-black/60 px-3 py-1 rounded-full backdrop-blur-xs">
                                                Arahkan Kamera ke Produk
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Control Buttons on Camera (Di luar dan di bawah video agar tidak pernah tertutup / terpotong) --}}
                                    <div class="flex items-center gap-2 pt-1" style="display: flex; gap: 8px; width: 100%;">
                                        {{-- Tombol Jepret Foto --}}
                                        <button type="button"
                                                @click="captureFromCamera()"
                                                :disabled="processing"
                                                style="background-color: #b45309; color: #ffffff; padding: 12px 18px; border-radius: 12px; font-weight: 700; font-size: 14px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; flex: 1; border: none; cursor: pointer;"
                                                class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-amber-700 hover:bg-amber-800 text-white px-5 py-3 font-bold text-sm shadow-md transition-all active:scale-95 cursor-pointer disabled:opacity-50">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span x-text="processing ? 'Memproses...' : 'Jepret Foto Sekarang'">Jepret Foto Sekarang</span>
                                        </button>

                                        {{-- Tombol Putar Kamera Depan / Belakang --}}
                                        <button type="button"
                                                @click="switchCamera()"
                                                :disabled="processing"
                                                title="Putar Kamera Depan / Belakang"
                                                style="background-color: #374151; color: #ffffff; padding: 12px 14px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; border: none; cursor: pointer;"
                                                class="inline-flex items-center justify-center rounded-xl bg-gray-700 hover:bg-gray-800 text-white p-3 font-semibold transition-colors cursor-pointer disabled:opacity-50">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </button>

                                        {{-- Tombol Batal / Tutup Kamera --}}
                                        <button type="button"
                                                @click="stopCamera()"
                                                style="background-color: #f3f4f6; color: #374151; padding: 12px 16px; border-radius: 12px; font-weight: 600; font-size: 13px; border: 1px solid #d1d5db; cursor: pointer;"
                                                class="rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-3 font-semibold text-xs border border-gray-300 transition-colors cursor-pointer">
                                            Batal
                                        </button>
                                    </div>
                                </div>

                                {{-- 2. Pratinjau Foto dengan Watermark --}}
                                @if ($photoData)
                                    <div x-show="!cameraActive" class="relative overflow-hidden rounded-xl border border-gray-300 bg-gray-900 shadow-xs">
                                        <img src="{{ $photoData }}" alt="Foto Progres" class="w-full max-h-56 object-contain bg-black/40">

                                        <div class="absolute top-2 right-2 flex items-center gap-2">
                                            <button type="button"
                                                    @click="startCamera()"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-black/70 px-2.5 py-1 text-xs font-semibold text-white backdrop-blur-sm hover:bg-black transition-colors cursor-pointer">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                Ambil Ulang
                                            </button>
                                            <button type="button"
                                                    wire:click="clearPhoto"
                                                    class="inline-flex items-center rounded-lg bg-red-600/80 p-1 text-white backdrop-blur-sm hover:bg-red-700 transition-colors cursor-pointer">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="p-2.5 bg-gray-900 text-white text-[11px] flex items-center justify-between border-t border-gray-800">
                                            <span class="flex items-center gap-1.5 text-emerald-400 font-semibold">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Watermark Berhasil Diterapkan
                                            </span>
                                            @if ($latitude && $longitude)
                                                <span class="text-gray-400 font-mono text-[10px]">
                                                    {{ $latitude }}, {{ $longitude }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @elseif (! $photoData)
                                    {{-- 3. Pilihan Aksi Sebelum Foto Diambil --}}
                                    <div x-show="!cameraActive" class="space-y-2.5">
                                        {{-- Loading Spinner saat Proses --}}
                                        <div x-show="processing" style="display: none;" class="flex flex-col items-center justify-center p-6 rounded-xl border border-amber-300 bg-white">
                                            <svg class="h-7 w-7 animate-spin text-amber-700 mb-2" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <p class="text-xs font-bold text-amber-900" x-text="statusMessage || 'Memproses foto...'"></p>
                                        </div>

                                        {{-- Tombol Pilihan Aksi --}}
                                        <div x-show="!processing" class="space-y-2">
                                            {{-- Tombol Buka Kamera --}}
                                            <button type="button"
                                                    @click="startCamera()"
                                                    style="background-color: #b45309; color: #ffffff; padding: 14px; border-radius: 12px; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; border: none; cursor: pointer;"
                                                    class="w-full flex items-center justify-center gap-2.5 rounded-xl bg-amber-700 hover:bg-amber-800 text-white p-3.5 font-bold text-sm shadow-sm transition-all active:scale-[0.99] cursor-pointer">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span>Buka Kamera (Ambil Foto Langsung)</span>
                                            </button>

                                            {{-- Tombol Alternatif Upload File --}}
                                            <div class="flex items-center justify-center pt-1">
                                                <button type="button"
                                                        @click="$refs.fileInput.click()"
                                                        class="text-xs font-semibold text-gray-500 hover:text-amber-800 flex items-center gap-1.5 transition-colors cursor-pointer py-1">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                    </svg>
                                                    <span>Atau unggah foto dari file / galeri</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @error('photoData')
                                    <p class="mt-2 text-xs font-bold text-red-600 flex items-center gap-1">
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>
                        @endif

                        {{-- Notes --}}
                        <div>
                            <label for="notes" class="mb-1.5 block text-sm font-bold text-gray-900">Catatan Perubahan (Opsional)</label>
                            <textarea wire:model="notes" id="notes" rows="2"
                                      class="w-full rounded-xl border border-gray-300 bg-white p-3 text-sm font-medium text-gray-900 placeholder-gray-400 focus:border-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                                      placeholder="Tambahkan catatan perubahan..."></textarea>
                            @error('notes') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </form>
                </div>

                {{-- Footer (Sticky) --}}
                <div class="flex items-center justify-end gap-3 border-t border-gray-200 p-4 shrink-0 bg-white">
                    <button type="button" wire:click="$dispatch('closeModal')"
                            class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-100 transition-colors shadow-xs">
                        Batal
                    </button>
                    @php
                        $hasSelectable = collect($availableStatuses)->contains('canSelect', true);
                    @endphp
                    @if ($hasSelectable)
                        <button type="submit" form="status-form-{{ $order->id }}"
                                class="rounded-xl bg-amber-700 hover:bg-amber-800 px-5 py-2.5 text-sm font-bold text-white shadow-md transition-colors">
                            Simpan Perubahan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
