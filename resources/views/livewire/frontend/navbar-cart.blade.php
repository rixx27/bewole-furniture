<div
    x-data="{ count: {{ (int) $cartQty }} }"
    x-on:cart-updated.window="
        if ($event.detail && typeof $event.detail.count !== 'undefined') {
            count = Number($event.detail.count);
        } else if ($event.detail && Array.isArray($event.detail) && $event.detail[0]?.count !== undefined) {
            count = Number($event.detail[0].count);
        } else if (typeof $event.detail === 'number') {
            count = Number($event.detail);
        }
    "
>
    <a
        href="{{ route('cart.index') }}"
        class="relative flex h-9 w-9 items-center justify-center rounded-full transition-all duration-300 hover:bg-white/15"
        title="Keranjang Belanja"
        aria-label="Keranjang Belanja"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        <span
            x-show="count > 0"
            x-cloak
            x-text="count > 99 ? '99+' : count"
            class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-amber-500 text-[9px] font-bold leading-none text-white shadow-sm"
        >
            {{ $cartQty > 99 ? '99+' : ($cartQty > 0 ? $cartQty : '') }}
        </span>
    </a>
</div>




