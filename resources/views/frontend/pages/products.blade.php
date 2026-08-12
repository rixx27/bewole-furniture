@extends('frontend.layouts.app')

@section('title', 'Katalog Produk')

@section('content')
    {{-- Header --}}
    <section class="relative overflow-hidden bg-wood-primary-dark pt-36 pb-20 sm:pt-40 lg:pt-44 lg:pb-24">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="animate-blob absolute -top-24 -left-24 h-96 w-96 rounded-full bg-wood-secondary/20 blur-3xl"></div>
            <div class="animate-blob absolute bottom-0 right-0 h-[28rem] w-[28rem] rounded-full bg-wood-primary/30 blur-3xl" style="animation-delay: 3s;"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="mb-6 flex items-center gap-2 text-xs text-white/80">
                <a href="{{ route('home') }}" class="transition-colors hover:text-wood-secondary-light">Home</a>
                <span>/</span>
                <span class="font-semibold text-white">Produk</span>
            </nav>

            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-4 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-white/90 backdrop-blur-sm">
                    <span class="text-wood-secondary-light">✦</span>
                    Our Products
                </span>
                <h1 class="mt-5 font-serif text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Koleksi Furniture<br>Kayu Jepara
                </h1>
                <p class="mt-4 max-w-xl text-base leading-relaxed text-white/85 sm:text-lg">
                    Temukan furniture berkualitas tinggi yang menghadirkan kehangatan, karakter, dan fungsi ke dalam ruangan Anda.
                </p>
            </div>
        </div>
    </section>

    {{-- Catalog Livewire Section --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <livewire:frontend.product-catalog />
    </section>

    {{-- Custom Furniture CTA --}}
    <x-home.custom-furniture />
@endsection
