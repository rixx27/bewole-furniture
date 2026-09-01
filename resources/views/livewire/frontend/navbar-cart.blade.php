<div
    x-data="{ 
        count: @entangle('cartQty'),
        bump: false,
        triggerBump() {
            this.bump = true;
            setTimeout(() => this.bump = false, 600);
        }
    }"
    x-on:cart-updated.window="
        if ($event.detail && typeof $event.detail.count !== 'undefined') {
            count = Number($event.detail.count);
        }
        triggerBump();
    "
    class="relative"
>
    <a
        href="{{ route('cart.index') }}"
        class="relative flex h-9 w-9 items-center justify-center rounded-full transition-all duration-300 hover:bg-white/15 active:scale-95"
        title="Keranjang Belanja"
        aria-label="Keranjang Belanja"
    >
        <svg class="h-5 w-5 transition-transform duration-300" :class="{ 'scale-110 text-amber-400': bump }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        <span
            x-show="count > 0"
            x-cloak
            x-text="count > 99 ? '99+' : count"
            :class="{ 'scale-125 bg-amber-400 ring-4 ring-amber-400/40': bump }"
            class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-amber-500 text-[9px] font-bold leading-none text-white shadow-sm transition-all duration-300"
        ></span>
    </a>
</div>
