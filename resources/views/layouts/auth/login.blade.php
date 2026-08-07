@php
    $siteName   = App\Helpers\WebsiteSettings::siteName();
    $siteLogo   = App\Helpers\WebsiteSettings::logoUrl();
    $loginBg    = App\Helpers\WebsiteSettings::get('login_background_url');
    $loginQuote = App\Helpers\WebsiteSettings::get('login_quote');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <style>
            /* Prevent flash of unstyled content during animations */
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="login-page min-h-screen bg-[#F7F4EF] font-sans text-[#2D2D2D] antialiased">
{{-- Mobile: flex-col stack; Tablet: 50/50 grid; Desktop: 40% / 60% grid --}}
        <div class="flex min-h-screen w-full flex-col overflow-x-hidden md:grid md:grid-cols-2 lg:grid-cols-[40%_60%] lg:overflow-hidden">

            {{-- ===== LEFT COLUMN: BACKGROUND IMAGE + OVERLAY + BRANDING ===== --}}
            {{-- Mobile: header image (~300px); Tablet/Desktop: fullscreen --}}
            <div class="login-bg relative h-[300px] w-full shrink-0 sm:h-[320px] md:h-screen md:shrink animate-fade-in">
                <div class="absolute inset-0">
                    @if ($loginBg)
                        <img src="{{ $loginBg }}" alt="{{ $siteName }}" class="h-full w-full object-cover">
                    @else
                        <div class="h-full w-full bg-gradient-to-br from-[#5B3A29] via-[#7a5236] to-[#A67C52]"></div>
                    @endif
                </div>
                {{-- Overlay --}}
                <div class="absolute inset-0 bg-black/55"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>

                {{-- Logo: mobile center top, desktop left top --}}
                <div class="relative z-10 flex items-center justify-center p-6 lg:justify-start lg:p-12 animate-slide-down">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 text-white">
                        @if ($siteLogo)
                            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-11 w-11 rounded-lg object-contain">
                        @else
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/15 backdrop-blur-sm">
                                <x-app-logo-icon class="h-6 w-6 fill-current text-[#EDE0CC]" />
                            </span>
                        @endif
                        <span class="text-xl font-semibold tracking-tight">{{ $siteName }}</span>
                    </a>
                </div>

                {{-- Quote (bottom) --}}
                @if ($loginQuote)
                    <div class="absolute inset-x-0 bottom-0 z-10 p-6 lg:p-12 animate-fade-in-up">
                        <blockquote class="max-w-md">
                            <div class="mb-3 h-px w-10 bg-[#A67C52] lg:mb-4 lg:w-12"></div>
                            <p class="text-xl font-medium leading-snug text-white lg:text-3xl">&ldquo;{{ $loginQuote }}&rdquo;</p>
                        </blockquote>
                    </div>
                @endif
            </div>

            {{-- ===== RIGHT COLUMN: FORM ===== --}}
            {{-- Mobile: flex-1 fills remaining height; Desktop: fullscreen column --}}
            <div class="relative flex flex-1 items-center justify-center px-5 py-8 sm:px-8 lg:h-screen lg:flex-none lg:px-12">
                {{-- Decorative blurred blobs --}}
                <div class="pointer-events-none absolute -top-24 -right-24 h-72 w-72 rounded-full bg-[#A67C52]/20 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-[#5B3A29]/10 blur-3xl"></div>

                <div class="relative w-full max-w-[460px] animate-fade-in-up">
                    <div class="liquid-glass rounded-3xl p-6 sm:p-8 lg:p-10">
                        {{ $slot }}
                    </div>

                    <p class="mt-6 pb-2 text-center text-sm text-[#2D2D2D]/55">
                        &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
                    </p>
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
