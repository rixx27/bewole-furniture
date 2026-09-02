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
                <div class="mb-5 rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-xs space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-600">Status Pembayaran Saat Ini:</span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-{{ $order->payment_status_color }}-100 px-3 py-0.5 text-xs font-bold text-{{ $order->payment_status_color }}-900 border border-{{ $order->payment_status_color }}-300">
                            {{ $order->payment_status_label }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-200 pt-2">
                        <span class="font-medium text-gray-600">Total Tagihan:</span>
                        <span class="font-extrabold text-gray-900 text-base">{{ $order->formatted_total_price }}</span>
                    </div>
                    @if ($order->down_payment_amount > 0)
                        <div class="flex items-center justify-between text-xs text-amber-800 pt-1">
                            <span>DP Diterima Sebelumnya:</span>
                            <span class="font-bold">{{ $order->formatted_down_payment_amount }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-rose-700 pt-0.5">
                            <span>Sisa Tagihan:</span>
                            <span class="font-bold">{{ $order->formatted_remaining_payment }}</span>
                        </div>
                    @endif
                </div>

                {{-- Bukti Pembayaran Preview jika ada --}}
                @if ($order->has_payment_proof || $order->has_final_payment_proof)
                    <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50/50 p-4 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-amber-900">Bukti Transfer dari Pelanggan</h4>
                        <div class="grid grid-cols-2 gap-3">
                            @if ($order->has_payment_proof)
                                <div class="space-y-1">
                                    <span class="text-[11px] font-semibold text-gray-600 block">Bukti Pembayaran / DP:</span>
                                    <a href="{{ $order->payment_proof_url }}" target="_blank" class="block aspect-video rounded-lg overflow-hidden border border-gray-200 hover:opacity-90 transition-opacity bg-black/5">
                                        <img src="{{ $order->payment_proof_url }}" alt="Bukti Transfer" class="h-full w-full object-cover">
                                    </a>
                                </div>
                            @endif

                            @if ($order->has_final_payment_proof)
                                <div class="space-y-1">
                                    <span class="text-[11px] font-semibold text-emerald-800 block">Bukti Pelunasan:</span>
                                    <a href="{{ $order->final_payment_proof_url }}" target="_blank" class="block aspect-video rounded-lg overflow-hidden border border-emerald-300 hover:opacity-90 transition-opacity bg-black/5">
                                        <img src="{{ $order->final_payment_proof_url }}" alt="Bukti Pelunasan" class="h-full w-full object-cover">
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <form id="payment-form-{{ $order->id }}" wire:submit="updatePayment">
                    {{-- Payment Status Selection --}}
                    <div class="mb-5">
                        <label class="mb-2 block text-sm font-bold text-gray-900">Pilih Status Pembayaran Baru</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @php
                                $paymentStatuses = [
                                    \App\Enums\PaymentStatus::Unpaid,
                                    \App\Enums\PaymentStatus::DownPayment,
                                    \App\Enums\PaymentStatus::Paid,
                                    \App\Enums\PaymentStatus::Failed,
                                    \App\Enums\PaymentStatus::Refunded,
                                ];
                            @endphp
                            @foreach ($paymentStatuses as $status)
                                <label class="flex cursor-pointer items-center gap-2.5 rounded-xl border p-3 transition-all duration-150 {{ $payment_status === $status->value ? 'border-amber-600 bg-amber-50 ring-2 ring-amber-500 shadow-sm' : 'border-gray-200 bg-white hover:border-amber-300 hover:bg-gray-50' }}">
                                    <input type="radio" wire:model.live="payment_status" name="payment_status" value="{{ $status->value }}" class="h-4 w-4 text-amber-700 border-gray-300 focus:ring-amber-600">
                                    <div class="flex-1">
                                        <p class="text-xs font-bold text-gray-900">{{ $status->label() }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('payment_status') <p class="mt-1.5 text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nominal DP Input if DownPayment selected --}}
                    @if ($payment_status === 'down_payment')
                        <div class="mb-5 rounded-xl border border-indigo-200 bg-indigo-50/50 p-4 space-y-3" x-data>
                            <label for="down_payment_amount" class="block text-xs font-bold uppercase tracking-wider text-indigo-900">
                                Nominal DP Masuk (Rp) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-2.5 text-xs font-bold text-gray-500">Rp</span>
                                <input
                                    type="text"
                                    wire:model="down_payment_amount"
                                    id="down_payment_amount"
                                    class="w-full rounded-xl border border-indigo-300 bg-white pl-10 pr-4 py-2 text-sm font-bold text-gray-900 placeholder-gray-400 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500 outline-hidden"
                                    placeholder="2.000.000"
                                >
                            </div>
                            @error('down_payment_amount') <p class="text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                            <p class="text-[11px] text-indigo-700">
                                Total pesanan: {{ $order->formatted_total_price }}. Masukkan nominal DP yang telah diverifikasi.
                            </p>
                        </div>
                    @endif

                    {{-- Rejection Reason if Failed selected --}}
                    @if ($payment_status === 'failed')
                        <div class="mb-5 rounded-xl border border-red-200 bg-red-50/50 p-4 space-y-2">
                            <label for="rejection_reason" class="block text-xs font-bold uppercase tracking-wider text-red-900">
                                Alasan Penolakan Bukti Transfer (Opsional)
                            </label>
                            <textarea
                                wire:model="rejection_reason"
                                id="rejection_reason"
                                rows="2"
                                class="w-full rounded-xl border border-red-300 bg-white p-3 text-xs font-medium text-gray-900 placeholder-gray-400 focus:border-red-600 focus:ring-2 focus:ring-red-500 outline-hidden"
                                placeholder="Contoh: Nominal transfer tidak sesuai, bukti buram atau tidak terbaca..."
                            ></textarea>
                            @error('rejection_reason') <p class="text-xs font-bold text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    {{-- Notes --}}
                    <div class="mb-5">
                        <label for="notes" class="mb-1.5 block text-sm font-bold text-gray-900">Catatan Internal (Opsional)</label>
                        <textarea wire:model="notes" id="notes" rows="2"
                                  class="w-full rounded-xl border border-gray-300 bg-white p-3 text-sm font-medium text-gray-900 placeholder-gray-400 focus:border-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500"
                                  placeholder="Tambahkan catatan internal..."></textarea>
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
