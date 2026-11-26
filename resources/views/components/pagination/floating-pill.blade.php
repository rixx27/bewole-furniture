@php
    $variant = $variant ?? 'centered';
    $itemLabel = $itemLabel ?? 'produk';
    $isLivewire = $isLivewire ?? str_starts_with((string) (\Illuminate\Pagination\Paginator::$defaultView ?? ''), 'livewire::');

    if (! isset($scrollTo)) {
        $scrollTo = $variant === 'split' ? false : '#product-catalog-top';
    }

    $scrollIntoViewJsSnippet = ($isLivewire && $scrollTo !== false)
        ? <<<JS
           (() => {
               const target = document.querySelector('{$scrollTo}') || \$el.closest('[wire\\:id]');
               if (target) {
                   target.scrollIntoView({ behavior: 'smooth', block: 'start' });
               }
           })()
        JS
        : '';

    $wrapperClasses = $variant === 'split'
        ? 'flex flex-col items-center justify-between gap-3 sm:flex-row'
        : 'flex flex-col items-center justify-center gap-3.5 pt-8 pb-4';

    $navClasses = 'relative inline-flex items-center gap-1 sm:gap-1.5 rounded-full border border-wood-primary/40 bg-wood-primary-dark/95 p-1.5 text-white shadow-xl shadow-wood-primary-dark/25 backdrop-blur-md transition-all max-w-full overflow-x-auto no-scrollbar';
    $activeBtnClasses = 'group inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold text-wood-secondary-light transition-all duration-200 hover:bg-white/10 hover:text-white active:scale-95 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-wood-secondary-light shrink-0';
    $disabledBtnClasses = 'inline-flex cursor-not-allowed items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-medium text-white/35 opacity-40 select-none shrink-0';
    $activePageClasses = 'flex h-8 w-8 sm:h-8.5 sm:w-8.5 items-center justify-center rounded-full bg-wood-bg text-wood-primary-dark text-xs font-bold shadow-md shadow-black/25 scale-105 select-none transition-all duration-300 shrink-0';
    $inactivePageClasses = 'flex h-8 w-8 sm:h-8.5 sm:w-8.5 items-center justify-center rounded-full text-xs font-semibold text-wood-secondary-light transition-all duration-200 hover:bg-white/10 hover:text-white active:scale-95 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-wood-secondary-light shrink-0';
@endphp

@if ($paginator->hasPages())
    <div class="{{ $wrapperClasses }}">
        {{-- Info Jumlah Data --}}
        <p class="text-xs font-medium text-wood-muted tracking-wide">
            Menampilkan <span class="font-semibold text-wood-text">{{ $paginator->firstItem() }}</span>–<span class="font-semibold text-wood-text">{{ $paginator->lastItem() }}</span> dari <span class="font-semibold text-wood-text">{{ $paginator->total() }}</span> {{ $itemLabel }}
        </p>

        {{-- Floating Pill Navigation Bar (Bewole Signature Wood Edition) --}}
        <nav role="navigation" aria-label="Navigasi Halaman" class="{{ $navClasses }}">
            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="{{ $disabledBtnClasses }}">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="hidden sm:inline">Previous</span>
                </span>
            @elseif ($isLivewire)
                <button
                    type="button"
                    wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    @if ($scrollIntoViewJsSnippet) x-on:click="{{ $scrollIntoViewJsSnippet }}" @endif
                    wire:loading.attr="disabled"
                    aria-label="{{ __('pagination.previous') }}"
                    class="{{ $activeBtnClasses }}"
                >
                    <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="hidden sm:inline">Previous</span>
                </button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}" class="{{ $activeBtnClasses }}">
                    <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="hidden sm:inline">Previous</span>
                </a>
            @endif

            {{-- Nomor Halaman (Pills) --}}
            <div class="flex items-center gap-1 px-1">
                @foreach ($elements as $element)
                    {{-- Separator Titik Tiga ("...") --}}
                    @if (is_string($element))
                        <span aria-disabled="true" class="flex h-8 w-8 items-center justify-center text-xs font-bold text-wood-secondary-light/60 select-none shrink-0">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Daftar Angka Halaman --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <div @if ($isLivewire) wire:key="paginator-{{ $paginator->getPageName() }}-page-{{ $page }}" @endif class="relative">
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="{{ $activePageClasses }}">
                                        {{ $page }}
                                    </span>
                                @elseif ($isLivewire)
                                    <button
                                        type="button"
                                        wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                        @if ($scrollIntoViewJsSnippet) x-on:click="{{ $scrollIntoViewJsSnippet }}" @endif
                                        wire:loading.attr="disabled"
                                        aria-label="Ke halaman {{ $page }}"
                                        class="{{ $inactivePageClasses }}"
                                    >
                                        {{ $page }}
                                    </button>
                                @else
                                    <a href="{{ $url }}" aria-label="Ke halaman {{ $page }}" class="{{ $inactivePageClasses }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                @if ($isLivewire)
                    <button
                        type="button"
                        wire:click="nextPage('{{ $paginator->getPageName() }}')"
                        @if ($scrollIntoViewJsSnippet) x-on:click="{{ $scrollIntoViewJsSnippet }}" @endif
                        wire:loading.attr="disabled"
                        aria-label="{{ __('pagination.next') }}"
                        class="{{ $activeBtnClasses }}"
                    >
                        <span class="hidden sm:inline">Next</span>
                        <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @else
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}" class="{{ $activeBtnClasses }}">
                        <span class="hidden sm:inline">Next</span>
                        <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endif
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="{{ $disabledBtnClasses }}">
                    <span class="hidden sm:inline">Next</span>
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </nav>
    </div>
@endif
