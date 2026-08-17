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
            <div class="relative flex flex-col w-full max-w-md max-h-[90vh] rounded-2xl bg-white shadow-2xl border border-gray-200 text-gray-900 overflow-hidden z-10"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                
                {{-- Header (Sticky) --}}
                <div class="flex items-start justify-between border-b border-gray-200 p-5 shrink-0 bg-white">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Ubah Status Pembayaran</h3>
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

                {{-- Status Saat Ini & Total --}}
                <div class="mb-5 rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-xs space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-gray-600">Status Pembayaran Saat Ini:</span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900 border border-amber-300">
                            {{ $order->payment_status_label }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm border-t border-gray-200 pt-2">
                        <span class="font-medium text-gray-600">Total Tagihan:</span>
                        <span class="font-extrabold text-gray-900 text-base">{{ $order->formatted_total_price }}</span>
                    </div>
                </div>

                <form id="payment-form-{{ $order->id }}" wire:submit="updatePayment">
                    {{-- Payment Status Selection --}}
                    <div class="mb-5">
                        <label class="mb-2 block text-sm font-bold text-gray-900">Pilih Status Pembayaran Baru</label>
                        <div class="space-y-2.5">
                            @php
                                $paymentStatuses = [
                                    \App\Enums\PaymentStatus::Unpaid,
                                    \App\Enums\PaymentStatus::Paid,
                                    \App\Enums\PaymentStatus::Failed,
                                    \App\Enums\PaymentStatus::Refunded,
                                ];
                            @endphp
                            @foreach ($paymentStatuses as $status)
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border p-3.5 transition-all duration-150 {{ $payment_status === $status->value ? 'border-amber-600 bg-amber-50 ring-2 ring-amber-500 shadow-sm' : 'border-gray-200 bg-white hover:border-amber-300 hover:bg-gray-50' }}">
                                    <input type="radio" wire:model="payment_status" name="payment_status" value="{{ $status->value }}" class="h-4 w-4 text-amber-700 border-gray-300 focus:ring-amber-600">
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-900">{{ $status->label() }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('payment_status') <p class="mt-1.5 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="mb-5">
                        <label for="notes" class="mb-1.5 block text-sm font-bold text-gray-900">Catatan (Opsional)</label>
                        <textarea wire:model="notes" id="notes" rows="2"
                                  class="w-full rounded-xl border border-gray-300 bg-white p-3 text-sm font-medium text-gray-900 placeholder-gray-400 focus:border-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                                  placeholder="Tambahkan catatan..."></textarea>
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
                    <button type="submit" form="payment-form-{{ $order->id }}"
                            class="rounded-xl bg-amber-700 hover:bg-amber-800 px-5 py-2.5 text-sm font-bold text-white shadow-md transition-colors">
                        Simpan Pembayaran
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
