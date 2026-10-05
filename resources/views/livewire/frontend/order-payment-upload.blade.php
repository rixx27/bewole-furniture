@php
    $bankName = App\Helpers\WebsiteSettings::get('bank_name') ?: 'BCA (Bank Central Asia)';
    $bankNumber = App\Helpers\WebsiteSettings::get('bank_account_number') ?: '8910-2345-6789';
    $bankHolder = App\Helpers\WebsiteSettings::get('bank_account_holder') ?: 'CV BEWOLE JEPARA FURNITURE';
    $isFullyPaid = $order->payment_status === 'paid' || $order->remaining_payment <= 0;
    $hasPending = $payments->where('status', 'pending')->isNotEmpty();
@endphp

<div class="@container rounded-3xl border border-wood-border/60 bg-white p-5 sm:p-7 shadow-sm space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-wood-border/40 pb-4">
        <div>
            <h2 class="text-base sm:text-lg font-bold text-wood-text flex items-center gap-2">
                <svg class="h-5 w-5 text-wood-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span>Informasi & Riwayat Pembayaran (DP & Pelunasan)</span>
            </h2>
            <div class="mt-1 flex items-center gap-2 text-xs text-wood-muted flex-wrap">
                <span>Status Pembayaran:</span>
                @if ($order->payment_status === 'paid')
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Lunas
                    </span>
                @elseif ($order->payment_status === 'down_payment')
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                        DP (Uang Muka) Masuk
                    </span>
                @elseif ($order->payment_status === 'unpaid')
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                        Menunggu Pembayaran
                    </span>
                @elseif ($order->payment_status === 'failed')
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                        Bukti Ditolak
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-bold bg-gray-50 text-gray-700 border border-gray-200">
                        {{ $order->payment_status_label }}
                    </span>
                @endif

                @if ($payments->count() > 0)
                    <span class="text-wood-border">•</span>
                    <span class="font-medium text-wood-text">{{ $payments->count() }}x Pembayaran Tercatat</span>
                @endif
            </div>
        </div>

        {{-- Upload Action Button --}}
        @if (!$isFullyPaid)
            <button
                type="button"
                wire:click="toggleForm"
                class="self-start sm:self-auto rounded-xl {{ $showForm ? 'border border-wood-border/60 bg-wood-light/30 text-wood-muted' : 'bg-wood-primary text-white hover:bg-wood-primary-dark shadow-sm' }} px-4 py-2 text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 cursor-pointer"
            >
                @if ($showForm)
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>Tutup Form</span>
                @else
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Upload Bukti Pembayaran Ke-{{ $nextPaymentNumber }}</span>
                @endif
            </button>
        @else
            <div class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 px-3.5 py-2 text-xs font-bold text-emerald-800 border border-emerald-200">
                <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Pembayaran Telah Selesai (Lunas)</span>
            </div>
        @endif
    </div>

    {{-- Alert: Pending Verification --}}
    @if ($hasPending)
        <div class="rounded-2xl border border-amber-200 bg-amber-50/80 p-4 text-xs text-amber-900">
            <div class="flex items-start gap-2.5">
                <svg class="h-5 w-5 text-amber-600 shrink-0 mt-0.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <span class="font-bold block mb-0.5">Bukti Pembayaran Sedang Diverifikasi Admin</span>
                    <p class="leading-relaxed text-amber-800">
                        Admin kami sedang mencocokkan mutasi rekening bank dengan bukti transfer yang Anda unggah. Nominal pembayaran akan otomatis ditambahkan setelah disetujui.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Alert: Rejection Reason if any --}}
    @if ($order->payment_status === 'failed' && $order->payment_rejection_reason)
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800">
            <div class="flex items-start gap-2.5">
                <svg class="h-5 w-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <span class="font-bold block mb-1">Bukti Transfer Ditolak Admin:</span>
                    <p class="leading-relaxed">{{ $order->payment_rejection_reason }}</p>
                    <p class="mt-1.5 font-semibold text-rose-700">Silakan unggah foto bukti transfer yang valid melalui tombol upload di atas.</p>
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

        {{-- Total DP / Termin Masuk --}}
        <div class="rounded-2xl border border-amber-200/80 bg-amber-50/50 p-4 min-w-0 flex flex-col justify-between">
            <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wider block">DP / Termin Masuk</span>
            <span class="text-base sm:text-lg font-black text-amber-700 mt-1.5 block whitespace-nowrap overflow-hidden text-ellipsis font-mono">
                {{ $order->formatted_total_verified_payment }}
            </span>
        </div>

        {{-- Sisa Pembayaran --}}
        <div class="rounded-2xl border {{ $isFullyPaid ? 'border-emerald-200/80 bg-emerald-50/50' : 'border-rose-200/80 bg-rose-50/50' }} p-4 min-w-0 flex flex-col justify-between">
            <span class="text-[11px] font-bold {{ $isFullyPaid ? 'text-emerald-800' : 'text-rose-800' }} uppercase tracking-wider block">Sisa Pembayaran</span>
            <span class="text-base sm:text-lg font-black {{ $isFullyPaid ? 'text-emerald-600' : 'text-rose-600' }} mt-1.5 block whitespace-nowrap overflow-hidden text-ellipsis font-mono">
                {{ $isFullyPaid ? 'Rp 0 (LUNAS)' : $order->formatted_remaining_payment }}
            </span>
        </div>
    </div>

    {{-- Bank Transfer Instructions Card --}}
    @if (!$isFullyPaid)
        <div class="rounded-2xl border border-amber-200/90 bg-amber-50/60 p-4 sm:p-5 space-y-3.5 text-xs" x-data="{ copied: false }">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span class="font-bold text-amber-900 uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Rekening Pembayaran Toko
                </span>
                <span class="text-[11px] text-amber-800 font-medium bg-amber-100/80 px-2.5 py-0.5 rounded-full border border-amber-200">
                    Bisa bayar bertahap (DP Awal, Termin Progres, & Pelunasan)
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
                    <span class="text-[11px] text-gray-500 block">Sisa Tagihan Saat Ini:</span>
                    <span class="text-sm sm:text-base font-extrabold text-rose-700 font-mono block mt-0.5">{{ $order->formatted_remaining_payment }}</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Upload Form --}}
    @if ($showForm || ($payments->isEmpty() && !$order->has_payment_proof && !$isFullyPaid))
        <form wire:submit="uploadPaymentProof" class="rounded-2xl border-2 border-dashed border-wood-primary/40 bg-wood-light/10 p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-wood-border/30 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-wood-text flex items-center gap-2">
                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-wood-primary text-white text-xs font-bold">
                            {{ $nextPaymentNumber }}
                        </span>
                        <span>Upload Bukti Pembayaran Ke-{{ $nextPaymentNumber }}</span>
                    </h3>
                    <p class="text-[11px] text-wood-muted mt-0.5">
                        Cukup unggah foto bukti transfer. Nominal pembayaran akan diverifikasi dan diinput oleh admin toko.
                    </p>
                </div>
                <span class="text-[11px] text-wood-muted hidden sm:inline">JPG, PNG, WEBP (Maks 5MB)</span>
            </div>

            {{-- File Input --}}
            <div>
                <label class="block text-xs font-semibold text-wood-text mb-2">
                    Pilih File Foto Bukti Transfer <span class="text-rose-500">*</span>
                </label>
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
                    <span class="text-xs font-semibold text-wood-text block">Pratinjau Foto Bukti:</span>
                    <div class="relative w-48 h-48 rounded-xl overflow-hidden border border-wood-border/60 shadow-sm bg-wood-light/20">
                        <img src="{{ $proof->temporaryUrl() }}" alt="Preview Bukti" class="h-full w-full object-cover">
                    </div>
                </div>
            @endif

            {{-- Customer Notes --}}
            <div>
                <label class="block text-xs font-semibold text-wood-text mb-1">
                    Catatan Tambahan (Opsional)
                </label>
                <textarea
                    wire:model="customer_notes"
                    rows="2"
                    class="w-full rounded-xl border border-wood-border/60 bg-white p-3 text-xs text-wood-text placeholder-wood-muted/70 focus:border-wood-primary focus:outline-hidden focus:ring-1 focus:ring-wood-primary"
                    placeholder="Contoh: Transfer termin ke-2 via BCA atas nama Budi Santoso untuk tahap finishing..."
                ></textarea>
                @error('customer_notes') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
            </div>

            {{-- Submit Button --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                @if ($showForm)
                    <button
                        type="button"
                        wire:click="toggleForm"
                        class="rounded-xl border border-wood-border/60 px-4 py-2 text-xs font-bold text-wood-muted hover:bg-wood-light/40 transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                @endif

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="rounded-xl bg-wood-primary px-5 py-2.5 text-xs font-bold text-white hover:bg-wood-primary-dark transition-all shadow-md flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer"
                >
                    <svg wire:loading wire:target="uploadPaymentProof" class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Kirim Bukti Pembayaran Ke-{{ $nextPaymentNumber }}</span>
                </button>
            </div>
        </form>
    @endif

    {{-- History & Archive of Multi-Stage Payments (History DP & Termin) --}}
    @if ($payments->isNotEmpty() || $order->has_payment_proof || $order->has_final_payment_proof)
        <div class="pt-2 border-t border-wood-border/40 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-wood-text flex items-center gap-2">
                    <svg class="h-4 w-4 text-wood-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span>Riwayat Pembayaran (History DP Bertahap)</span>
                </h3>
                <span class="text-[11px] text-wood-muted">
                    Total Masuk: <strong class="text-amber-900 font-mono">{{ $order->formatted_total_verified_payment }}</strong>
                </span>
            </div>

            <div class="space-y-3">
                @forelse ($payments as $payment)
                    <div class="rounded-2xl border {{ $payment->status === 'verified' ? 'border-emerald-200 bg-emerald-50/20' : ($payment->status === 'rejected' ? 'border-rose-200 bg-rose-50/20' : 'border-amber-200/90 bg-amber-50/20') }} p-4 transition-all">
                        <div class="flex flex-col @[520px]:flex-row @[520px]:items-center justify-between gap-3">
                            {{-- Left side info --}}
                            <div class="space-y-1.5 flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center justify-center h-5 px-2 rounded-md bg-wood-primary/10 text-wood-primary font-bold text-[11px]">
                                        #{{ $payment->payment_number }}
                                    </span>
                                    <h4 class="text-xs sm:text-sm font-bold text-gray-900">
                                        {{ $payment->title ?: 'Pembayaran Ke-' . $payment->payment_number }}
                                    </h4>

                                    {{-- Status Badge --}}
                                    @if ($payment->status === 'verified')
                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            <svg class="h-3 w-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Terverifikasi Admin
                                        </span>
                                    @elseif ($payment->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                            <svg class="h-3 w-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                            <svg class="h-3 w-3 text-amber-600 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                            Menunggu Verifikasi
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3 text-[11px] text-gray-500 flex-wrap">
                                    <span>Metode: <strong class="text-gray-700">{{ $payment->payment_method_label }}</strong></span>
                                    <span>•</span>
                                    <span>Waktu Upload: <strong class="text-gray-700">{{ $payment->created_at->format('d M Y, H:i') }} WIB</strong></span>
                                    @if ($payment->verified_at && $payment->status === 'verified')
                                        <span>•</span>
                                        <span class="text-emerald-700">Diverifikasi: {{ $payment->verified_at->format('d M Y, H:i') }}</span>
                                    @endif
                                </div>

                                {{-- Customer Note --}}
                                @if ($payment->customer_notes)
                                    <div class="mt-1 rounded-lg bg-gray-50 border border-gray-200/80 px-2.5 py-1 text-[11px] text-gray-600 italic">
                                        <span class="font-semibold not-italic text-gray-700">Catatan Pembeli:</span> "{{ $payment->customer_notes }}"
                                    </div>
                                @endif

                                {{-- Admin Note --}}
                                @if ($payment->admin_notes)
                                    <div class="mt-1 rounded-lg bg-indigo-50/60 border border-indigo-200/80 px-2.5 py-1 text-[11px] text-indigo-800">
                                        <span class="font-semibold text-indigo-900">Catatan Toko:</span> {{ $payment->admin_notes }}
                                    </div>
                                @endif

                                {{-- Rejection Reason --}}
                                @if ($payment->status === 'rejected' && $payment->rejection_reason)
                                    <div class="mt-1 rounded-lg bg-rose-50 border border-rose-200 px-2.5 py-1 text-[11px] text-rose-800">
                                        <span class="font-bold text-rose-900">Alasan Penolakan:</span> {{ $payment->rejection_reason }}
                                    </div>
                                @endif
                            </div>

                            {{-- Right side: Amount & Proof preview --}}
                            <div class="flex items-center gap-3 shrink-0 self-end @[520px]:self-center">
                                <div class="text-right">
                                    <span class="text-[10px] uppercase font-bold text-gray-400 block">Nominal Masuk</span>
                                    @if ($payment->status === 'verified')
                                        <span class="text-sm sm:text-base font-extrabold text-emerald-700 font-mono block">
                                            {{ $payment->formatted_amount }}
                                        </span>
                                    @elseif ($payment->status === 'rejected')
                                        <span class="text-xs font-semibold text-rose-500 line-through block">
                                            {{ $payment->formatted_amount }}
                                        </span>
                                    @else
                                        <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200 block">
                                            Menunggu Admin
                                        </span>
                                    @endif
                                </div>

                                {{-- Proof Thumbnail --}}
                                @if ($payment->proof_file)
                                    <button
                                        type="button"
                                        wire:click="openImagePreview('{{ $payment->proof_url }}', '{{ $payment->title ?: 'Bukti Pembayaran #' . $payment->payment_number }}')"
                                        class="group relative h-14 w-14 rounded-xl overflow-hidden border border-gray-200 hover:border-wood-primary transition-all shadow-xs cursor-pointer shrink-0"
                                        title="Klik untuk melihat bukti transfer"
                                    >
                                        <img src="{{ $payment->proof_url }}" alt="Bukti Transfer" class="h-full w-full object-cover group-hover:scale-105 transition-transform">
                                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                                            </svg>
                                        </div>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Legacy Fallback if payments relation empty but legacy columns populated --}}
                    @if ($order->has_payment_proof)
                        <div class="rounded-2xl border border-wood-border/60 bg-wood-light/10 p-3.5 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-wood-text">Bukti Pembayaran DP Awal</span>
                                @if ($order->payment_proof_uploaded_at)
                                    <span class="text-[11px] text-wood-muted">{{ $order->payment_proof_uploaded_at->format('d M Y, H:i') }}</span>
                                @endif
                            </div>
                            <button
                                type="button"
                                wire:click="openImagePreview('{{ $order->payment_proof_url }}', 'Bukti Pembayaran DP')"
                                class="block aspect-video w-full rounded-xl overflow-hidden border border-wood-border/40 hover:opacity-90 transition-opacity bg-black/5 cursor-pointer"
                            >
                                <img src="{{ $order->payment_proof_url }}" alt="Bukti Pembayaran" class="h-full w-full object-cover">
                            </button>
                        </div>
                    @endif
                @endforelse
            </div>
        </div>
    @endif

    {{-- Lightbox Modal for Proof Image Preview --}}
    @if ($previewModalImage)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-init="$el.style.display = 'flex'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="fixed inset-0 bg-black/75 backdrop-blur-xs" wire:click="closeImagePreview"></div>

            <div class="relative max-w-2xl w-full bg-white rounded-2xl overflow-hidden shadow-2xl z-10 border border-gray-200">
                <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
                    <h4 class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ $previewModalTitle ?: 'Bukti Pembayaran' }}</h4>
                    <div class="flex items-center gap-2">
                        <a href="{{ $previewModalImage }}" target="_blank" download class="p-1.5 text-gray-500 hover:text-wood-primary hover:bg-white rounded-lg transition-colors" title="Buka di tab baru / unduh">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                        <button type="button" wire:click="closeImagePreview" class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-white rounded-lg transition-colors cursor-pointer">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-4 bg-black/5 flex items-center justify-center max-h-[80vh] overflow-auto">
                    <img src="{{ $previewModalImage }}" alt="Foto Bukti Transfer" class="max-h-[75vh] w-auto object-contain rounded-lg shadow-sm">
                </div>
            </div>
        </div>
    @endif
</div>
