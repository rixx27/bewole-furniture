<div>
    {{-- Stats Cards --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        {{-- Total Reviews --}}
        <div class="rounded-xl border border-border bg-card p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-text-muted">Total Ulasan</p>
                    <p class="mt-1.5 text-2xl font-bold text-text-primary dark:text-black">{{ number_format($totalReviews) }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Average Rating --}}
        <div class="rounded-xl border border-border bg-card p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-text-muted">Rata-rata Rating</p>
                    <div class="mt-1.5 flex items-center gap-2">
                        <p class="text-2xl font-bold text-text-primary dark:text-black">{{ number_format($averageRating, 1) }}</p>
                        <div class="flex items-center">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= round($averageRating))
                                    <svg class="h-3.5 w-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @else
                                    <svg class="h-3.5 w-3.5 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-50 text-yellow-600 dark:bg-yellow-950 dark:text-yellow-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Visible Count --}}
        <div class="rounded-xl border border-border bg-card p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-text-muted">Ditampilkan</p>
                    <p class="mt-1.5 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($visibleCount) }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Hidden Count --}}
        <div class="rounded-xl border border-border bg-card p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-text-muted">Disembunyikan</p>
                    <p class="mt-1.5 text-2xl font-bold text-gray-500 dark:text-gray-400">{{ number_format($hiddenCount) }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-50 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="mb-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
            <div class="relative flex-1 min-w-[200px]">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Cari customer, produk, atau komentar..."
                       class="w-full rounded-lg border border-border bg-card py-2.5 pl-10 pr-4 text-sm text-text-primary placeholder-text-muted focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
            </div>

            <select wire:model.live="ratingFilter"
                    class="rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
                <option value="">Semua Rating</option>
                <option value="5">★★★★★ (5)</option>
                <option value="4">★★★★☆ (4)</option>
                <option value="3">★★★☆☆ (3)</option>
                <option value="2">★★☆☆☆ (2)</option>
                <option value="1">★☆☆☆☆ (1)</option>
            </select>

            <select wire:model.live="visibilityFilter"
                    class="rounded-lg border border-border bg-card px-4 py-2.5 text-sm text-text-primary focus:border-primary focus:outline-hidden focus:ring-1 focus:ring-primary">
                <option value="">Semua Visibilitas</option>
                <option value="visible">Ditampilkan</option>
                <option value="hidden">Disembunyikan</option>
            </select>

            @if ($search || $ratingFilter || $visibilityFilter)
                <button wire:click="resetFilters"
                        class="rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors">
                    Reset
                </button>
            @endif
        </div>
    </div>

    {{-- Table Card --}}
    <div class="rounded-xl border border-border bg-card shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-bg-secondary/50">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Foto</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">
                            <button wire:click="sortBy('product_id')" class="flex items-center gap-1 hover:text-text-primary">
                                Produk
                                @if ($sortField === 'product_id')
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        @endif
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">
                            <button wire:click="sortBy('rating')" class="flex items-center gap-1 hover:text-text-primary">
                                Rating
                                @if ($sortField === 'rating')
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        @endif
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Komentar</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Foto Review</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">Ditampilkan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-text-muted">
                            <button wire:click="sortBy('created_at')" class="flex items-center gap-1 hover:text-text-primary">
                                Tanggal
                                @if ($sortField === 'created_at')
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if ($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        @endif
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-text-muted">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/50">
                    @forelse ($reviews as $review)
                        <tr class="transition-colors hover:bg-bg-secondary/30">
                            {{-- Product Photo --}}
                            <td class="px-6 py-4">
                                <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-bg-secondary">
                                    @if ($review->product && $review->product->thumbnail)
                                        <img src="{{ asset('storage/' . $review->product->thumbnail) }}" alt="{{ $review->product->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-text-muted">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Product Name --}}
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-text-primary dark:text-white">{{ $review->product?->name ?? '-' }}</span>
                            </td>

                            {{-- Customer --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-bold text-primary">
                                        {{ $review->user?->initials() ?? '?' }}
                                    </span>
                                    <span class="text-sm text-text-secondary">{{ $review->user?->name ?? 'Guest' }}</span>
                                </div>
                            </td>

                            {{-- Rating --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-0.5">
                                    {!! $review->rating_stars !!}
                                </div>
                            </td>

                            {{-- Comment Excerpt --}}
                            <td class="px-6 py-4 max-w-[200px]">
                                <p class="text-sm text-text-secondary truncate">{{ $review->excerpt }}</p>
                            </td>

                            {{-- Review Images --}}
                            <td class="px-6 py-4">
                                @if ($review->images->count() > 0)
                                    <div class="flex items-center gap-1">
                                        @foreach ($review->images->take(3) as $image)
                                            <div class="h-7 w-7 overflow-hidden rounded border border-border">
                                                <img src="{{ asset('storage/' . $image->image) }}" alt="Review image" class="h-full w-full object-cover">
                                            </div>
                                        @endforeach
                                        @if ($review->images->count() > 3)
                                            <span class="text-xs text-text-muted">+{{ $review->images->count() - 3 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-text-muted">-</span>
                                @endif
                            </td>

                            {{-- Visibility --}}
                            <td class="px-6 py-4">
                                @if ($review->is_visible)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Ditampilkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                        Disembunyikan
                                    </span>
                                @endif
                            </td>

                            {{-- Date --}}
                            <td class="px-6 py-4">
                                <span class="text-sm text-text-muted">{{ $review->created_at->format('d/m/Y') }}</span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Detail --}}
                                    <button wire:click="openDetail({{ $review->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs font-medium text-text-secondary hover:bg-bg-secondary transition-colors"
                                            title="Lihat detail">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Detail
                                    </button>

                                    {{-- Toggle Visibility --}}
                                    @if ($review->is_visible)
                                        <button wire:click="toggleVisibility({{ $review->id }})"
                                                wire:confirm="Apakah Anda yakin ingin menyembunyikan ulasan ini?"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs font-medium text-amber-600 hover:bg-amber-50 transition-colors"
                                                title="Sembunyikan">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                            </svg>
                                            Sembunyikan
                                        </button>
                                    @else
                                        <button wire:click="toggleVisibility({{ $review->id }})"
                                                wire:confirm="Apakah Anda yakin ingin menampilkan ulasan ini?"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs font-medium text-emerald-600 hover:bg-emerald-50 transition-colors"
                                                title="Tampilkan">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Tampilkan
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-bg-secondary">
                                        <svg class="h-8 w-8 text-text-muted/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-text-primary dark:text-white">Belum ada ulasan</p>
                                    <p class="mt-1 text-xs text-text-muted">Ulasan akan muncul setelah pelanggan memberikan ulasan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($reviews->hasPages())
            <div class="border-t border-border px-6 py-4">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    @if ($showDetailModal && $selectedReviewId)
        <livewire:admin.review.review-detail :key="'detail-' . $selectedReviewId" />
    @endif
</div>

