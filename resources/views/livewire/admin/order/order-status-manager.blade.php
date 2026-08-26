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
