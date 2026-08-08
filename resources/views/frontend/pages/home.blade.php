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
<div id="about"></div>
    <x-home.featured-products />

    {{-- ============================================================
         PHASE 4 : OUR PHILOSOPHY
         Preview singkat perusahaan (Tentang Kami + foto).
         Tombol "Selengkapnya" → halaman Tentang Kami.
         ============================================================ --}}
    <x-home.philosophy />

    <div id="why-us"></div>
    <div id="reviews"></div>
    <div id="faq"></div>
    <div id="contact"></div>
@endsection
