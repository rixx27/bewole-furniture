@extends('frontend.layouts.app')

@section('title', 'Lacak Pesanan')

@section('content')
    {{-- ============================================================
         PAGE HERO
         ============================================================ --}}
    <section class="relative overflow-hidden bg-wood-primary-dark pt-36 pb-20 sm:pt-40 lg:pt-44 lg:pb-24">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="animate-blob absolute -top-24 -left-24 h-96 w-96 rounded-full bg-wood-secondary/20 blur-3xl"></div>
            <div class="animate-blob absolute bottom-0 right-0 h-[28rem] w-[28rem] rounded-full bg-wood-primary/30 blur-3xl" style="animation-delay: 3s;"></div>
        </div>

        <div class="relative mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="mb-6 flex items-center gap-2 text-xs text-white/80">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
                <span>/</span>
                <span class="font-semibold text-white">Tracking</span>
            </nav>

            <div class="max-w-3xl">
                <span class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-white backdrop-blur-sm">
                    <span class="text-wood-secondary-light">✦</span>
                    Lacak Pengiriman & Produksi
                </span>
                <h1 class="font-serif text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Lacak Pesanan Anda
                </h1>
                <p class="mt-4 max-w-2xl text-sm leading-relaxed text-white/85 sm:text-base">
                    Pantau tahapan pengerjaan dan status pengiriman pesanan furniture Anda secara transparan dan real-time.
                </p>
            </div>
        </div>
    </section>

    {{-- ============================================================
         TRACKING LIVEWIRE SECTION
         ============================================================ --}}
    <section class="bg-wood-bg py-16 sm:py-20 lg:py-24">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <livewire:frontend.order-tracking :order-code="$order_code ?? request('order_code') ?? request('code')" />
        </div>
    </section>
@endsection
