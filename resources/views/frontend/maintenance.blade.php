@php
    $title = 'Maintenance';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - {{ App\Helpers\WebsiteSettings::siteName() }}</title>
    @if (App\Helpers\WebsiteSettings::logoUrl())
        <link rel="icon" href="{{ App\Helpers\WebsiteSettings::logoUrl() }}">
    @else
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @endif
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="bg-bg-primary font-sans text-text-primary antialiased">
    <div class="flex min-h-screen items-center justify-center p-6">
        <div class="w-full max-w-2xl text-center">
            {{-- Icon --}}
            <div class="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-3xl bg-amber-100 dark:bg-amber-950">
                <svg class="h-12 w-12 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17l-4.49 2.59m0-8.64l4.49 2.59m-4.49 2.6v2.59c0 .51.27.98.72 1.23l3.96 2.29c.45.26.99.26 1.44 0l3.96-2.29c.45-.26.72-.72.72-1.23v-2.59m0-5.18v-2.59c0-.51-.27-.98-.72-1.23l-3.96-2.29a1.414 1.414 0 00-1.44 0L7.23 3.99a1.458 1.458 0 00-.72 1.23v2.59m0 5.18c0 .51.27.98.72 1.23l3.96 2.29c.45.26.99.26 1.44 0"/>
                </svg>
            </div>

            {{-- Content --}}
            <h1 class="mb-3 text-4xl font-bold tracking-tight text-text-primary dark:text-black">
                Website Sedang Dalam Pemeliharaan
            </h1>
            <p class="mx-auto mb-8 max-w-lg text-lg text-text-secondary leading-relaxed">
                {{ $message }}
            </p>

            {{-- Decorative --}}
            <div class="flex items-center justify-center gap-4 text-text-muted">
                <div class="h-px w-16 bg-border"></div>
                <span class="text-sm font-medium">{{ App\Helpers\WebsiteSettings::siteName() }}</span>
                <div class="h-px w-16 bg-border"></div>
            </div>

            {{-- Admin Login --}}
            <div class="mt-8">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-border bg-card px-6 py-3 text-sm font-medium text-text-secondary hover:bg-bg-secondary transition-colors shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Login Admin
                </a>
            </div>
        </div>
    </div>
</body>
</html>

