@php
    $siteName = App\Helpers\WebsiteSettings::siteName();
    $siteLogo = App\Helpers\WebsiteSettings::logoUrl();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="premium-page relative min-h-screen overflow-x-hidden bg-[#F7F4EF] font-sans text-[#2D2D2D] antialiased">

        {{-- Decorative blurred background (full page) --}}
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute -left-32 -top-32 h-[28rem] w-[28rem] rounded-full bg-[#A67C52]/25 blur-3xl"></div>
            <div class="absolute -bottom-32 -right-32 h-[28rem] w-[28rem] rounded-full bg-[#5B3A29]/15 blur-3xl"></div>
            <div class="absolute right-1/4 top-1/3 h-72 w-72 rounded-full bg-[#EDE0CC]/50 blur-3xl"></div>
            <div class="absolute bottom-1/4 left-1/4 h-56 w-56 rounded-full bg-white/40 blur-3xl"></div>

            {{-- Very subtle gradient --}}
            <div class="absolute inset-0 bg-gradient-to-br from-white/30 via-transparent to-[#A67C52]/10"></div>

{{-- Very subtle grain/noise --}}
            <div class="absolute inset-0 opacity-[0.03]"
                 style="background-image: url('data:image/svg+xml;utf8,%3Csvg%20xmlns=%22http://www.w3.org/2000/svg%22%20width=%22120%22%20height=%22120%22%3E%3Cfilter%20id=%22n%22%3E%3CfeTurbulence%20type=%22fractalNoise%22%20baseFrequency=%220.9%22%20numOctaves=%222%22/%3E%3C/filter%3E%3Crect%20width=%22100%25%22%20height=%22100%25%22%20filter=%22url(%23n)%22/%3E%3C/svg%3E');">
            </div>
        </div>

        {{-- Center content --}}
        <main class="relative flex min-h-screen flex-col items-center justify-center px-4 py-10 sm:px-6 sm:py-12">
            <div class="w-full max-w-[420px] lg:max-w-[440px]">

                {{-- Logo (fade down) --}}
                <div class="mb-7 flex flex-col items-center gap-3 animate-fade-down sm:mb-9">
                    <a href="{{ route('home') }}" wire:navigate class="group flex flex-col items-center gap-3">
                        @if ($siteLogo)
                            <img src="{{ $siteLogo }}" alt="{{ $siteName }}"
                                 class="h-14 w-14 rounded-2xl object-contain shadow-sm ring-1 ring-[#A67C52]/20 transition-transform duration-300 group-hover:scale-105">
                        @else
                            <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#5B3A29] shadow-md">
                                <x-app-logo-icon class="h-8 w-8 fill-current text-[#EDE0CC]" />
                            </span>
                        @endif
                        <span class="text-2xl font-semibold tracking-tight text-[#5B3A29]">{{ $siteName }}</span>
                    </a>
                </div>

                {{-- Liquid glass card (fade up) --}}
                <div class="liquid-glass animate-fade-up rounded-3xl p-6 sm:p-8">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-center text-sm text-[#2D2D2D]/50 animate-fade-in">
                    &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
                </p>
            </div>
        </main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
