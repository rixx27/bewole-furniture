@extends('frontend.layouts.app')

@section('title', 'Beranda')

@section('content')
    {{-- ============================================================
         PHASE 2 : HERO SECTION
         Data sepenuhnya dari Admin Hero (customizable via dashboard).
         ============================================================ --}}
    @include('frontend.partials.hero', ['hero' => $hero ?? null])

    {{-- ============================================================
         EXPLORE OUR COLLECTION : Apple-style interactive showcase
         Data kategori berasal dari Admin Panel (Category model).
         ============================================================ --}}
    <x-home.category-showcase />

    {{-- ============================================================
         PHASE 3+ : Featured Pieces
         Produk unggulan dari database (status aktif / featured).
         Section ber-id "products" agar anchor Hero & menu berfungsi.
         ============================================================ --}}
    <x-home.featured-products />

    {{-- ============================================================
         PHASE 4 : OUR PHILOSOPHY
         Preview singkat perusahaan (Tentang Kami + foto).
         Section ber-id "about" agar target Hero "about" menuju sini.
         ============================================================ --}}
    <x-home.philosophy />

    {{-- ============================================================
         PHASE 5 : CUSTOM FURNITURE
         Section CTA khusus untuk request furniture custom.
         Section ber-id "custom-furniture".
         ============================================================ --}}
    <x-home.custom-furniture />

    {{-- ============================================================
         PHASE 6 : FAQ FRONTEND
         Section FAQ dinamis dari database CRUD Admin.
         Section ber-id "faq".
         ============================================================ --}}
    <x-home.faq />

    {{-- Anchor targets — scroll-margin-top agar tidak tertutup navbar fixed. --}}
    <div id="why-us" class="scroll-mt-24"></div>
    <div id="reviews" class="scroll-mt-24"></div>
@endsection
