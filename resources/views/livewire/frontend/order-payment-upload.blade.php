@php
    $bankName = App\Helpers\WebsiteSettings::get('bank_name') ?: 'BCA (Bank Central Asia)';
    $bankNumber = App\Helpers\WebsiteSettings::get('bank_account_number') ?: '8910-2345-6789';
    $bankHolder = App\Helpers\WebsiteSettings::get('bank_account_holder') ?: 'CV BEWOLE JEPARA FURNITURE';
@endphp

<div class="@container rounded-3xl border border-wood-border/60 bg-white p-5 sm:p-7 shadow-sm space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-wood-border/40 pb-4">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-wood-text flex items-center gap-2">
                <svg class="h-5 w-5 text-wood-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span>Informasi & Bukti Pembayaran</span>
            </h2>
            <div class="mt-1 flex items-center gap-2 text-xs text-wood-muted flex-wrap">
                <span>Status Pembayaran:</span>
                @if ($order->payment_status === 'paid')
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        {{ $order->payment_status_label }}
                    </span>
                @elseif ($order->payment_status === 'down_payment')
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                        {{ $order->payment_status_label }}
                    </span>
                @elseif ($order->payment_status === 'unpaid')
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                        {{ $order->payment_status_label }}
                    </span>
                @elseif ($order->payment_status === 'failed')
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                        {{ $order->payment_status_label }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold bg-gray-50 text-gray-700 border border-gray-200">
                        {{ $order->payment_status_label }}
                    </span>
                @endif
            </div>
        </div>

        @if (!$is_final_payment && in_array($order->payment_status, ['unpaid', 'failed']))
            <button
                type="button"
                wire:click="toggleForm"
                class="self-start sm:self-auto rounded-xl border border-wood-primary/40 bg-wood-light/30 px-3.5 py-2 text-xs font-bold text-wood-primary hover:bg-wood-primary hover:text-white transition-all shrink-0"
            >
                {{ $showForm ? 'Tutup Form' : ($order->has_payment_proof ? 'Ganti / Upload Ulang' : 'Upload Bukti Pembayaran') }}
            </button>
        @elseif ($order->payment_status === 'down_payment' && !$order->has_final_payment_proof)
            <button
                type="button"
                wire:click="toggleForm"
                class="self-start sm:self-auto rounded-xl bg-wood-primary px-4 py-2 text-xs font-bold text-white hover:bg-wood-primary-dark transition-all shadow-sm flex items-center gap-1.5 shrink-0"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                {{ $showForm ? 'Tutup Form' : 'Upload Bukti Pelunasan' }}
            </button>
        @endif
    </div>

    {{-- Rejection Reason Alert if failed --}}
    @if ($order->payment_status === 'failed' && $order->payment_rejection_reason)
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800">
            <div class="flex items-start gap-2.5">
                <svg class="h-5 w-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <span class="font-bold block mb-1">Bukti Transfer Ditolak Admin:</span>
                    <p class="leading-relaxed">{{ $order->payment_rejection_reason }}</p>
                    <p class="mt-1.5 font-semibold text-rose-700">Silakan unggah foto bukti transfer yang valid melalui tombol di atas.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Payment Summary Grid --}}
    <div class="grid grid-cols-1 @[480px]:grid-cols-3 gap-3">
        {{-- Total Tagihan --}}
        <div class="rounded-2xl border border-wood-border/60 bg-wood-light/20 p-4 min-w-0 flex flex-col justify-between">
            <span class="text-[11px] font-bold text-wood-muted uppercase tracking-wider block">Total Tagihan</span>
            <span class="text-base sm:text-lg font-black text-wood-text mt-1.5 block whitespace-nowrap overflow-hidden text-ellipsis font-mono">
                {{ $order->formatted_total_price }}
            </span>
        </div>

        {{-- DP Masuk --}}
        <div class="rounded-2xl border border-amber-200/80 bg-amber-50/50 p-4 min-w-0 flex flex-col justify-between">
            <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wider block">DP (Uang Muka) Masuk</span>
            <span class="text-base sm:text-lg font-black text-amber-700 mt-1.5 block whitespace-nowrap overflow-hidden text-ellipsis font-mono">
                {{ $order->down_payment_amount > 0 ? $order->formatted_down_payment_amount : 'Rp 0' }}
            </span>
        </div>

        {{-- Sisa Pembayaran --}}
        <div class="rounded-2xl border {{ $order->remaining_payment > 0 ? 'border-rose-200/80 bg-rose-50/50' : 'border-emerald-200/80 bg-emerald-50/50' }} p-4 min-w-0 flex flex-col justify-between">
            <span class="text-[11px] font-bold {{ $order->remaining_payment > 0 ? 'text-rose-800' : 'text-emerald-800' }} uppercase tracking-wider block">Sisa Pembayaran</span>
            <span class="text-base sm:text-lg font-black {{ $order->remaining_payment > 0 ? 'text-rose-600' : 'text-emerald-600' }} mt-1.5 block whitespace-nowrap overflow-hidden text-ellipsis font-mono">
                {{ $order->payment_status === 'paid' ? 'Rp 0 (LUNAS)' : $order->formatted_remaining_payment }}
            </span>
        </div>
    </div>

    {{-- Bank Transfer Instructions Card --}}
    @if ($order->payment_status !== 'paid')
        <div class="rounded-2xl border border-amber-200/90 bg-amber-50/60 p-4 sm:p-5 space-y-3.5 text-xs" x-data="{ copied: false }">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span class="font-bold text-amber-900 uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Rekening Pembayaran Toko
                </span>
                <span class="text-[11px] text-amber-800 font-medium bg-amber-100/80 px-2.5 py-0.5 rounded-full border border-amber-200">
                    Bisa bayar DP (50%) atau Lunas
                </span>
            </div>

            <div class="rounded-xl border border-amber-200 bg-white p-3.5 sm:p-4 flex flex-col @[500px]:flex-row @[500px]:items-center justify-between gap-3 shadow-xs">
                <div>
                    <span class="text-xs font-extrabold text-gray-900 block">{{ $bankName }}</span>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-sm sm:text-base font-mono font-bold text-amber-900 tracking-wider">{{ $bankNumber }}</span>
                        <button
                            type="button"
                            @click="navigator.clipboard.writeText('{{ preg_replace('/[^0-9]/', '', $bankNumber) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-[11px] font-semibold text-amber-800 border border-amber-200 transition-colors cursor-pointer"
                        >
                            <span x-show="!copied">Salin No. Rek</span>
                            <span x-show="copied" x-cloak class="text-emerald-700 font-bold">✓ Tersalin</span>
                        </button>
                    </div>
                    <span class="text-[11px] text-gray-500 block mt-0.5">a.n. {{ $bankHolder }}</span>
                </div>
                <div class="@[500px]:text-right border-t @[500px]:border-t-0 pt-2.5 @[500px]:pt-0 border-amber-100">
                    <span class="text-[11px] text-gray-500 block">Nominal Transfer Sesuai Pesanan:</span>
                    <span class="text-sm sm:text-base font-extrabold text-amber-900 font-mono block mt-0.5">{{ $order->formatted_total_price }}</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Upload Form --}}
    @if ($showForm || ($order->payment_status === 'unpaid' && !$order->has_payment_proof))
        <form wire:submit="uploadPaymentProof" class="rounded-2xl border-2 border-dashed border-wood-primary/40 bg-wood-light/10 p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-wood-border/30 pb-3">
                <h3 class="text-sm font-bold text-wood-text">
                    {{ $is_final_payment || $order->payment_status === 'down_payment' ? 'Upload Bukti Pelunasan' : 'Upload Bukti Transfer Pembayaran' }}
                </h3>
                <span class="text-[11px] text-wood-muted">JPG, PNG, WEBP (Maks 5MB)</span>
            </div>

            {{-- File Input --}}
            <div>
                <label class="block text-xs font-semibold text-wood-text mb-2">Pilih File Foto Bukti Transfer <span class="text-rose-500">*</span></label>
                <input
                    type="file"
                    wire:model="proof"
                    accept="image/jpeg,image/png,image/jpg,image/webp"
                    class="w-full text-xs text-wood-text file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-wood-primary file:text-white hover:file:bg-wood-primary-dark file:cursor-pointer cursor-pointer border border-wood-border/60 rounded-xl p-2 bg-white"
                >
                @error('proof') <span class="mt-1.5 block text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>

            {{-- Image Preview --}}
            @if ($proof)
                <div class="space-y-2">
                    <span class="text-xs font-semibold text-wood-text block">Pratinjau Foto:</span>
                    <div class="relative w-48 h-48 rounded-xl overflow-hidden border border-wood-border/60 shadow-sm bg-wood-light/20">
                        <img src="{{ $proof->temporaryUrl() }}" alt="Preview Bukti" class="h-full w-full object-cover">
                    </div>
                </div>
            @endif

            {{-- Submit Button --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                @if ($showForm)
                    <button
                        type="button"
                        wire:click="toggleForm"
                        class="rounded-xl border border-wood-border/60 px-4 py-2 text-xs font-bold text-wood-muted hover:bg-wood-light/40 transition-colors"
                    >
                        Batal
                    </button>
                @endif

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="rounded-xl bg-wood-primary px-5 py-2.5 text-xs font-bold text-white hover:bg-wood-primary-dark transition-all shadow-md flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <svg wire:loading wire:target="uploadPaymentProof" class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Kirim Bukti Pembayaran</span>
                </button>
            </div>
        </form>
    @endif

    {{-- Uploaded Proof Previews / Archive --}}
    @if ($order->has_payment_proof || $order->has_final_payment_proof)
        <div class="pt-2 border-t border-wood-border/40 space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-wood-text flex items-center gap-2">
                <svg class="h-4 w-4 text-wood-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Arsip Bukti Transfer Terkirim
            </h3>

            <div class="grid grid-cols-1 @[480px]:grid-cols-2 gap-4">
                {{-- Initial / DP Proof --}}
                @if ($order->has_payment_proof)
                    <div class="rounded-2xl border border-wood-border/60 bg-wood-light/10 p-3.5 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-wood-text">
                                {{ $order->down_payment_amount > 0 ? 'Bukti Pembayaran DP' : 'Bukti Pembayaran Awal' }}
                            </span>
                            @if ($order->payment_proof_uploaded_at)
                                <span class="text-[11px] text-wood-muted">{{ $order->payment_proof_uploaded_at->format('d M Y, H:i') }}</span>
                            @endif
                        </div>
                        <a href="{{ $order->payment_proof_url }}" target="_blank" class="block aspect-video w-full rounded-xl overflow-hidden border border-wood-border/40 hover:opacity-90 transition-opacity bg-black/5">
                            <img src="{{ $order->payment_proof_url }}" alt="Bukti Pembayaran" class="h-full w-full object-cover">
                        </a>
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-wood-muted">Status:</span>
                            <span class="font-bold text-wood-text">{{ $order->payment_status_label }}</span>
                        </div>
                    </div>
                @endif

                {{-- Final Proof --}}
                @if ($order->has_final_payment_proof)
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/40 p-3.5 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-900">Bukti Pelunasan</span>
                            @if ($order->final_payment_proof_uploaded_at)
                                <span class="text-[11px] text-emerald-700">{{ $order->final_payment_proof_uploaded_at->format('d M Y, H:i') }}</span>
                            @endif
                        </div>
                        <a href="{{ $order->final_payment_proof_url }}" target="_blank" class="block aspect-video w-full rounded-xl overflow-hidden border border-emerald-200 hover:opacity-90 transition-opacity bg-black/5">
                            <img src="{{ $order->final_payment_proof_url }}" alt="Bukti Pelunasan" class="h-full w-full object-cover">
                        </a>
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-emerald-700">Status:</span>
                            <span class="font-bold text-emerald-900">{{ $order->payment_status_label }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

