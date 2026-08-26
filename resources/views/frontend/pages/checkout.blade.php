@extends('frontend.layouts.app')

@section('title', 'Checkout')

@section('content')
    <section class="relative overflow-hidden bg-wood-primary-dark pt-36 pb-20 sm:pt-40 lg:pt-44 lg:pb-24">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="animate-blob absolute -top-24 -left-24 h-96 w-96 rounded-full bg-wood-secondary/20 blur-3xl"></div>
            <div class="animate-blob absolute bottom-0 right-0 h-[28rem] w-[28rem] rounded-full bg-wood-primary/30 blur-3xl" style="animation-delay: 3s;"></div>
        </div>
        <div class="relative mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="mb-6 flex items-center gap-2 text-xs text-white/80">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('products.index') }}" class="hover:text-white transition-colors">Produk</a>
                <span>/</span>
                <a href="{{ route('cart.index') }}" class="hover:text-white transition-colors">Keranjang</a>
                <span>/</span>
                <span class="font-semibold text-white">Checkout</span>
            </nav>
            <h1 class="font-serif text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">Formulir Pembelian</h1>
            <p class="mt-4 max-w-2xl text-sm leading-relaxed text-white/85 sm:text-base">Lengkapi data diri dan alamat pengiriman Anda untuk menyelesaikan pesanan.</p>
        </div>
    </section>

    <section class="bg-wood-bg py-16 sm:py-20 lg:py-24">
        <livewire:frontend.checkout-page />
    </section>
@endsection
