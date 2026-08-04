@php
    $title = 'Tentang Kami';
    $siteName = App\Helpers\WebsiteSettings::siteName();
    $siteLogo = App\Helpers\WebsiteSettings::logoUrl();
    $profile = app(App\Services\CompanyProfileService::class)->get();
    $stats = collect($profile?->statistics ?? []);
    $hasImage = $profile && $profile->company_image;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ $title }} — {{ $siteName }}</title>

    @if ($siteLogo)
        <link rel="icon" href="{{ $siteLogo }}">
    @else
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @endif

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-bg-primary font-sans text-text-primary antialiased">

    {{-- Simple top bar --}}
    <header class="border-b border-border bg-card">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 lg:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-lg font-bold tracking-tight text-text-primary">
                @if ($siteLogo)
                    <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-8 w-8 object-contain">
                @else
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-sm font-bold text-white">B</span>
                @endif
                <span>{{ $siteName }}</span>
            </a>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-text-secondary hover:text-text-primary transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Beranda
            </a>
        </div>
    </header>

    {{-- Page Hero --}}
    <section class="border-b border-border bg-bg-secondary/50">
        <div class="mx-auto max-w-6xl px-4 py-12 lg:px-6 lg:py-16">
            <h1 class="text-3xl font-bold tracking-tight text-text-primary lg:text-4xl">Tentang Kami</h1>
            <p class="mt-2 max-w-2xl text-sm text-text-secondary lg:text-base">
                {{ $siteName }} — mengenal kami lebih dekat melalui visi, misi, dan perjalanan kami.
            </p>
        </div>
    </section>

    {{-- Content --}}
    <main class="mx-auto max-w-6xl px-4 py-12 lg:px-6 lg:py-16">
        @if ($profile)
            {{-- Layout 2 kolom jika ada foto, 1 kolom jika tidak --}}
            <div class="{{ $hasImage ? 'grid gap-10 lg:grid-cols-2 lg:items-start' : 'grid gap-10' }}">
                @if ($hasImage)
                    <div class="overflow-hidden rounded-xl border border-border bg-card shadow-sm">
                        <img src="{{ asset('storage/' . $profile->company_image) }}"
                             alt="Foto {{ $siteName }}"
                             class="h-full w-full object-cover">
                    </div>
                @endif

                <div class="{{ $hasImage ? '' : 'max-w-3xl' }}">
                    {{-- Tentang Kami --}}
                    <section class="mb-10">
                        <h2 class="mb-4 flex items-center gap-2 text-xl font-bold text-text-primary">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <i class="fa-solid fa-info-circle text-sm"></i>
                            </span>
                            Tentang Kami
                        </h2>
                        <div class="prose prose-neutral max-w-none text-sm leading-relaxed text-text-secondary lg:text-base">
                            {!! nl2br(e($profile->about)) !!}
                        </div>
                    </section>

                    {{-- Visi --}}
                    <section class="mb-10">
                        <h2 class="mb-4 flex items-center gap-2 text-xl font-bold text-text-primary">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </span>
                            Visi
                        </h2>
                        <div class="text-sm leading-relaxed text-text-secondary lg:text-base">
                            {!! nl2br(e($profile->vision)) !!}
                        </div>
                    </section>

                    {{-- Misi --}}
                    <section class="mb-10">
                        <h2 class="mb-4 flex items-center gap-2 text-xl font-bold text-text-primary">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <i class="fa-solid fa-bullseye text-sm"></i>
                            </span>
                            Misi
                        </h2>
                        @if ($profile->missions->count())
                            <ul class="space-y-3">
                                @foreach ($profile->missions as $mission)
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                                            <i class="fa-solid fa-check text-xs"></i>
                                        </span>
                                        <span class="text-sm leading-relaxed text-text-secondary lg:text-base">
                                            {!! nl2br(e($mission->content)) !!}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </section>
                </div>
            </div>

            {{-- Statistik --}}
            @if ($stats->count())
                <section class="mt-14">
                    <h2 class="mb-6 text-center text-xl font-bold text-text-primary lg:text-2xl">Angka &amp; Pencapaian Kami</h2>
                    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                        @foreach ($stats as $stat)
                            <div class="rounded-xl border border-border bg-card p-6 text-center shadow-sm">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                    <i class="{{ $stat->icon }} text-lg"></i>
                                </div>
                                <p class="text-2xl font-bold tracking-tight text-text-primary lg:text-3xl">
                                    {{ $stat->manual_value }}<span class="text-primary">+</span>
                                </p>
                                <p class="mt-1 text-xs font-medium text-text-muted lg:text-sm">{{ $stat->title }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @else
            {{-- Tidak ada profil --}}
            <div class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-border bg-card p-16 text-center shadow-sm">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-primary/10">
                    <i class="fa-solid fa-building text-3xl text-primary"></i>
                </div>
                <h2 class="text-lg font-semibold text-text-primary">Informasi Perusahaan Belum Tersedia</h2>
                <p class="mt-1 max-w-md text-sm text-text-muted">Konten tentang perusahaan akan segera hadir. Silakan kembali lagi nanti.</p>
            </div>
        @endif
    </main>

    {{-- Footer --}}
    <footer class="border-t border-border bg-card">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-4 py-6 text-center text-xs text-text-muted lg:flex-row lg:px-6 lg:text-left">
            <p>&copy; {{ date('Y') }} {{ $siteName }}. Hak Cipta Dilindungi.</p>
            <p>Halaman Tentang Kami</p>
        </div>
    </footer>
</body>
</html>
