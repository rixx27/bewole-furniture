@php
    $title = 'Daftar Kategori';
@endphp

<x-layouts::admin :title="$title">

    {{-- Alert Success --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mb-4 flex items-center gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="flex-1">{{ session('success') }}</span>
            <button x-on:click="show = false" class="text-emerald-400 hover:text-emerald-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    {{-- Alert Error --}}
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mb-4 flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-300">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="flex-1">{{ session('error') }}</span>
            <button x-on:click="show = false" class="text-red-400 hover:text-red-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-black">Daftar Kategori</h2>
                <p class="mt-1 text-sm text-text-secondary">Kelola kategori produk furniture Anda.</p>
            </div>
            <a href="{{ route('admin.categories.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white transition-all hover:bg-primary-dark shadow-xs">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Tambah Kategori
            </a>
        </div>
    </div>

    {{-- Search --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('admin.categories.index') }}">
            <div class="flex gap-3">
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari kategori..."
                           class="w-full rounded-lg border border-border bg-card py-2.5 pl-10 pr-4 text-sm text-text-primary placeholder-text-muted focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
                </div>
                <button type="submit"
                        class="rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                    Cari
                </button>
                @if ($search)
                    <a href="{{ route('admin.categories.index') }}"
                       class="rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-bg-secondary/50">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Nama Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Slug</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Dibuat Pada</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-text-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/50">
                    @forelse ($categories as $category)
                        <tr class="transition-colors hover:bg-bg-secondary/30">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($category->image)
                                        <img src="{{ asset('storage/' . $category->image) }}"
                                             alt="{{ $category->name }}"
                                             class="h-10 w-10 rounded-lg object-cover">
                                    @else
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary-light text-primary">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-text-primary dark:text-black">{{ $category->name }}</p>
                                        @if ($category->description)
                                            <p class="text-xs text-text-muted line-clamp-1">{{ $category->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <code class="rounded bg-bg-secondary px-2 py-1 text-xs font-mono text-text-secondary">{{ $category->slug }}</code>
                            </td>
                            <td class="px-6 py-4">
                                @if ($category->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700 dark:bg-red-950 dark:text-red-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-text-secondary">{{ $category->created_at->locale('id')->isoFormat('D MMM YYYY') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Lihat --}}
                                    <a href="{{ route('admin.categories.show', $category) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs font-medium text-text-secondary hover:bg-bg-secondary transition-colors"
                                       title="Lihat detail">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Lihat
                                    </a>

                                    {{-- Ubah --}}
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs font-medium text-amber-600 hover:bg-amber-50 transition-colors"
                                       title="Ubah kategori">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Ubah
                                    </a>

                                    {{-- Hapus --}}
                                    <button type="button"
                                            x-data
                                            x-on:click="$dispatch('open-modal', 'delete-category-{{ $category->id }}')"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors"
                                            title="Hapus kategori">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>

                                    {{-- Modal Konfirmasi Hapus --}}
                                    <div x-data="{ show: false }"
                                         x-on:open-modal.window="if ($event.detail === 'delete-category-{{ $category->id }}') show = true"
                                         x-show="show"
                                         x-cloak
                                         class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0">
                                        {{-- Overlay --}}
                                        <div x-show="show"
                                             x-on:click="show = false"
                                             class="absolute inset-0 bg-black/50 backdrop-blur-sm">
                                        </div>
                                        {{-- Modal Content --}}
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
                                                <p class="text-sm font-medium text-text-primary dark:text-white">{{ $category->name }}</p>
                                                @if ($category->products()->count() > 0)
                                                    <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">
                                                        * Kategori ini memiliki {{ $category->products()->count() }} produk terkait.
                                                    </p>
                                                @endif
                                            </div>

                                            <div class="flex items-center justify-end gap-3">
                                                <button type="button"
                                                        x-on:click="show = false"
                                                        class="rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                                                    Batal
                                                </button>
                                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                                                        Ya, Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-bg-secondary">
                                        <svg class="h-8 w-8 text-text-muted/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                    </div>
                                    @if ($search)
                                        <p class="text-sm font-medium text-text-primary dark:text-white">Kategori tidak ditemukan</p>
                                        <p class="mt-1 text-xs text-text-muted">Tidak ada kategori yang cocok dengan pencarian "{{ $search }}"</p>
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

        {{-- Pagination --}}
        @if ($categories->hasPages())
            <div class="border-t border-border px-6 py-4">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

</x-layouts::admin>
