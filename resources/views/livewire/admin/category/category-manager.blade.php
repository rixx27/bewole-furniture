@php
    $title = 'Daftar Kategori';
@endphp

<div class="space-y-6">

    {{-- ============================================================
         ALERT (sukses / error dari Livewire dispatch)
         ============================================================ --}}
    <div x-data="{ toast: null }"
         x-on:category-saved.window="toast = $event.detail; setTimeout(() => toast = null, 5000)">
        <template x-if="toast">
            <div x-show="toast"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="mb-4 flex items-center gap-3 rounded-lg border px-4 py-3 text-sm"
                 :class="toast.type === 'error'
                    ? 'border-red-200 bg-red-50 text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-300'
                    : 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300'">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          :d="toast.type === 'error' ? 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'"/>
                </svg>
                <span class="flex-1" x-text="toast.message"></span>
                <button x-on:click="toast = null" class="opacity-60 hover:opacity-100">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>

    {{-- ============================================================
         FORM CREATE / EDIT
         ============================================================ --}}
    @if ($editing)
        <div class="rounded-xl border border-border bg-card p-6 shadow-sm">
            {{-- Breadcrumb + Header --}}
            <div class="mb-6">
                <div class="flex items-center gap-2 text-xs text-text-muted mb-3">
                    <a href="{{ route('admin.categories.index') }}" wire:navigate class="hover:text-primary transition-colors">Kategori</a>
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-text-secondary">{{ $editingId ? 'Ubah Kategori' : 'Tambah Kategori' }}</span>
                </div>
                <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-black">{{ $editingId ? 'Ubah Kategori' : 'Tambah Kategori' }}</h2>
                <p class="mt-1 text-sm text-text-secondary">Kelola kategori untuk section "Explore Our Collection".</p>
            </div>

            <form wire:submit="save" class="space-y-5">
                {{-- Nama Kategori --}}
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           wire:model="name"
                           placeholder="Contoh: Lemari Kayu"
                           class="w-full rounded-lg border {{ $errors->has('name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cover Image --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                        Cover Image <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-start gap-4">
                        {{-- Preview --}}
                        <div class="relative h-28 w-28 shrink-0 overflow-hidden rounded-xl border border-border bg-bg-secondary">
                            @if ($cover_preview)
                                <img src="{{ $cover_preview }}" alt="Preview" class="h-full w-full object-cover">
                                <button type="button"
                                        wire:click="removeCover"
                                        class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-full bg-black/60 text-white hover:bg-black/80"
                                        title="Hapus cover">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            @else
                                <div class="flex h-full w-full items-center justify-center text-text-muted">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1">
                            <input type="file"
                                   accept="image/jpeg,image/png,image/webp"
                                   wire:model="cover_image"
                                   class="block w-full text-sm text-text-secondary file:mr-3 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-dark">
                            <p class="mt-2 text-xs text-text-muted">Format: JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</p>
                            @error('cover_image')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                            <div wire:loading wire:target="cover_image" class="mt-2 text-xs text-primary">Mengunggah cover...</div>
                        </div>
                    </div>
                </div>

                {{-- Deskripsi Singkat --}}
                <div>
                    <label for="short_description" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                        Deskripsi Singkat
                    </label>
                    <textarea id="short_description"
                              wire:model="short_description"
                              rows="3"
                              placeholder="Deskripsi singkat kategori (opsional)"
                              class="w-full rounded-lg border {{ $errors->has('short_description') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors"></textarea>
                    @error('short_description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Urutan + Status --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="sort_order" class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">
                            Urutan
                        </label>
                        <input type="number"
                               id="sort_order"
                               wire:model="sort_order"
                               min="0"
                               placeholder="0"
                               class="w-full rounded-lg border {{ $errors->has('sort_order') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-border focus:border-primary focus:ring-primary' }} bg-card px-4 py-2.5 text-sm text-text-primary placeholder-text-muted outline-hidden ring-0 transition-colors">
                        @error('sort_order')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-text-primary dark:text-black">Status</label>
                        <label class="flex items-center gap-3 rounded-lg border border-border bg-card px-4 py-2.5">
                            <input type="checkbox"
                                   wire:model="is_active"
                                   class="h-4 w-4 rounded border-border text-primary focus:ring-primary">
                            <span class="text-sm font-medium text-text-primary dark:text-black">Aktif</span>
                        </label>
                        <p class="mt-1 text-xs text-text-muted">Nonaktifkan untuk menyembunyikan dari tampilan publik.</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 border-t border-border pt-5">
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-white transition-all hover:bg-primary-dark shadow-xs disabled:opacity-60">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span wire:loading.remove wire:target="save">Simpan</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                    <button type="button"
                            wire:click="closeForm"
                            class="rounded-lg border border-border bg-card px-5 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    @else
        {{-- ============================================================
             LISTING
             ============================================================ --}}
        <div class="mb-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-black">Daftar Kategori</h2>
                    <p class="mt-1 text-sm text-text-secondary">Kelola kategori produk furniture Anda.</p>
                </div>
                <button type="button"
                        wire:click="openCreate"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white transition-all hover:bg-primary-dark shadow-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Tambah Kategori
                </button>
            </div>
        </div>

        {{-- Search --}}
        <div class="mb-6">
            <div class="relative max-w-md">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari kategori..."
                       class="w-full rounded-lg border border-border bg-card py-2.5 pl-10 pr-4 text-sm text-text-primary placeholder-text-muted focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
            </div>
        </div>

        {{-- Table Card --}}
        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border bg-bg-secondary/50">
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Cover</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Code</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Urutan</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-text-muted">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        @forelse ($categories as $category)
                            <tr class="transition-colors hover:bg-bg-secondary/30">
                                {{-- Cover --}}
                                <td class="px-6 py-4">
                                    @if ($category['cover_image'])
                                        <img src="{{ $category['cover_image_url'] }}"
                                             alt="{{ $category['name'] }}"
                                             class="h-[70px] w-[70px] rounded-lg object-cover">
                                    @else
                                        <div class="flex h-[70px] w-[70px] items-center justify-center rounded-lg bg-secondary-light text-primary">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>

                                {{-- Nama --}}
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-text-primary dark:text-black">{{ $category['name'] }}</p>
                                    @if ($category['short_description'])
                                        <p class="text-xs text-text-muted line-clamp-1">{{ $category['short_description'] }}</p>
                                    @endif
                                </td>

                                {{-- Code --}}
                                <td class="px-6 py-4">
                                    <code class="rounded bg-bg-secondary px-2 py-1 text-xs font-mono text-text-secondary">{{ $category['code'] }}</code>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4">
                                    <button type="button"
                                            wire:click="toggleActive({{ $category['id'] }})"
                                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium transition-colors {{ $category['is_active'] ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-950 dark:text-red-300' }}"
                                            title="Klik untuk mengubah status">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $category['is_active'] ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                        {{ $category['is_active'] ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </td>

                                {{-- Urutan --}}
                                <td class="px-6 py-4">
                                    <span class="text-sm text-text-secondary">{{ $category['sort_order'] }}</span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Ubah --}}
                                        <button type="button"
                                                wire:click="openEdit({{ $category['id'] }})"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs font-medium text-amber-600 hover:bg-amber-50 transition-colors"
                                                title="Ubah kategori">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Ubah
                                        </button>

                                        {{-- Hapus --}}
                                        <button type="button"
                                                x-data
                                                x-on:click="$dispatch('open-modal', 'delete-category-{{ $category['id'] }}')"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors"
                                                title="Hapus kategori">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>

                                        {{-- Modal Konfirmasi Hapus --}}
                                        <div x-data="{ show: false }"
                                             x-on:open-modal.window="if ($event.detail === 'delete-category-{{ $category['id'] }}') show = true"
                                             x-show="show"
                                             x-cloak
                                             class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             x-transition:leave="transition ease-in duration-150"
                                             x-transition:leave-start="opacity-100"
                                             x-transition:leave-end="opacity-0">
                                            <div x-show="show" x-on:click="show = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
                                            <div x-show="show"
                                                 x-transition:enter="transition ease-out duration-300"
                                                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                 x-transition:leave="transition ease-in duration-200"
                                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                                                 class="relative w-full max-w-md rounded-xl bg-card p-6 shadow-2xl border border-border"
                                                 x-on:click.away="show = false">
                                                <div class="mb-5 flex items-center gap-4">
                                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-600 dark:bg-red-950 dark:text-red-400">
                                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h3 class="text-lg font-semibold text-text-primary dark:text-white">Konfirmasi Hapus</h3>
                                                        <p class="text-sm text-text-secondary">Apakah Anda yakin ingin menghapus kategori ini?</p>
                                                    </div>
                                                </div>

                                                <div class="mb-5 rounded-lg bg-bg-secondary p-4">
                                                    <p class="text-sm font-medium text-text-primary dark:text-white">{{ $category['name'] }}</p>
                                                    @if ($category['products_count'] > 0)
                                                        <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">
                                                            * Kategori ini memiliki {{ $category['products_count'] }} produk terkait.
                                                        </p>
                                                    @endif
                                                </div>

                                                <div class="flex items-center justify-end gap-3">
                                                    <button type="button"
                                                            x-on:click="show = false"
                                                            class="rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                                                        Batal
                                                    </button>
                                                    <button type="button"
                                                            wire:click="delete({{ $category['id'] }})"
                                                            x-on:click="show = false"
                                                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                                                        Ya, Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-bg-secondary">
                                            <svg class="h-8 w-8 text-text-muted/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        </div>
                                        @if ($search !== '')
                                            <p class="text-sm font-medium text-text-primary dark:text-white">Kategori tidak ditemukan</p>
                                            <p class="mt-1 text-xs text-text-muted">Tidak ada kategori yang cocok dengan pencarian "{{ $search }}".</p>
                                        @else
                                            <p class="text-sm font-medium text-text-primary dark:text-white">Belum ada kategori</p>
                                            <p class="mt-1 text-xs text-text-muted">Mulai dengan menambahkan kategori baru.</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
