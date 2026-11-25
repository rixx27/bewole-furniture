@php
if (! isset($scrollTo)) {
    $scrollTo = '#product-catalog-top';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (() => {
           const target = document.querySelector('{$scrollTo}') || \$el.closest('[wire\\:id]');
           if (target) {
               target.scrollIntoView({ behavior: 'smooth', block: 'start' });
           }
       })()
    JS
    : '';
@endphp

@if ($paginator->hasPages())
    <div class="flex flex-col items-center justify-center gap-3.5 pt-8 pb-4">
        {{-- Hasil Pencarian / Jumlah Produk --}}
        <p class="text-xs font-medium text-wood-muted tracking-wide">
            Menampilkan <span class="font-semibold text-wood-text">{{ $paginator->firstItem() }}</span>–<span class="font-semibold text-wood-text">{{ $paginator->lastItem() }}</span> dari <span class="font-semibold text-wood-text">{{ $paginator->total() }}</span> produk
        </p>

        {{-- Floating Pill Navigation Bar (Bewole Signature Wood Edition) --}}
        <nav
            role="navigation"
            aria-label="Navigasi Halaman"
            class="relative inline-flex items-center gap-1 sm:gap-1.5 rounded-full border border-wood-primary/40 bg-wood-primary-dark/95 p-1.5 text-white shadow-xl shadow-wood-primary-dark/25 backdrop-blur-md transition-all"
        >
            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <span
                    aria-disabled="true"
                    aria-label="{{ __('pagination.previous') }}"
                    class="inline-flex cursor-not-allowed items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-medium text-white/35 opacity-40 select-none"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="hidden sm:inline">Previous</span>
                </span>
            @else
                <button
                    type="button"
                    wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                    wire:loading.attr="disabled"
                    aria-label="{{ __('pagination.previous') }}"
                    class="group inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold text-wood-secondary-light transition-all duration-200 hover:bg-white/10 hover:text-white active:scale-95 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-wood-secondary-light"
                >
                    <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="hidden sm:inline">Previous</span>
                </button>
            @endif

            {{-- Nomor Halaman (Pills) --}}
            <div class="flex items-center gap-1 px-1">
                @foreach ($elements as $element)
                    {{-- Separator Titik Tiga ("...") --}}
                    @if (is_string($element))
                        <span aria-disabled="true" class="flex h-8 w-8 items-center justify-center text-xs font-bold text-wood-secondary-light/60 select-none">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Daftar Angka Halaman --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <div wire:key="paginator-{{ $paginator->getPageName() }}-page-{{ $page }}" class="relative">
                                @if ($page == $paginator->currentPage())
                                    <span
                                        aria-current="page"
                                        class="flex h-8 w-8 sm:h-8.5 sm:w-8.5 items-center justify-center rounded-full bg-wood-bg text-wood-primary-dark text-xs font-bold shadow-md shadow-black/25 scale-105 select-none transition-all duration-300"
                                    >
                                        {{ $page }}
                                    </span>
                                @else
                                    <button
                                        type="button"
                                        wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                        wire:loading.attr="disabled"
                                        aria-label="Ke halaman {{ $page }}"
                                        class="flex h-8 w-8 sm:h-8.5 sm:w-8.5 items-center justify-center rounded-full text-xs font-semibold text-wood-secondary-light transition-all duration-200 hover:bg-white/10 hover:text-white active:scale-95 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-wood-secondary-light"
                                    >
                                        {{ $page }}
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <button
                    type="button"
                    wire:click="nextPage('{{ $paginator->getPageName() }}')"
                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                    wire:loading.attr="disabled"
                    aria-label="{{ __('pagination.next') }}"
                    class="group inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold text-wood-secondary-light transition-all duration-200 hover:bg-white/10 hover:text-white active:scale-95 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-wood-secondary-light"
                >
                    <span class="hidden sm:inline">Next</span>
                    <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @else
                <span
                    aria-disabled="true"
                    aria-label="{{ __('pagination.next') }}"
                    class="inline-flex cursor-not-allowed items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-medium text-white/35 opacity-40 select-none"
                >
                    <span class="hidden sm:inline">Next</span>
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </nav>
    </div>
@endif
