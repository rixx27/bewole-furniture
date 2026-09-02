@php
    $title = 'Detail Produk';
@endphp

<x-layouts::admin :title="$title">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-text-muted mb-3">
            <a href="{{ route('admin.products.index') }}" class="hover:text-primary transition-colors">Produk</a>
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-text-secondary">{{ $title }}</span>
        </div>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-text-primary dark:text-black">{{ $title }}</h2>
                <p class="mt-1 text-sm text-text-secondary">Informasi lengkap produk "{{ $product->name }}".</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.products.edit', $product) }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-medium text-amber-600 hover:bg-amber-50 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Ubah Produk
                </a>
                <a href="{{ route('admin.products.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Detail Card --}}
    <div class="max-w-6xl">
        {{-- Thumbnail & Info Utama --}}
        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3">
                {{-- Thumbnail --}}
                <div class="md:col-span-1">
                    @if ($product->thumbnail)
                        <img src="{{ asset('storage/' . $product->thumbnail) }}"
                             alt="{{ $product->name }}"
                             class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full min-h-[250px] items-center justify-center bg-bg-secondary">
                            <svg class="h-16 w-16 text-text-muted/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="md:col-span-2 p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-text-primary dark:text-black">{{ $product->name }}</h3>
                            <code class="mt-1 inline-block rounded bg-bg-secondary px-2 py-0.5 text-xs font-mono text-text-secondary">{{ $product->slug }}</code>
                        </div>
                        @if ($product->is_featured)
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                </svg>
                                Unggulan
                            </span>
                        @endif
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Kategori</p>
                            <p class="mt-1 text-sm text-text-primary dark:text-black">{{ $product->category->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Status</p>
                            @php
                                $statusColors = [
                                    'active' => 'emerald',
                                    'pre_order' => 'amber',
                                    'sold_out' => 'gray',
                                ];
                                $color = $statusColors[$product->status] ?? 'gray';
                            @endphp
                            <span class="mt-1 inline-flex items-center gap-1.5 rounded-full bg-{{ $color }}-50 px-3 py-1 text-sm font-medium text-{{ $color }}-700 dark:bg-{{ $color }}-950 dark:text-{{ $color }}-300">
                                <span class="h-2 w-2 rounded-full bg-{{ $color }}-500"></span>
                                {{ $product->status_label }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Harga Unit Unfinished</p>
                            @if ($product->has_discount)
                                <p class="mt-1 text-lg font-bold text-primary">{{ $product->formatted_discount_price }}</p>
                                <p class="text-xs text-text-muted line-through">{{ $product->formatted_price_unfinished }}</p>
                                <span class="mt-0.5 inline-block rounded bg-red-50 px-2 py-0.5 text-xs font-medium text-red-600 dark:bg-red-950 dark:text-red-400">-{{ $product->discount_percentage }}%</span>
                            @else
                                <p class="mt-1 text-lg font-bold text-text-primary dark:text-black">{{ $product->formatted_price_unfinished }}</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Harga Unit Finished</p>
                            <p class="mt-1 text-lg font-bold text-text-primary dark:text-black">{{ $product->formatted_price_finished }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Stok</p>
                            <p class="mt-1 text-sm text-text-primary dark:text-black">{{ $product->stock }} unit</p>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Bahan</p>
                            <p class="mt-1 text-sm text-text-primary dark:text-black">{{ $product->material ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Dimensi</p>
                            <p class="mt-1 text-sm text-text-primary dark:text-black">{{ $product->dimensions ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Berat</p>
                            <p class="mt-1 text-sm text-text-primary dark:text-black">{{ $product->weight !== null ? rtrim(rtrim(number_format($product->weight, 2, '.', ''), '0'), '.') . ' kg' : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden mb-6">
            <div class="border-b border-border bg-bg-secondary/50 px-6 py-3">
                <h4 class="text-sm font-semibold text-text-primary dark:text-black">Deskripsi</h4>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-text-muted mb-1">Deskripsi Singkat</p>
                    <p class="text-sm text-text-secondary">{{ $product->short_description ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-text-muted mb-1">Deskripsi Lengkap</p>
                    <div class="text-sm text-text-secondary leading-relaxed whitespace-pre-line">{{ $product->description ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- Galeri Gambar --}}
        @if ($product->images->count() > 0)
            <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden mb-6">
                <div class="border-b border-border bg-bg-secondary/50 px-6 py-3">
                    <h4 class="text-sm font-semibold text-text-primary dark:text-black">Galeri Gambar ({{ $product->images->count() }})</h4>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                        @foreach ($product->images as $image)
                            <a href="{{ asset('storage/' . $image->image) }}"
                               target="_blank"
                               class="group relative block">
                                <img src="{{ asset('storage/' . $image->image) }}"
                                     alt="Galeri {{ $loop->iteration }}"
                                     class="h-32 w-full rounded-lg border border-border object-cover transition-transform duration-200 group-hover:scale-105">
                                <span class="absolute bottom-2 right-2 rounded bg-black/60 px-2 py-0.5 text-[10px] text-black">
                                    #{{ $loop->iteration }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Informasi Waktu --}}
        <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden mb-6">
            <div class="border-b border-border bg-bg-secondary/50 px-6 py-3">
                <h4 class="text-sm font-semibold text-text-primary dark:text-black">Informasi Sistem</h4>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Dibuat Pada</p>
                        <p class="mt-1 text-sm text-text-primary dark:text-black">{{ $product->created_at->locale('id')->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-text-muted">Diperbarui Pada</p>
                        <p class="mt-1 text-sm text-text-primary dark:text-black">{{ $product->updated_at->locale('id')->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }}</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-border bg-bg-secondary/50 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs text-text-muted">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                        </svg>
                        <span>ID: {{ $product->id }}</span>
                    </div>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline"
                          x-data
                          x-on:submit.prevent="if (confirm('Apakah Anda yakin ingin menghapus produk "{{ $product->name }}"?')) $el.submit()">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Produk
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-layouts::admin>

