@extends('frontend.layouts.app')

@section('title', 'Katalog Produk')

@section('content')
    {{-- Header --}}
    <section class="relative overflow-hidden bg-wood-bg pb-0 pt-28 sm:pt-32">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="mb-6 flex items-center gap-2 text-xs text-wood-muted">
                <a href="{{ route('home') }}" class="hover:text-wood-primary transition-colors">Home</a>
                <span>/</span>
                <span class="font-semibold text-wood-text">Produk</span>
            </nav>

            <div class="max-w-2xl">
                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-wood-primary">OUR PRODUCTS</span>
                <h1 class="mt-2 text-4xl font-bold tracking-tight text-wood-text font-serif sm:text-5xl">
                    Koleksi Furniture<br>Kayu Jepara
                </h1>
                <p class="mt-4 max-w-lg text-base text-wood-muted leading-relaxed">
                    Temukan furniture berkualitas tinggi yang menghadirkan kehangatan, karakter, dan fungsi ke dalam ruangan Anda.
                </p>
            </div>
        </div>

        {{-- Subtle decorative line --}}
        <div class="mt-8 h-px w-full bg-gradient-to-r from-transparent via-wood-border to-transparent"></div>
    </section>

    {{-- Catalog Livewire Section --}}
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <livewire:frontend.product-catalog />
    </section>

    {{-- Custom Furniture CTA --}}
    <x-home.custom-furniture />
@endsection
