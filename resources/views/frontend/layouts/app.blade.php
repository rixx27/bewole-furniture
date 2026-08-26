@php
    $siteName = App\Helpers\WebsiteSettings::siteName();
    $siteLogo = App\Helpers\WebsiteSettings::logoUrl();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

<title>
        @hasSection('title')
            @yield('title') — {{ $siteName }}
        @else
            {{ $siteName }}
        @endif
    </title>
    <meta name="description" content="{{ App\Helpers\WebsiteSettings::get('site_tagline') ?? $siteName }}">

    @if ($siteLogo)
        <link rel="icon" href="{{ $siteLogo }}">
    @else
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @endif

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/frontend.js'])
    @livewireStyles
</head>
<body class="flex min-h-screen flex-col bg-wood-bg font-sans text-wood-text antialiased">
    @include('frontend.partials.navbar')

<main class="flex-1">
        @yield('content')
    </main>

    @include('frontend.partials.footer')

    {{-- Back To Top --}}
    <button
        type="button"
        x-data="{ visible: false }"
        x-init="
            const onScroll = () => visible = window.scrollY > 500;
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();
        "
        x-show="visible"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-4 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-4 opacity-0"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-6 right-6 z-50 flex h-12 w-12 items-center justify-center rounded-full bg-wood-primary text-white shadow-lg shadow-wood-primary/30 transition-transform duration-300 hover:-translate-y-1 hover:bg-wood-primary-dark"
        aria-label="Kembali ke atas"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 15.75l7.5-7.5 7.5 7.5"/>
        </svg>
    </button>

    @livewireScripts
    @stack('scripts')
</body>
</html>
