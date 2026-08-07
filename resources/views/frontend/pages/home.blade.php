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
         PHASE 3+ : Section berikutnya akan diletakkan di sini.
         ID disamakan dengan anchor HeroButtonTarget agar smooth
         scroll & tombol hero berfungsi.
         ============================================================ --}}
    <div id="about"></div>
    <div id="products"></div>
    <div id="why-us"></div>
    <div id="reviews"></div>
    <div id="faq"></div>
    <div id="contact"></div>
@endsection
