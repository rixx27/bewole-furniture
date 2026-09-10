@php
    try {
        $siteName = App\Helpers\WebsiteSettings::siteName();
        $siteLogo = App\Helpers\WebsiteSettings::logoUrl();
        $whatsappUrl = App\Helpers\WebsiteSettings::get('whatsapp_url');
    } catch (\Throwable $e) {
        $siteName = config('app.name', 'Bewole Furniture');
        $siteLogo = null;
        $whatsappUrl = null;
    }
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ $siteName }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F7F4EF;
            color: #2D2D2D;
        }
        .font-serif {
            font-family: 'Playfair Display', Georgia, serif;
        }
    </style>
</head>
<body class="flex min-h-screen flex-col justify-between bg-[#F7F4EF] antialiased selection:bg-[#A67C52]/20 selection:text-[#5B3A29]">

    {{-- Top Header / Brand --}}
    <header class="w-full px-6 py-6 sm:px-10">
        <div class="mx-auto flex max-w-7xl items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3 transition-opacity hover:opacity-85">
                @if ($siteLogo)
                    <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-9 w-9 rounded-full object-contain shadow-xs">
                @else
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#5B3A29] text-sm font-bold text-[#EDE0CC] shadow-xs">
                        {{ strtoupper(substr($siteName, 0, 1)) }}
                    </span>
                @endif
                <span class="text-lg font-bold tracking-tight text-[#2D2D2D]">{{ $siteName }}</span>
            </a>

            <a href="{{ url('/') }}" class="text-xs font-semibold text-[#5B3A29] transition-colors hover:text-[#A67C52] sm:text-sm">
                ← Kembali ke Beranda
            </a>
        </div>
    </header>

    {{-- Main Error Content --}}
    <main class="relative flex flex-1 items-center justify-center px-4 py-12 sm:px-6 lg:px-8 overflow-hidden">
        {{-- Ambient decorative background blobs --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 flex items-center justify-center">
            <div class="h-96 w-96 rounded-full bg-[#A67C52]/10 blur-3xl"></div>
            <div class="absolute -bottom-10 right-1/4 h-80 w-80 rounded-full bg-[#5B3A29]/5 blur-3xl"></div>
        </div>

        <div class="relative mx-auto w-full max-w-xl text-center">
            {{-- Status Code Badge --}}
            <div class="inline-flex items-center gap-2 rounded-full border border-[#5B3A29]/15 bg-white/70 px-4 py-1.5 text-xs font-bold uppercase tracking-[0.2em] text-[#5B3A29] shadow-xs backdrop-blur-md">
                <span>✦</span>
                <span>@yield('code')</span>
            </div>

            {{-- Big Status Code Number --}}
            <h1 class="mt-4 font-serif text-7xl font-bold tracking-tight text-[#5B3A29] sm:text-8xl lg:text-9xl">
                @yield('code')
            </h1>

            {{-- Title --}}
            <h2 class="mt-4 font-serif text-2xl font-bold tracking-tight text-[#2D2D2D] sm:text-3xl lg:text-4xl">
                @yield('message')
            </h2>

            {{-- Description --}}
            <p class="mx-auto mt-4 max-w-md text-sm leading-relaxed text-[#78716C] sm:text-base">
                @yield('description')
            </p>

            {{-- Action Buttons --}}
            <div class="mt-8 flex flex-col items-center justify-center gap-3.5 sm:flex-row sm:gap-4">
                <a
                    href="{{ url('/') }}"
                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-full bg-[#5B3A29] px-7 py-3.5 text-sm font-semibold text-[#EDE0CC] shadow-lg shadow-[#5B3A29]/20 transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#422818] hover:shadow-xl hover:shadow-[#5B3A29]/30"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Ke Halaman Utama</span>
                </a>

                <a
                    href="{{ url('/produk') }}"
                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-full border border-[#5B3A29]/20 bg-white/80 px-7 py-3.5 text-sm font-semibold text-[#5B3A29] shadow-xs backdrop-blur-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-[#5B3A29]/40 hover:bg-white"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span>Katalog Produk</span>
                </a>
            </div>

            @if ($whatsappUrl)
                <div class="mt-8 text-xs text-[#78716C]">
                    Butuh bantuan segera?
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-[#5B3A29] underline underline-offset-4 hover:text-[#A67C52]">
                        Hubungi Kami via WhatsApp
                    </a>
                </div>
            @endif
        </div>
    </main>

    {{-- Minimal Footer --}}
    <footer class="w-full py-6 text-center text-xs text-[#A8A29E]">
        &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
    </footer>

</body>
</html>
