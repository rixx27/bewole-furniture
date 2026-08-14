<div>
    @if ($show && $order)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-init="$el.style.display = 'flex'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="$dispatch('closeModal')"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl border border-gray-200 text-gray-900"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                
                {{-- Modal Header --}}
                <div class="mb-5 flex items-start justify-between border-b border-gray-200 pb-4">
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

                {{-- Status Saat Ini --}}
                <div class="mb-5 rounded-xl border border-gray-200 bg-gray-50 p-3.5 shadow-xs">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-gray-600">Status Saat Ini:</span>
                        @php 
                            $currentEnum = \App\Enums\OrderStatus::tryFrom($order->status);
                        @endphp
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900 border border-amber-300">
                            <span>{{ $currentEnum?->emoji() ?? '⚙️' }}</span>
                            <span>{{ $order->status_label }}</span>
                        </span>
                    </div>
                </div>

                <form wire:submit="updateStatus">
                    {{-- Status Selection --}}
                    <div class="mb-5">
                        <label class="mb-2 block text-sm font-bold text-gray-900">Pilih Status Baru</label>
                        @if (empty($availableStatuses))
                            <p class="text-sm font-medium text-gray-500">Tidak ada perubahan status yang tersedia.</p>
                        @else
                            <div class="space-y-2.5 max-h-80 overflow-y-auto pr-1">
                                @foreach ($availableStatuses as $status)
                                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border p-3.5 transition-all duration-150 {{ $newStatus === $status['value'] ? 'border-amber-600 bg-amber-50 ring-2 ring-amber-500 shadow-sm' : 'border-gray-200 bg-white hover:border-amber-300 hover:bg-gray-50' }}">
                                        <input type="radio" name="newStatus" wire:model.live="newStatus" value="{{ $status['value'] }}" class="mt-1 h-4 w-4 text-amber-700 border-gray-300 focus:ring-amber-600">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 font-bold text-sm text-gray-900">
                                                <span class="text-base">{{ $status['emoji'] }}</span>
                                                <span class="text-gray-900 font-bold">{{ $status['label'] }}</span>
                                            </div>
                                            <p class="mt-1 text-xs font-medium text-gray-600 leading-relaxed">{{ $status['description'] }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        @error('newStatus') <p class="mt-1.5 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="mb-5">
                        <label for="notes" class="mb-1.5 block text-sm font-bold text-gray-900">Catatan (Opsional)</label>
                        <textarea wire:model="notes" id="notes" rows="2"
                                  class="w-full rounded-xl border border-gray-300 bg-white p-3 text-sm font-medium text-gray-900 placeholder-gray-400 focus:border-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                                  placeholder="Tambahkan catatan..."></textarea>
                        @error('notes') <p class="mt-1 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                        <button type="button" wire:click="$dispatch('closeModal')"
                                class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-100 transition-colors shadow-xs">
                            Batal
                        </button>
                        @if (!empty($availableStatuses))
                            <button type="submit"
                                    class="rounded-xl bg-amber-700 hover:bg-amber-800 px-5 py-2.5 text-sm font-bold text-white shadow-md transition-colors">
                                Simpan Perubahan
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
