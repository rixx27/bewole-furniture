<div>
    @if ($show && $order)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-init="$el.style.display = 'flex'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$dispatch('closeModal')"></div>
            <div class="relative w-full max-w-md rounded-xl bg-card p-6 shadow-2xl border border-border"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-text-primary dark:text-white">Ubah Status Pesanan</h3>
                        <p class="text-sm text-text-muted">#{{ $order->order_code }} - {{ $order->customer_name }}</p>
                    </div>
                    <button wire:click="$dispatch('closeModal')" class="rounded-lg p-1.5 text-text-muted hover:bg-bg-secondary transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="mb-4 rounded-lg border border-border bg-bg-secondary/50 p-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-text-muted">Status Saat Ini</span>
                        @php $color = $order->status_color; @endphp
                        <span class="inline-flex items-center gap-1 rounded-full bg-{{ $color }}-50 px-2.5 py-0.5 text-xs font-medium text-{{ $color }}-700 dark:bg-{{ $color }}-950 dark:text-{{ $color }}-300">
                            {{ $order->status_label }}
                        </span>
                    </div>
                </div>

                <form wire:submit="updateStatus">
                    {{-- Status Selection --}}
                    <div class="mb-4">
                        <label class="mb-2 block text-sm font-medium text-text-primary dark:text-white">Pilih Status Baru</label>
                        @if (empty($availableStatuses))
                            <p class="text-sm text-text-muted">Tidak ada perubahan status yang tersedia.</p>
                        @else
                            <div class="space-y-2">
                                @foreach ($availableStatuses as $status)
                                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-border p-3 hover:bg-bg-secondary transition-colors {{ $newStatus === $status->value ? 'border-primary bg-primary/5' : '' }}">
                                        <input type="radio" name="newStatus" wire:model="newStatus" value="{{ $status->value }}" class="h-4 w-4 text-primary border-border focus:ring-primary">
                                        <div>
                                            <p class="text-sm font-medium text-text-primary dark:text-white">{{ $status->label() }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        @error('newStatus') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="mb-4">
                        <label for="notes" class="mb-2 block text-sm font-medium text-text-primary dark:text-white">Catatan (Opsional)</label>
                        <textarea wire:model="notes" id="notes" rows="3"
                                  class="w-full rounded-lg border border-border bg-card p-2.5 text-sm text-text-primary placeholder-text-muted focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary"
                                  placeholder="Tambahkan catatan..."></textarea>
                        @error('notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 border-t border-border pt-4">
                        <button type="button" wire:click="$dispatch('closeModal')"
                                class="rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                            Batal
                        </button>
                        @if (!empty($availableStatuses))
                            <button type="submit"
                                    class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors">
                                Simpan Perubahan
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
