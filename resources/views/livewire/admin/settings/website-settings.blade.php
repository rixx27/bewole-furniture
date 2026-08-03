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
        </div>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-black">Pengaturan Website</h2>
                <p class="mt-1 text-sm text-text-secondary">Atur identitas, kontak, media sosial, dan konfigurasi website Anda.</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button"
                        wire:click="resetForm"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
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
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 text-sm font-medium text-white transition-all hover:bg-primary-dark shadow-sm">
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
        {{-- Settings Form --}}
        <form wire:submit="save" class="space-y-6">
            {{-- ============================================ --}}
            {{-- SECTION 1: IDENTITAS WEBSITE --}}
            {{-- ============================================ --}}
            <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-text-primary dark:text-black">Identitas Website</h3>
                            <p class="text-xs text-text-muted">Informasi dasar tentang website Anda.</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                        {{-- Nama Website --}}
                        <div>
                            <label for="site_name" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                Nama Website
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

                    {{-- Logo --}}
                    <div class="mt-5">
                        <label class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Logo Website
                        </label>
                        <div class="flex items-center gap-4">
                            <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-border bg-bg-secondary">
                                @if ($logo_preview)
                                    <img src="{{ $logo_preview }}" alt="Logo Preview" class="h-full w-full object-contain">
                                @elseif ($existing_logo)
                                    <img src="{{ asset('storage/' . $existing_logo) }}" alt="Logo" class="h-full w-full object-contain">
                                @else
                                    <svg class="h-8 w-8 text-text-muted/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <label class="relative cursor-pointer rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                                    <span>Pilih Logo</span>
                                    <input type="file" wire:model="logo" accept="image/jpg,image/jpeg,image/png,image/svg+xml,image/webp" class="sr-only">
                                </label>
                                <p class="mt-1 text-xs text-text-muted">Logo juga digunakan sebagai favicon browser. JPG, PNG, SVG, WebP. Maks 2 MB.</p>
                                @if ($existing_logo || $logo_preview)
                                    <button type="button" wire:click="removeLogo" class="mt-1 text-xs text-red-500 hover:text-red-700 transition-colors">
                                        Hapus Logo
                                    </button>
                                @endif
                                @error('logo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 2: INFORMASI KONTAK --}}
            {{-- ============================================ --}}
            <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-text-primary dark:text-black">Informasi Kontak</h3>
                            <p class="text-xs text-text-muted">Informasi kontak yang ditampilkan ke pengunjung.</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
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
                                   placeholder="(021) 1234 5678"
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
                        <div>
                            <label for="address" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                                Alamat Lengkap
                            </label>
                            <textarea id="address"
                                      wire:model="address"
                                      rows="3"
                                      placeholder="Jl. Contoh No. 123, Kota, Provinsi"
                                      class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary resize-none"></textarea>
                            @error('address') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mt-5">
                        <label for="google_maps_embed" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Google Maps Embed
                        </label>
                        <textarea id="google_maps_embed"
                                  wire:model="google_maps_embed"
                                  rows="3"
                                  placeholder="<iframe src='...'></iframe>"
                                  class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary resize-none"></textarea>
                        @error('google_maps_embed') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 3: MEDIA SOSIAL --}}
            {{-- ============================================ --}}
            <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-text-primary dark:text-black">Media Sosial</h3>
                            <p class="text-xs text-text-muted">Tautan media sosial website Anda.</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
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
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 4: JAM OPERASIONAL --}}
            {{-- ============================================ --}}
            <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-text-primary dark:text-black">Jam Operasional</h3>
                            <p class="text-xs text-text-muted">Informasi jam kerja toko Anda.</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
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

            {{-- ============================================ --}}
            {{-- SECTION 5: MAINTENANCE MODE --}}
            {{-- ============================================ --}}
            <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg" :class="is_maintenance ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600'">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17l-4.49 2.59m0-8.64l4.49 2.59m-4.49 2.6v2.59c0 .51.27.98.72 1.23l3.96 2.29c.45.26.99.26 1.44 0l3.96-2.29c.45-.26.72-.72.72-1.23v-2.59m0-5.18v-2.59c0-.51-.27-.98-.72-1.23l-3.96-2.29a1.414 1.414 0 00-1.44 0L7.23 3.99a1.458 1.458 0 00-.72 1.23v2.59m0 5.18c0 .51.27.98.72 1.23l3.96 2.29c.45.26.99.26 1.44 0"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-text-primary dark:text-black">Maintenance Mode</h3>
                            <p class="text-xs text-text-muted">Aktifkan mode pemeliharaan website.</p>
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
                                <p class="text-sm font-semibold text-text-primary dark:text-black">Website Maintenance</p>
                                <p class="text-xs text-text-muted">Saat aktif, pengunjung akan melihat halaman maintenance.</p>
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
                            Pesan Maintenance
                        </label>
                        <textarea id="maintenance_message"
                                  wire:model="maintenance_message"
                                  rows="4"
                                  placeholder="Maaf, website sedang dalam masa pemeliharaan. Silakan kembali lagi nanti."
                                  class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary resize-none"></textarea>
                        @error('maintenance_message') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-text-muted">Pesan tetap tersimpan meskipun maintenance mode nonaktif.</p>
                    </div>
                </div>
            </div>

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
                            Perubahan akan langsung diterapkan.
                        </span>
                    </p>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-2.5 text-sm font-medium text-white transition-all hover:bg-primary-dark shadow-sm disabled:opacity-60 disabled:cursor-not-allowed">
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

