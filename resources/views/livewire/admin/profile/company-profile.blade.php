<div>
    {{-- Toast Notification --}}
    <div x-data="{ show: false, type: 'success', message: '' }"
         x-on:profile-saved.window="show = true; type = $event.detail.type; message = $event.detail.message; setTimeout(() => show = false, 5000)"
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
            <span class="text-text-secondary">Profil Perusahaan</span>
        </div>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-black">Profil Perusahaan</h2>
                <p class="mt-1 text-sm text-text-secondary">Kelola informasi tentang perusahaan, visi, misi, dan statistik.</p>
            </div>
            @if ($profile)
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
            @endif
        </div>
    </div>

    {{-- No Profile Yet --}}
    @if (!$profile)
        <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-border bg-card p-12 shadow-sm">
            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-primary/10">
                <svg class="h-10 w-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-text-primary dark:text-black">Belum Ada Profil</h3>
            <p class="mt-1 mb-6 text-sm text-text-muted">Anda belum membuat profil perusahaan. Klik tombol di bawah untuk memulai.</p>
            <button type="button"
                    wire:click="createProfile"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 text-sm font-medium text-white transition-all hover:bg-primary-dark shadow-sm">
                <svg wire:loading.remove wire:target="createProfile" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                <svg wire:loading wire:target="createProfile" class="h-5 w-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Buat Profil
            </button>
        </div>
    @else
        {{-- Profile Form --}}
        <form wire:submit="save" class="space-y-6">
            {{-- ============================================ --}}
            {{-- SECTION 1: TENTANG KAMI --}}
            {{-- ============================================ --}}
            <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-text-primary dark:text-black">Tentang Kami</h3>
                            <p class="text-xs text-text-muted">Cerita singkat tentang perusahaan Anda.</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <label for="about" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                        Tentang Kami <span class="text-red-500">*</span>
                    </label>
                    <textarea id="about"
                              wire:model="about"
                              rows="6"
                              placeholder="Tuliskan tentang perusahaan Anda..."
                              class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary resize-none"></textarea>
                    @error('about') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 2: VISI --}}
            {{-- ============================================ --}}
            <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-text-primary dark:text-black">Visi</h3>
                            <p class="text-xs text-text-muted">Visi perusahaan Anda.</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <label for="vision" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                        Visi <span class="text-red-500">*</span>
                    </label>
                    <textarea id="vision"
                              wire:model="vision"
                              rows="4"
                              placeholder="Contoh: Menjadi perusahaan furniture terbaik di Indonesia..."
                              class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary resize-none"></textarea>
                    @error('vision') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 3: MISI --}}
            {{-- ============================================ --}}
            <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-text-primary dark:text-black">Misi</h3>
                                <p class="text-xs text-text-muted">Daftar misi perusahaan. Tambah, edit, hapus, dan urutkan.</p>
                            </div>
                        </div>
                        <button type="button"
                                wire:click="addMission"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-2 text-xs font-medium text-white hover:bg-primary-dark transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Tambah Misi
                        </button>
                    </div>
                </div>
                <div class="p-6 space-y-3">
                    @forelse ($missions as $index => $mission)
                        <div class="flex items-start gap-3 rounded-lg border border-border bg-bg-secondary/30 p-3">
                            <div class="flex flex-col items-center gap-1 pt-1">
                                <button type="button" wire:click="moveMissionUp({{ $index }})" @disabled($loop->first)
                                        class="flex h-6 w-6 items-center justify-center rounded text-text-muted hover:bg-bg-secondary hover:text-text-primary disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 15.75l7.5-7.5 7.5 7.5"/>
                                    </svg>
                                </button>
                                <span class="text-[10px] font-semibold text-text-muted">{{ $loop->iteration }}</span>
                                <button type="button" wire:click="moveMissionDown({{ $index }})" @disabled($loop->last)
                                        class="flex h-6 w-6 items-center justify-center rounded text-text-muted hover:bg-bg-secondary hover:text-text-primary disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="flex-1">
                                <textarea wire:model="missions.{{ $index }}.content"
                                          rows="2"
                                          placeholder="Tuliskan misi perusahaan..."
                                          class="w-full rounded-lg border border-border bg-card px-3 py-2 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary resize-none"></textarea>
                                @error("missions.{{ $index }}.content") <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <button type="button"
                                    wire:click="removeMission({{ $index }})"
                                    class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 transition-colors"
                                    title="Hapus misi">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @empty
                        <div class="rounded-lg border-2 border-dashed border-border bg-bg-secondary/30 p-8 text-center">
                            <p class="text-sm text-text-muted">Belum ada misi. Klik "Tambah Misi" untuk menambahkan.</p>
                        </div>
                    @endforelse
                    @error('missions') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 4: FOTO PERUSAHAAN --}}
            {{-- ============================================ --}}
            <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-text-primary dark:text-black">Foto Perusahaan</h3>
                            <p class="text-xs text-text-muted">Opsional. Gambar utama perusahaan. JPG, JPEG, PNG, WebP. Maks 2 MB.</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                        <div class="flex h-40 w-full sm:w-56 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-border bg-bg-secondary">
                            @if ($company_image_preview)
                                <img src="{{ $company_image_preview }}" alt="Preview" class="h-full w-full object-cover">
                            @elseif ($existing_company_image)
                                <img src="{{ asset('storage/' . $existing_company_image) }}" alt="Foto Perusahaan" class="h-full w-full object-cover">
                            @else
                                <div class="flex flex-col items-center justify-center text-center px-4">
                                    <svg class="h-12 w-12 text-text-muted/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/>
                                    </svg>
                                    <p class="mt-2 text-xs text-text-muted">Belum ada foto</p>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label class="relative inline-flex cursor-pointer items-center gap-2 rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                </svg>
                                <span>{{ $existing_company_image || $company_image_preview ? 'Ganti Foto' : 'Pilih Foto' }}</span>
                                <input type="file" wire:model="company_image" accept="image/jpg,image/jpeg,image/png,image/webp" class="sr-only">
                            </label>
                            <p class="mt-2 text-xs text-text-muted">Kosongkan jika tidak ingin menampilkan foto di halaman "Tentang Kami".</p>
                            @if ($existing_company_image || $company_image_preview)
                                <button type="button"
                                        wire:click="removeCompanyImage"
                                        class="mt-2 inline-flex items-center gap-1.5 text-xs text-red-500 hover:text-red-700 transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus Foto
                                </button>
                            @endif
                            @error('company_image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 5: STATISTIK PERUSAHAAN --}}
            {{-- ============================================ --}}
            <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
                <div class="border-b border-border bg-bg-secondary/50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-text-primary dark:text-black">Statistik Perusahaan</h3>
                            <p class="text-xs text-text-muted">Angka statistik yang ditampilkan pada halaman "Tentang Kami".</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        {{-- Project Selesai --}}
                        <div>
                            <label for="project_done" class="mb-1.5 flex items-center gap-2 text-sm font-medium text-text-primary dark:text-black">
                                <i class="fa-solid fa-briefcase text-primary"></i>
                                Project Selesai <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="project_done" wire:model="project_done" min="0"
                                   class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                            @error('project_done') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Pelanggan --}}
                        <div>
                            <label for="customers" class="mb-1.5 flex items-center gap-2 text-sm font-medium text-text-primary dark:text-black">
                                <i class="fa-solid fa-users text-primary"></i>
                                Pelanggan <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="customers" wire:model="customers" min="0"
                                   class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                            @error('customers') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tahun Berdiri --}}
                        <div>
                            <label for="years_established" class="mb-1.5 flex items-center gap-2 text-sm font-medium text-text-primary dark:text-black">
                                <i class="fa-solid fa-calendar-check text-primary"></i>
                                Tahun Berdiri <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="years_established" wire:model="years_established" min="0"
                                   class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                            @error('years_established') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Kota Terlayani --}}
                        <div>
                            <label for="cities_served" class="mb-1.5 flex items-center gap-2 text-sm font-medium text-text-primary dark:text-black">
                                <i class="fa-solid fa-city text-primary"></i>
                                Kota Terlayani <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="cities_served" wire:model="cities_served" min="0"
                                   class="w-full rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors focus:border-primary focus:ring-1 focus:ring-primary">
                            @error('cities_served') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
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
