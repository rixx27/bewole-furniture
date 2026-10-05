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
            <div class="relative flex flex-col w-full max-w-2xl max-h-[92vh] rounded-2xl bg-white shadow-2xl border border-gray-200 text-gray-900 overflow-hidden z-10"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                
                {{-- Header (Sticky) --}}
                <div class="flex items-start justify-between border-b border-gray-200 p-5 shrink-0 bg-white">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Kelola Pembayaran & Termin DP</h3>
                        <p class="mt-1 text-sm font-semibold text-gray-600">
                            #{{ $order->order_code }} — <span class="text-amber-800">{{ $order->customer_name }}</span>
                        </p>
                    </div>
                    <button wire:click="$dispatch('closeModal')" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors cursor-pointer">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Scrollable Body --}}
                <div class="flex-1 overflow-y-auto p-5 space-y-6">

                    {{-- Status Saat Ini & Summary --}}
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-xs space-y-2.5 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-600">Status Pembayaran Saat Ini:</span>
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-0.5 text-xs font-bold {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($order->payment_status === 'down_payment' ? 'bg-indigo-100 text-indigo-800 border border-indigo-300' : 'bg-amber-100 text-amber-800 border border-amber-300') }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $order->payment_status === 'paid' ? 'bg-emerald-500' : ($order->payment_status === 'down_payment' ? 'bg-indigo-500' : 'bg-amber-500') }}"></span>
                                {{ $order->payment_status_label }}
                            </span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 border-t border-gray-200 pt-3">
                            <div>
                                <span class="text-[11px] font-bold text-gray-500 uppercase block">Total Tagihan</span>
                                <span class="font-black text-gray-900 text-sm sm:text-base font-mono block mt-0.5">{{ $order->formatted_total_price }}</span>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-amber-800 uppercase block">Total DP/Termin Masuk</span>
                                <span class="font-black text-amber-700 text-sm sm:text-base font-mono block mt-0.5">{{ $order->formatted_total_verified_payment }}</span>
                            </div>
                            <div>
                                <span class="text-[11px] font-bold {{ $order->remaining_payment > 0 ? 'text-rose-700' : 'text-emerald-700' }} uppercase block">Sisa Tagihan</span>
                                <span class="font-black {{ $order->remaining_payment > 0 ? 'text-rose-600' : 'text-emerald-600' }} text-sm sm:text-base font-mono block mt-0.5">
                                    {{ $order->payment_status === 'paid' ? 'Rp 0 (LUNAS)' : $order->formatted_remaining_payment }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Multi-Stage Installments Section (History DP Bertahap) --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700 flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Riwayat Pembayaran DP & Termin</span>
                            </h4>
                            <button
                                type="button"
                                wire:click="toggleManualForm"
                                class="text-xs font-bold text-amber-800 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-3 py-1 rounded-lg transition-colors flex items-center gap-1 cursor-pointer"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>{{ $showManualForm ? 'Tutup Form Manual' : 'Catat Bayar Manual' }}</span>
                            </button>
                        </div>

                        {{-- Manual Payment Input Form --}}
                        @if ($showManualForm)
                            <div class="rounded-xl border border-amber-300 bg-amber-50/60 p-4 space-y-3 animate-fadeIn">
                                <h5 class="text-xs font-bold text-amber-900 flex items-center gap-1">
                                    <svg class="h-4 w-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Catat Pembayaran Manual (Tunai / WhatsApp Langsung)</span>
                                </h5>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                                            Nominal Pembayaran (Rp) <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            wire:model="manual_amount"
                                            placeholder="Contoh: 1.000.000"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-bold text-gray-900 focus:border-amber-600 focus:ring-1 focus:ring-amber-500"
                                        >
                                        @error('manual_amount') <span class="text-[11px] text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                                            Metode Pembayaran <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            wire:model="manual_method"
                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-900 focus:border-amber-600 focus:ring-1 focus:ring-amber-500"
                                        >
                                            <option value="cash">Tunai / Cash Langsung</option>
                                            <option value="bank_transfer">Transfer Bank (Direct WhatsApp)</option>
                                            <option value="qris">QRIS</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan Internal (Opsional)</label>
                                    <input
                                        type="text"
                                        wire:model="manual_notes"
                                        placeholder="Contoh: Pembayaran DP 50% via transfer langsung diterima admin"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 focus:border-amber-600 focus:ring-1 focus:ring-amber-500"
                                    >
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-1">
                                    <button
                                        type="button"
                                        wire:click="toggleManualForm"
                                        class="px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="saveManualPayment"
                                        class="px-4 py-1.5 rounded-lg bg-amber-700 hover:bg-amber-800 text-xs font-bold text-white transition-colors cursor-pointer shadow-xs"
                                    >
                                        Simpan Pembayaran
                                    </button>
                                </div>
                            </div>
                        @endif

                        {{-- Installment Items --}}
                        <div class="space-y-3">
                            @forelse ($payments as $payment)
                                <div class="rounded-xl border {{ $payment->status === 'verified' ? 'border-emerald-200 bg-emerald-50/20' : ($payment->status === 'rejected' ? 'border-rose-200 bg-rose-50/20' : 'border-amber-300 bg-amber-50/40 ring-1 ring-amber-400') }} p-3.5 space-y-2">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="inline-flex items-center justify-center h-5 px-2 rounded-md bg-gray-200 text-gray-800 font-bold text-[10px]">
                                                #{{ $payment->payment_number }}
                                            </span>
                                            <span class="text-xs sm:text-sm font-bold text-gray-900">
                                                {{ $payment->title ?: 'Pembayaran Ke-' . $payment->payment_number }}
                                            </span>

                                            {{-- Status Badge --}}
                                            @if ($payment->status === 'verified')
                                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                    <svg class="h-3 w-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Terverifikasi
                                                </span>
                                            @elseif ($payment->status === 'rejected')
                                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                                    <svg class="h-3 w-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    Ditolak
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                                                    Menunggu Verifikasi
                                                </span>
                                            @endif
                                        </div>

                                        <div class="text-left sm:text-right">
                                            <span class="text-[10px] font-semibold text-gray-500 uppercase block">Nominal</span>
                                            @if ($payment->status === 'verified')
                                                <span class="text-sm font-bold text-emerald-700 font-mono">{{ $payment->formatted_amount }}</span>
                                            @elseif ($payment->status === 'rejected')
                                                <span class="text-xs font-semibold text-rose-500 line-through">{{ $payment->formatted_amount }}</span>
                                            @else
                                                <span class="text-xs font-bold text-amber-800">Menunggu Input Admin</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between gap-3 text-[11px] text-gray-500 pt-1 border-t border-gray-100 flex-wrap">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span>Metode: <strong class="text-gray-700">{{ $payment->payment_method_label }}</strong></span>
                                            <span>•</span>
                                            <span>Upload: <strong class="text-gray-700">{{ $payment->created_at->format('d M Y, H:i') }}</strong></span>
                                            @if ($payment->verifiedBy)
                                                <span>•</span>
                                                <span>Oleh: <strong class="text-gray-700">{{ $payment->verifiedBy->name }}</strong></span>
                                            @endif
                                        </div>

                                        {{-- Proof button if present --}}
                                        @if ($payment->proof_file)
                                            <button
                                                type="button"
                                                wire:click="openPreview('{{ $payment->proof_url }}', '{{ $payment->title ?: 'Bukti Pembayaran #' . $payment->payment_number }}')"
                                                class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-800 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 px-2 py-0.5 rounded border border-amber-200 transition-colors cursor-pointer"
                                            >
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span>Lihat Struk Transfer</span>
                                            </button>
                                        @endif
                                    </div>

                                    @if ($payment->customer_notes)
                                        <div class="rounded-lg bg-gray-50 p-2 text-[11px] text-gray-700 italic border border-gray-200">
                                            <span class="font-semibold not-italic">Catatan Pembeli:</span> "{{ $payment->customer_notes }}"
                                        </div>
                                    @endif

                                    @if ($payment->admin_notes)
                                        <div class="rounded-lg bg-indigo-50/70 p-2 text-[11px] text-indigo-900 border border-indigo-200">
                                            <span class="font-semibold">Catatan Toko:</span> {{ $payment->admin_notes }}
                                        </div>
                                    @endif

                                    @if ($payment->status === 'rejected' && $payment->rejection_reason)
                                        <div class="rounded-lg bg-rose-50 p-2 text-[11px] text-rose-800 border border-rose-200">
                                            <span class="font-bold">Alasan Penolakan:</span> {{ $payment->rejection_reason }}
                                        </div>
                                    @endif

                                    {{-- Actions for Pending Installment --}}
                                    @if ($payment->status === 'pending')
                                        <div class="pt-2 border-t border-amber-200/80">
                                            @if ($verifyingPaymentId === $payment->id)
                                                {{-- Inline Verification Form --}}
                                                <div class="rounded-lg bg-white border border-emerald-300 p-3 space-y-2.5 shadow-xs">
                                                    <h6 class="text-xs font-bold text-emerald-900 flex items-center gap-1.5">
                                                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span>Verifikasi & Masukkan Nominal yang Diterima</span>
                                                    </h6>

                                                    <div class="space-y-1">
                                                        <label class="block text-[11px] font-semibold text-gray-700">
                                                            Nominal Transfer Masuk ke Rekening (Rp) <span class="text-red-500">*</span>
                                                        </label>
                                                        <div class="relative">
                                                            <span class="absolute left-3 top-2 text-xs font-bold text-gray-400">Rp</span>
                                                            <input
                                                                type="text"
                                                                wire:model="verify_amount"
                                                                class="w-full rounded-lg border border-gray-300 bg-white pl-9 pr-3 py-1.5 text-xs font-bold text-gray-900 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-500"
                                                                placeholder="Contoh: 1.170.000"
                                                            >
                                                        </div>
                                                        @error('verify_amount') <span class="text-[11px] text-red-600">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="space-y-1">
                                                        <label class="block text-[11px] font-semibold text-gray-700">Catatan Admin (Opsional)</label>
                                                        <input
                                                            type="text"
                                                            wire:model="verify_notes"
                                                            placeholder="Contoh: Transfer BCA mutasi sesuai"
                                                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-500"
                                                        >
                                                    </div>

                                                    <div class="flex items-center justify-end gap-2 pt-1">
                                                        <button
                                                            type="button"
                                                            wire:click="cancelVerifyInstallment"
                                                            class="px-2.5 py-1 text-xs font-semibold text-gray-600 hover:text-gray-900 transition-colors cursor-pointer"
                                                        >
                                                            Batal
                                                        </button>
                                                        <button
                                                            type="button"
                                                            wire:click="confirmVerifyInstallment"
                                                            class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white transition-colors cursor-pointer shadow-xs"
                                                        >
                                                            Konfirmasi & Setujui
                                                        </button>
                                                    </div>
                                                </div>
                                            @elseif ($rejectingPaymentId === $payment->id)
                                                {{-- Inline Rejection Form --}}
                                                <div class="rounded-lg bg-white border border-rose-300 p-3 space-y-2.5 shadow-xs">
                                                    <h6 class="text-xs font-bold text-rose-900 flex items-center gap-1.5">
                                                        <svg class="h-4 w-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                        </svg>
                                                        <span>Tolak Bukti Pembayaran</span>
                                                    </h6>

                                                    <div class="space-y-1">
                                                        <label class="block text-[11px] font-semibold text-gray-700">
                                                            Alasan Penolakan <span class="text-red-500">*</span>
                                                        </label>
                                                        <textarea
                                                            wire:model="reject_reason"
                                                            rows="2"
                                                            placeholder="Contoh: Bukti transfer buram / nominal mutasi tidak ditemukan..."
                                                            class="w-full rounded-lg border border-rose-300 bg-white p-2 text-xs text-gray-900 focus:border-rose-600 focus:ring-1 focus:ring-rose-500"
                                                        ></textarea>
                                                        @error('reject_reason') <span class="text-[11px] text-red-600">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div class="flex items-center justify-end gap-2 pt-1">
                                                        <button
                                                            type="button"
                                                            wire:click="cancelRejectInstallment"
                                                            class="px-2.5 py-1 text-xs font-semibold text-gray-600 hover:text-gray-900 transition-colors cursor-pointer"
                                                        >
                                                            Batal
                                                        </button>
                                                        <button
                                                            type="button"
                                                            wire:click="confirmRejectInstallment"
                                                            class="px-3.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-xs font-bold text-white transition-colors cursor-pointer shadow-xs"
                                                        >
                                                            Tolak Bukti
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex items-center justify-end gap-2">
                                                    <button
                                                        type="button"
                                                        wire:click="startRejectInstallment({{ $payment->id }})"
                                                        class="px-3 py-1 rounded-lg border border-rose-300 text-xs font-semibold text-rose-700 hover:bg-rose-50 transition-colors cursor-pointer"
                                                    >
                                                        Tolak
                                                    </button>
                                                    <button
                                                        type="button"
                                                        wire:click="startVerifyInstallment({{ $payment->id }})"
                                                        class="px-3.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-xs font-bold text-white transition-colors cursor-pointer shadow-xs flex items-center gap-1"
                                                    >
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        <span>Verifikasi Pembayaran</span>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-xs text-gray-500">
                                    Belum ada catatan termin pembayaran. Pelanggan dapat mengunggah bukti transfer, atau Anda dapat mencatat pembayaran manual melalui tombol di atas.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Accordion / Manual Status Override Section --}}
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-3" x-data="{ open: false }">
                        <button
                            type="button"
                            @click="open = !open"
                            class="w-full flex items-center justify-between text-xs font-bold text-gray-700 hover:text-gray-900 cursor-pointer"
                        >
                            <span class="flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Pengaturan Status Keseluruhan (Manual Override)</span>
                            </span>
                            <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" x-cloak class="pt-3 border-t border-gray-200 space-y-4">
                            <form id="payment-override-form" wire:submit="updatePayment">
                                <div class="mb-4">
                                    <label class="mb-2 block text-xs font-bold text-gray-900">Ubah Status Pembayaran Order</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
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
                                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border p-2.5 transition-all {{ $payment_status === $status->value ? 'border-amber-600 bg-amber-50 ring-2 ring-amber-500' : 'border-gray-200 bg-white hover:border-amber-300' }}">
                                                <input type="radio" wire:model.live="payment_status" name="payment_status" value="{{ $status->value }}" class="h-4 w-4 text-amber-700">
                                                <span class="text-xs font-semibold text-gray-900">{{ $status->label() }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('payment_status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                @if ($payment_status === 'down_payment')
                                    <div class="mb-4 space-y-1">
                                        <label for="down_payment_amount" class="block text-xs font-bold text-indigo-900">
                                            Nominal DP Masuk (Rp) <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            wire:model="down_payment_amount"
                                            id="down_payment_amount"
                                            class="w-full rounded-lg border border-indigo-300 bg-white px-3 py-2 text-xs font-bold text-gray-900"
                                            placeholder="2.000.000"
                                        >
                                        @error('down_payment_amount') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                @endif

                                @if ($payment_status === 'failed')
                                    <div class="mb-4 space-y-1">
                                        <label for="rejection_reason" class="block text-xs font-bold text-red-900">
                                            Alasan Penolakan
                                        </label>
                                        <textarea
                                            wire:model="rejection_reason"
                                            id="rejection_reason"
                                            rows="2"
                                            class="w-full rounded-lg border border-red-300 bg-white p-2 text-xs text-gray-900"
                                            placeholder="Alasan penolakan..."
                                        ></textarea>
                                        @error('rejection_reason') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                @endif

                                <div class="mb-4 space-y-1">
                                    <label for="notes" class="block text-xs font-bold text-gray-900">Catatan Internal</label>
                                    <textarea
                                        wire:model="notes"
                                        id="notes"
                                        rows="2"
                                        class="w-full rounded-lg border border-gray-300 bg-white p-2 text-xs text-gray-900"
                                        placeholder="Catatan..."
                                    ></textarea>
                                </div>

                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        class="px-4 py-2 rounded-lg bg-gray-800 hover:bg-gray-900 text-xs font-bold text-white transition-colors cursor-pointer"
                                    >
                                        Simpan Status Keseluruhan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Footer (Sticky) --}}
                <div class="flex items-center justify-between border-t border-gray-200 p-4 shrink-0 bg-gray-50">
                    <span class="text-xs text-gray-500 font-medium">Klik di luar atau tombol tutup untuk kembali.</span>
                    <button
                        type="button"
                        wire:click="$dispatch('closeModal')"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-100 transition-colors shadow-xs cursor-pointer"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Lightbox Modal for Proof Image Preview --}}
    @if ($previewImage)
        <div class="fixed inset-0 z-60 flex items-center justify-center p-4"
             x-data x-init="$el.style.display = 'flex'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="fixed inset-0 bg-black/75 backdrop-blur-xs" wire:click="closePreview"></div>

            <div class="relative max-w-2xl w-full bg-white rounded-2xl overflow-hidden shadow-2xl z-20 border border-gray-200">
                <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
                    <h4 class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ $previewTitle ?: 'Bukti Pembayaran' }}</h4>
                    <div class="flex items-center gap-2">
                        <a href="{{ $previewImage }}" target="_blank" download class="p-1.5 text-gray-500 hover:text-amber-800 hover:bg-white rounded-lg transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                        <button type="button" wire:click="closePreview" class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-white rounded-lg transition-colors cursor-pointer">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-4 bg-black/5 flex items-center justify-center max-h-[80vh] overflow-auto">
                    <img src="{{ $previewImage }}" alt="Foto Bukti Transfer" class="max-h-[75vh] w-auto object-contain rounded-lg shadow-sm">
                </div>
            </div>
        </div>
    @endif
</div>
