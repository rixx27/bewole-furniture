<div>
    {{-- Toast Notification --}}
    <div x-data="{ show: false, type: 'success', message: '' }"
         x-on:settings-saved.window="show = true; type = $event.detail.type; message = $event.detail.message; setTimeout(() => show = false, 5000)"
         x-show="show"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed right-4 top-4 z-[9999] max-w-md"
         :class="{
             'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-950 dark:border-emerald-800 dark:text-emerald-300': type === 'success',
             'bg-red-50 border-red-200 text-red-700 dark:bg-red-950 dark:border-red-800 dark:text-red-300': type === 'error',
             'bg-blue-50 border-blue-200 text-blue-700 dark:bg-blue-950 dark:border-blue-800 dark:text-blue-300': type === 'info',
         }"
         role="alert">
        <div class="flex items-center gap-3 rounded-xl border p-4 shadow-lg">
            <template x-if="type === 'success'">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </template>
            <template x-if="type === 'error'">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </template>
            <template x-if="type === 'info'">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </template>
            <p class="text-sm font-medium" x-text="message"></p>
            <button x-on:click="show = false" class="shrink-0 opacity-60 hover:opacity-100">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-text-muted mb-3">
            <span class="text-text-secondary">Pengaturan Website</span>
            <span>/</span>
            <span class="font-medium text-primary uppercase tracking-wider text-[11px]">
                @if ($activeTab === 'info') Informasi Perusahaan
                @elseif ($activeTab === 'contact') Kontak & Sosial Media
                @elseif ($activeTab === 'system') Sistem
                @endif
            </span>
        </div>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-black">Pengaturan Website</h2>
                <p class="mt-1 text-sm text-text-secondary">Kelola identitas, kontak, lokasi Google Maps, dan sistem global.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button"
                        wire:click="resetForm"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset Form
                </button>
            </div>
        </div>
    </div>

    {{-- No Settings Yet --}}
    @if (!$settings)
        <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-border bg-card p-12 shadow-sm">
            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-primary/10">
                <svg class="h-10 w-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-text-primary dark:text-black">Belum Ada Pengaturan</h3>
            <p class="mt-1 mb-6 text-sm text-text-muted">Anda belum membuat pengaturan website. Klik tombol di bawah untuk memulai.</p>
            <button type="button"
                    wire:click="createSettings"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 text-sm font-medium text-white transition-all hover:bg-primary-dark shadow-sm cursor-pointer">
                <svg wire:loading.remove wire:target="createSettings" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <svg wire:loading wire:target="createSettings" class="h-5 w-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Buat Pengaturan Website
            </button>
        </div>
    @else
        {{-- Section Navigation Tabs --}}
        <div class="mb-6 border-b border-border">
            <nav class="-mb-px flex space-x-2 overflow-x-auto sm:space-x-4" aria-label="Tabs">
                <button type="button"
                        wire:click="setTab('info')"
                        @class([
                            'flex items-center gap-2 border-b-2 py-3 px-4 text-sm font-medium whitespace-nowrap transition-colors cursor-pointer',
                            'border-primary text-primary font-semibold' => $activeTab === 'info',
                            'border-transparent text-text-secondary hover:border-border hover:text-text-primary' => $activeTab !== 'info',
                        ])>
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9"/>
                    </svg>
                    <span>Informasi Perusahaan</span>
                </button>

                <button type="button"
                        wire:click="setTab('contact')"
                        @class([
                            'flex items-center gap-2 border-b-2 py-3 px-4 text-sm font-medium whitespace-nowrap transition-colors cursor-pointer',
                            'border-primary text-primary font-semibold' => $activeTab === 'contact',
                            'border-transparent text-text-secondary hover:border-border hover:text-text-primary' => $activeTab !== 'contact',
                        ])>
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span>Kontak & Sosial Media</span>
                </button>


                <button type="button"
                        wire:click="setTab('system')"
                        @class([
                            'flex items-center gap-2 border-b-2 py-3 px-4 text-sm font-medium whitespace-nowrap transition-colors cursor-pointer',
                            'border-primary text-primary font-semibold' => $activeTab === 'system',
                            'border-transparent text-text-secondary hover:border-border hover:text-text-primary' => $activeTab !== 'system',
                        ])>
                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Sistem</span>
                </button>
            </nav>
        </div>

        {{-- Settings Form --}}
        <form wire:submit="save" class="space-y-6">

            {{-- ============================================ --}}
            {{-- TAB A: INFORMASI PERUSAHAAN --}}
            {{-- ============================================ --}}
            @if ($activeTab === 'info')
                <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                    <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-text-primary dark:text-black">Informasi Perusahaan</h3>
                                <p class="text-xs text-text-muted">Nama, tagline, alamat, dan jam kerja perusahaan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                            {{-- Nama Website --}}
                            <div>
                                <label for="site_name" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                    Nama Perusahaan / Website
                                </label>
                                <input type="text"
                                       id="site_name"
                                       wire:model="site_name"
                                       placeholder="Bewole Furniture"
                                       class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                                @error('site_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            {{-- Tagline --}}
                            <div>
                                <label for="site_tagline" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                    Tagline
                                </label>
                                <input type="text"
                                       id="site_tagline"
                                       wire:model="site_tagline"
                                       placeholder="Furniture Kualitas Terbaik"
                                       class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                                @error('site_tagline') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div>
                            <label for="address" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                Alamat Lengkap Perusahaan
                            </label>
                            <textarea id="address"
                                      wire:model="address"
                                      rows="3"
                                      placeholder="Jl. Raya Jepara - Kudus KM 10, Tahunan, Jepara, Jawa Tengah"
                                      class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary resize-none"></textarea>
                            @error('address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Jam & Hari Operasional --}}
                        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                            <div>
                                <label for="working_days" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                    Hari Operasional
                                </label>
                                <input type="text"
                                       id="working_days"
                                       wire:model="working_days"
                                       placeholder="Senin - Sabtu"
                                       class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                                @error('working_days') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="working_hours" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                    Jam Operasional
                                </label>
                                <input type="text"
                                       id="working_hours"
                                       wire:model="working_hours"
                                       placeholder="08:00 - 17:00 WIB"
                                       class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                                @error('working_hours') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Rekening Pembayaran Toko --}}
                <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                    <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-500/10 text-amber-700 dark:text-amber-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-6 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-text-primary dark:text-black">Rekening Pembayaran Toko</h3>
                                <p class="text-xs text-text-muted">Nomor rekening dan pilihan bank yang otomatis ditampilkan pada kartu rekening menu Informasi & Bukti Pembayaran (Tracking).</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                            {{-- Pilihan Bank --}}
                            <div>
                                <label for="bank_name" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                    Pilihan Bank / Nama Bank <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="bank_name"
                                       list="bank_options"
                                       wire:model.live="bank_name"
                                       placeholder="Pilih atau ketik bank (misal: BCA)"
                                       class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                                <datalist id="bank_options">
                                    <option value="BCA (Bank Central Asia)"></option>
                                    <option value="Mandiri (Bank Mandiri)"></option>
                                    <option value="BRI (Bank Rakyat Indonesia)"></option>
                                    <option value="BNI (Bank Negara Indonesia)"></option>
                                    <option value="BSI (Bank Syariah Indonesia)"></option>
                                    <option value="Bank Jateng"></option>
                                    <option value="CIMB Niaga"></option>
                                    <option value="Permata Bank"></option>
                                    <option value="Bank Danamon"></option>
                                </datalist>
                                @error('bank_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror

                                {{-- Quick Bank Select Buttons --}}
                                <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                    <span class="text-[11px] text-text-muted">Pilih cepat:</span>
                                    @foreach (['BCA (Bank Central Asia)', 'Mandiri (Bank Mandiri)', 'BRI (Bank Rakyat Indonesia)', 'BNI (Bank Negara Indonesia)', 'BSI (Bank Syariah Indonesia)', 'Bank Jateng'] as $b)
                                        <button
                                            type="button"
                                            wire:click="$set('bank_name', '{{ $b }}')"
                                            class="rounded-md border border-border bg-bg-secondary/40 px-2 py-0.5 text-[10px] font-semibold text-text-secondary hover:border-primary hover:text-primary transition-colors cursor-pointer"
                                        >
                                            {{ explode(' ', $b)[0] }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Nomor Rekening --}}
                            <div>
                                <label for="bank_account_number" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                    Nomor Rekening <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="bank_account_number"
                                       wire:model.live="bank_account_number"
                                       placeholder="8910-2345-6789"
                                       class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-mono text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                                @error('bank_account_number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                <p class="mt-1 text-[11px] text-text-muted">Nomor rekening transfer yang dapat disalin langsung oleh pembeli.</p>
                            </div>

                            {{-- Nama Pemilik Rekening --}}
                            <div>
                                <label for="bank_account_holder" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                    Nama Pemilik Rekening (a.n.) <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="bank_account_holder"
                                       wire:model.live="bank_account_holder"
                                       placeholder="CV BEWOLE JEPARA FURNITURE"
                                       class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                                @error('bank_account_holder') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                <p class="mt-1 text-[11px] text-text-muted">Nama resmi pemilik rekening sesuai buku tabungan.</p>
                            </div>
                        </div>

                        {{-- Preview Card seperti di Halaman Pembeli --}}
                        <div class="rounded-xl border border-amber-200/90 bg-amber-50/50 p-4 space-y-2">
                            <span class="text-[11px] font-bold text-amber-900 uppercase tracking-wider block">
                                Pratinjau Tampilan Rekening Pembayaran Toko (di Sisi Pembeli):
                            </span>
                            <div class="rounded-lg border border-amber-200 bg-white p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                                <div>
                                    <span class="text-xs font-extrabold text-gray-900 block">{{ $bank_name ?: 'BCA (Bank Central Asia)' }}</span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-sm font-mono font-bold text-amber-900 tracking-wider">{{ $bank_account_number ?: '8910-2345-6789' }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-amber-50 text-[10px] font-semibold text-amber-800 border border-amber-200">
                                            Salin No. Rek
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-gray-500 block mt-0.5">a.n. {{ $bank_account_holder ?: 'CV BEWOLE JEPARA FURNITURE' }}</span>
                                </div>
                                <div class="text-left sm:text-right">
                                    <span class="text-[10px] text-emerald-700 font-semibold bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200 inline-block">
                                        ✓ Otomatis Tersinkron ke Menu Pembayaran Pelanggan
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Logo Website --}}
                <div x-data="{ logoLocalPreview: null }" class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                    <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-text-primary dark:text-black">Logo Website &amp; Favicon</h3>
                                <p class="text-xs text-text-muted">Logo utama yang ditampilkan pada navbar dan footer website, serta otomatis disinkronkan sebagai favicon browser.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row items-start gap-6">
                            <div class="relative h-24 w-24 shrink-0 overflow-hidden rounded-xl border border-border bg-bg-secondary/50 flex items-center justify-center shadow-xs">
                                <template x-if="logoLocalPreview">
                                    <img :src="logoLocalPreview" alt="Preview Logo" class="h-full w-full object-contain p-2">
                                </template>
                                <template x-if="!logoLocalPreview">
                                    <div class="h-full w-full flex items-center justify-center">
                                        @if ($logo_preview)
                                            <img src="{{ $logo_preview }}" alt="Preview Logo" class="h-full w-full object-contain p-2">
                                        @elseif ($existing_logo)
                                            <img src="{{ asset('storage/' . $existing_logo) }}" alt="Logo" class="h-full w-full object-contain p-2">
                                        @else
                                            <span class="text-xs text-text-muted">No Logo</span>
                                        @endif
                                    </div>
                                </template>
                                <button type="button"
                                        x-show="logoLocalPreview || @js(boolval($existing_logo || $logo_preview))"
                                        @click="logoLocalPreview = null; if ($refs.logoInput) $refs.logoInput.value = '';"
                                        wire:click="removeLogo"
                                        class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-full bg-black/70 text-white hover:bg-red-600 transition-colors cursor-pointer"
                                        title="Hapus Logo">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="flex-1">
                                <label for="logo_input" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">Unggah Logo &amp; Favicon</label>
                                <input type="file"
                                       id="logo_input"
                                       x-ref="logoInput"
                                       accept="image/jpg,image/jpeg,image/png,image/svg+xml,image/webp"
                                       x-on:change="logoLocalPreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                                       wire:model="logo"
                                       class="block w-full text-sm text-text-secondary file:mr-4 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-dark transition-all cursor-pointer">
                                <p class="mt-2 text-xs text-text-muted">Format: PNG, JPG, JPEG, SVG, WEBP. Maksimal 2 MB. Logo otomatis disinkronkan menjadi favicon browser.</p>
                                @error('logo') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                                <div wire:loading wire:target="logo" class="mt-2 text-xs text-primary font-medium">Mengunggah logo...</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Foto Card Custom Furniture --}}
                <div x-data="{ customLocalPreview: null }" class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                    <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-text-primary dark:text-black">Foto Card Custom Furniture (Halaman Home)</h3>
                                <p class="text-xs text-text-muted">Kelola foto furniture yang tampil pada card section "Custom Furniture" (Punya desain furniture sendiri?) di beranda.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row items-start gap-6">
                            {{-- Preview Box --}}
                            <div class="relative w-48 h-36 shrink-0 overflow-hidden rounded-2xl border-2 border-dashed border-border bg-bg-secondary/50 flex items-center justify-center shadow-xs">
                                <template x-if="customLocalPreview">
                                    <div class="h-full w-full">
                                        <img :src="customLocalPreview" alt="Preview Baru" class="h-full w-full object-cover">
                                        <button type="button"
                                                @click="customLocalPreview = null; if ($refs.customFileInput) $refs.customFileInput.value = '';"
                                                wire:click="removeCustomFurnitureImage"
                                                class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-black/70 text-white hover:bg-red-600 transition-colors shadow-sm cursor-pointer z-10"
                                                title="Batalkan Pilihan">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                        <span class="absolute bottom-1.5 left-2 rounded-md bg-black/60 px-2 py-0.5 text-[10px] font-medium text-white backdrop-blur-xs">Preview Baru</span>
                                    </div>
                                </template>
                                <template x-if="!customLocalPreview">
                                    <div class="h-full w-full flex items-center justify-center">
                                        @if ($custom_furniture_image_preview)
                                            <img src="{{ $custom_furniture_image_preview }}" alt="Preview Custom Furniture" class="h-full w-full object-cover">
                                            <button type="button"
                                                    wire:click="removeCustomFurnitureImage"
                                                    class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-black/70 text-white hover:bg-red-600 transition-colors shadow-sm cursor-pointer"
                                                    title="Batalkan Pilihan">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                            <span class="absolute bottom-1.5 left-2 rounded-md bg-black/60 px-2 py-0.5 text-[10px] font-medium text-white backdrop-blur-xs">Preview Baru</span>
                                        @elseif ($existing_custom_furniture_image)
                                            <img src="{{ asset('storage/' . $existing_custom_furniture_image) }}" alt="Custom Furniture" class="h-full w-full object-cover">
                                            <button type="button"
                                                    wire:click="removeCustomFurnitureImage"
                                                    class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-black/70 text-white hover:bg-red-600 transition-colors shadow-sm cursor-pointer"
                                                    title="Hapus foto custom">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                            <span class="absolute bottom-1.5 left-2 rounded-md bg-emerald-600/80 px-2 py-0.5 text-[10px] font-medium text-white backdrop-blur-xs">Foto Khusus Aktif</span>
                                        @else
                                            <div class="flex flex-col items-center justify-center p-3 text-center text-text-muted">
                                                <svg class="h-8 w-8 mb-1 text-text-muted/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                                </svg>
                                                <span class="text-[11px] font-semibold text-text-secondary">Foto Default</span>
                                                <span class="text-[10px] text-text-muted mt-0.5 leading-tight">Mengikuti produk terbaru</span>
                                            </div>
                                        @endif
                                    </div>
                                </template>
                            </div>

                            {{-- Upload Input --}}
                            <div class="flex-1 space-y-3">
                                <div>
                                    <label for="custom_furniture_image_input" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                        Ganti Foto Card
                                    </label>
                                    <input type="file"
                                           id="custom_furniture_image_input"
                                           x-ref="customFileInput"
                                           accept="image/jpeg,image/png,image/webp"
                                           x-on:change="customLocalPreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                                           wire:model="custom_furniture_image"
                                           class="block w-full text-sm text-text-secondary file:mr-4 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-dark transition-all cursor-pointer">
                                    <p class="mt-2 text-xs text-text-muted">
                                        Format: JPG, JPEG, PNG, atau WEBP. Rekomendasi rasio 4:3 (Maksimal 5 MB).
                                    </p>
                                    @error('custom_furniture_image')
                                        <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                                    @enderror
                                    <div wire:loading wire:target="custom_furniture_image" class="mt-2 flex items-center gap-2 text-xs text-primary font-medium">
                                        <svg class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Mengunggah foto...
                                    </div>
                                </div>

                                @if ($existing_custom_furniture_image || $custom_furniture_image_preview)
                                    <div class="pt-1">
                                        <button type="button"
                                                @click="customLocalPreview = null; if ($refs.customFileInput) $refs.customFileInput.value = '';"
                                                wire:click="removeCustomFurnitureImage"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50/70 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 dark:border-red-900/50 dark:bg-red-950/30 dark:text-red-400 transition-colors cursor-pointer">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <span>Kembalikan ke Default (Gunakan Foto Produk Terbaru)</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ============================================ --}}
            {{-- TAB B: KONTAK & SOSIAL MEDIA --}}
            {{-- ============================================ --}}
            @if ($activeTab === 'contact')
                <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                    <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-text-primary dark:text-black">Kontak &amp; Sosial Media</h3>
                                <p class="text-xs text-text-muted">Informasi komunikasi, tautan media sosial, dan peta Google Maps.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        {{-- Field Kontak --}}
                        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                            <div>
                                <label for="email" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                    Email
                                </label>
                                <input type="email"
                                       id="email"
                                       wire:model="email"
                                       placeholder="info@bewolefurniture.com"
                                       class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                                @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="phone" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                    Nomor Telepon
                                </label>
                                <input type="text"
                                       id="phone"
                                       wire:model="phone"
                                       placeholder="(0291) 1234 5678"
                                       class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                                @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="whatsapp" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                    WhatsApp
                                </label>
                                <input type="text"
                                       id="whatsapp"
                                       wire:model="whatsapp"
                                       placeholder="6281234567890"
                                       class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                                @error('whatsapp') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                <p class="mt-1 text-xs text-text-muted">Gunakan format internasional (contoh: 6281234567890).</p>
                            </div>
                        </div>

                        {{-- Media Sosial --}}
                        <div class="pt-4 border-t border-border">
                            <h4 class="mb-4 text-xs font-bold uppercase tracking-wider text-text-muted">Media Sosial</h4>
                            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                                <div>
                                    <label for="facebook" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                        Facebook
                                    </label>
                                    <input type="url"
                                           id="facebook"
                                           wire:model="facebook"
                                           placeholder="https://facebook.com/bewolefurniture"
                                           class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                                    @error('facebook') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="instagram" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                        Instagram
                                    </label>
                                    <input type="url"
                                           id="instagram"
                                           wire:model="instagram"
                                           placeholder="https://instagram.com/bewolefurniture"
                                           class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                                    @error('instagram') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="tiktok" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                        TikTok
                                    </label>
                                    <input type="url"
                                           id="tiktok"
                                           wire:model="tiktok"
                                           placeholder="https://tiktok.com/@bewolefurniture"
                                           class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                                    @error('tiktok') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Google Maps Embed URL --}}
                        <div class="pt-4 border-t border-border">
                            <label for="google_maps_embed" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                Google Maps Embed URL
                            </label>
                            <p class="mb-2 text-xs text-text-muted">
                                Masukkan URL embed Google Maps untuk menampilkan lokasi perusahaan pada halaman Tentang Kami.
                            </p>
                            <textarea id="google_maps_embed"
                                      wire:model="google_maps_embed"
                                      rows="3"
                                      placeholder="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.4... atau <iframe src='...'></iframe>"
                                      class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary resize-none font-mono text-xs"></textarea>
                            @error('google_maps_embed') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror

                            {{-- Preview Box --}}
                            @php
                                $cleanEmbedUrl = App\Helpers\WebsiteSettings::googleMapsEmbedUrl();
                            @endphp
                            <div class="mt-4">
                                <span class="block text-xs font-semibold uppercase tracking-wider text-text-muted mb-2">Pratinjau Peta:</span>
                                @if ($google_maps_embed)
                                    <div class="overflow-hidden rounded-xl border border-border bg-bg-secondary p-2 shadow-xs">
                                        <div class="aspect-[16/9] w-full overflow-hidden rounded-lg bg-gray-100">
                                            @php
                                                $previewUrl = trim($google_maps_embed);
                                                if (preg_match('/src=["\']([^"\']+)["\']/', $previewUrl, $matches)) {
                                                    $previewUrl = $matches[1];
                                                }
                                            @endphp
                                            <iframe src="{{ $previewUrl }}"
                                                    width="100%"
                                                    height="100%"
                                                    style="border:0;"
                                                    allowfullscreen=""
                                                    loading="lazy"
                                                    referrerpolicy="no-referrer-when-downgrade"
                                                    class="h-full w-full rounded-lg">
                                            </iframe>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center rounded-xl border border-dashed border-border bg-bg-secondary p-6 text-center text-xs text-text-muted">
                                        Google Maps Embed URL belum diisi. Pratinjau akan tampil di sini setelah URL dimasukkan.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif



            {{-- ============================================ --}}
            {{-- TAB D: SISTEM --}}
            {{-- ============================================ --}}
            @if ($activeTab === 'system')
                <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                    <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg" :class="is_maintenance ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600'">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17l-4.49 2.59m0-8.64l4.49 2.59m-4.49 2.6v2.59c0 .51.27.98.72 1.23l3.96 2.29c.45.26.99.26 1.44 0l3.96-2.29c.45-.26.72-.72.72-1.23v-2.59m0-5.18v-2.59c0-.51-.27-.98-.72-1.23l-3.96-2.29a1.414 1.414 0 00-1.44 0L7.23 3.99a1.458 1.458 0 00-.72 1.23v2.59m0 5.18c0 .51.27.98.72 1.23l3.96 2.29c.45.26.99.26 1.44 0"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-text-primary dark:text-black">Konfigurasi Sistem</h3>
                                <p class="text-xs text-text-muted">Pengaturan mode pemeliharaan (maintenance mode) dan status sistem global.</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between rounded-xl border border-border bg-bg-secondary/50 p-5">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl" :class="is_maintenance ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600'">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-text-primary dark:text-black">Maintenance Mode Website</p>
                                    <p class="text-xs text-text-muted">Saat aktif, pengunjung non-admin akan diarahkan ke halaman pemeliharaan.</p>
                                </div>
                            </div>
                            <button type="button"
                                    wire:click="$toggle('is_maintenance')"
                                    class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden"
                                    :class="is_maintenance ? 'bg-amber-500' : 'bg-gray-300 dark:bg-gray-600'"
                                    role="switch"
                                    :aria-checked="is_maintenance">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
                                      :class="is_maintenance ? 'translate-x-5' : 'translate-x-0'"></span>
                            </button>
                        </div>

                        <div x-show="$wire.is_maintenance" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="mt-5">
                            <label for="maintenance_message" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                Pesan Pemeliharaan (Maintenance Message)
                            </label>
                            <textarea id="maintenance_message"
                                      wire:model="maintenance_message"
                                      rows="4"
                                      placeholder="Maaf, website sedang dalam masa pemeliharaan. Silakan kembali lagi nanti."
                                      class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary resize-none"></textarea>
                            @error('maintenance_message') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-text-muted">Pesan ini tetap tersimpan meskipun maintenance mode sedang nonaktif.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ============================================ --}}
            {{-- STICKY SAVE BUTTON --}}
            {{-- ============================================ --}}
            <div class="sticky bottom-0 z-10 -mx-6 -mb-6 mt-8 border-t border-border bg-card/95 backdrop-blur-sm px-6 py-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-text-muted">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Perubahan akan langsung disimpan ke database.
                        </span>
                    </p>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-2.5 text-sm font-medium text-white transition-all hover:bg-primary-dark shadow-sm disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                        {{-- Loading Spinner --}}
                        <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <svg wire:loading.remove wire:target="save" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </form>
    @endif
</div>
