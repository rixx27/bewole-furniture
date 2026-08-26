@extends('frontend.layouts.app')

@section('title', $product->name)

@section('content')
    {{-- ============================================================
         PAGE HERO (Brown Wood Theme)
         ============================================================ --}}
    <section class="relative overflow-hidden bg-wood-primary-dark pt-36 pb-16 sm:pt-40 lg:pt-44 lg:pb-20">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="animate-blob absolute -top-24 -left-24 h-96 w-96 rounded-full bg-wood-secondary/20 blur-3xl"></div>
            <div class="animate-blob absolute bottom-0 right-0 h-[28rem] w-[28rem] rounded-full bg-wood-primary/30 blur-3xl" style="animation-delay: 3s;"></div>
        </div>

        <div class="relative mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="mb-6 flex flex-wrap items-center gap-2 text-xs text-white/80">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('products.index') }}" class="hover:text-white transition-colors">Produk</a>
                <span>/</span>
                @if ($product->category)
                    <a href="{{ route('products.index', ['selectedCategory' => $product->category->slug]) }}" class="hover:text-white transition-colors">
                        {{ $product->category->name }}
                    </a>
                    <span>/</span>
                @endif
                <span class="font-semibold text-white truncate max-w-xs">{{ $product->name }}</span>
            </nav>

            <span class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-white backdrop-blur-sm">
                <span class="text-wood-secondary-light">✦</span>
                {{ $product->category?->name ?? 'Furniture Premium' }}
            </span>
            <h1 class="font-serif text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">
                {{ $product->name }}
            </h1>
            @if ($product->short_description)
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-white/85 sm:text-base">
                    {{ $product->short_description }}
                </p>
            @endif
        </div>
    </section>

    {{-- ============================================================
         PRODUCT DETAIL CONTENT
         ============================================================ --}}
    <section class="bg-wood-bg py-10 sm:py-14 lg:py-16">
        <livewire:frontend.product-detail :product="$product" />
    </section>
@endsection
