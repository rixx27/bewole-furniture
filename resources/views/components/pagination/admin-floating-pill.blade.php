@include('components.pagination.floating-pill', [
    'variant' => $variant ?? 'split',
    'itemLabel' => $itemLabel ?? 'data',
    'scrollTo' => $scrollTo ?? false,
    'isLivewire' => $isLivewire ?? null,
])
