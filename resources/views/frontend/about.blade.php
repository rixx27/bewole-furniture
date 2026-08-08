@php
    $siteName = App\Helpers\WebsiteSettings::siteName();
    $profile = app(App\Services\CompanyProfileService::class)->get();
    $stats = collect($profile?->statistics ?? []);
    $hasImage = $profile && $profile->company_image;
    $imageUrl = $hasImage ? asset('storage/' . $profile->company_image) : null;

    // ============================================================
    // STATUS ICONS
    // Database menyimpan identifier (class Font Awesome, mis. "fa-solid fa-briefcase").
    // Project ini tidak memuat Font Awesome — ia memakai inline SVG (Heroicons outline).
    // Pemetaan identifier → path SVG agar icon dirender konsisten dengan desain Bewole.
    // ============================================================
    $statIconPaths = [
        'fa-solid fa-briefcase' => 'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z',
        'fa-solid fa-users' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
        'fa-solid fa-calendar-check' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z',
        'fa-solid fa-city' => 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21',
    ];

    // Fallback aman jika identifier icon kosong/null/tidak dikenal —
    // pastikan card tetap menampilkan icon, tidak pernah kotak kosong.
    $statFallbackPath = 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zM15.75 17.25h-7.5m7.5-3h-7.5';

    $statIconPath = fn ($icon) => $statIconPaths[$icon] ?? $statFallbackPath;
@endphp

@extends('frontend.layouts.app')

@section('title', 'Tentang Kami')

@section('content')
    {{-- ============================================================
         PAGE HERO (breadcrumb-style)
         ============================================================ --}}
    <section class="relative overflow-hidden bg-wood-primary-dark pt-36 pb-20 sm:pt-40 lg:pt-44 lg:pb-24">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="animate-blob absolute -top-24 -left-24 h-96 w-96 rounded-full bg-wood-secondary/20 blur-3xl"></div>
            <div class="animate-blob absolute bottom-0 right-0 h-[28rem] w-[28rem] rounded-full bg-wood-primary/30 blur-3xl" style="animation-delay: 3s;"></div>
        </div>

        <div class="relative mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <span class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-white backdrop-blur-sm">
                <span class="text-wood-secondary-light">✦</span>
                Tentang Perusahaan
            </span>
            <h1 class="font-serif text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                Tentang Kami
            </h1>
            <p class="mt-4 max-w-2xl text-sm leading-relaxed text-white/85 sm:text-base">
                {{ $siteName }} — mengenal kami lebih dekat melalui cerita, visi, dan misi kami.
            </p>
        </div>
    </section>

    {{-- ============================================================
         CONTENT
         ============================================================ --}}
    <section class="bg-wood-bg py-16 sm:py-20 lg:py-24">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            @if ($profile)
                {{-- Photo (full width, editorial) --}}
                @if ($hasImage)
                    <div data-reveal class="group relative aspect-[16/9] w-full overflow-hidden rounded-[2rem] border border-wood-border/60 bg-wood-surface shadow-xl shadow-wood-primary/10 sm:aspect-[21/9]">
                        <img
                            src="{{ $imageUrl }}"
                            alt="Foto {{ $siteName }}"
                            loading="lazy"
                            class="h-full w-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-[1.03]"
                        >
                    </div>
                @endif

                <div class="mt-12 grid gap-10 lg:grid-cols-2 lg:gap-16">
                    {{-- Tentang Kami --}}
                    <section data-reveal>
                        <h2 class="font-serif text-2xl font-bold tracking-tight text-wood-text sm:text-3xl">
                            Tentang Kami
                        </h2>
                        <div class="mt-4 h-0.5 w-16 rounded-full bg-wood-secondary"></div>
                        <div class="mt-6 space-y-4 text-base leading-relaxed text-wood-muted sm:text-lg">
                            {!! nl2br(e($profile->about)) !!}
                        </div>
                    </section>

                    <div class="space-y-10">
                        {{-- Visi --}}
                        <section data-reveal>
                            <h2 class="font-serif text-2xl font-bold tracking-tight text-wood-text sm:text-3xl">
                                Visi
                            </h2>
                            <div class="mt-4 h-0.5 w-16 rounded-full bg-wood-secondary"></div>
                            <div class="mt-6 text-base leading-relaxed text-wood-muted sm:text-lg">
                                {!! nl2br(e($profile->vision)) !!}
                            </div>
                        </section>

                        {{-- Misi --}}
                        <section data-reveal>
                            <h2 class="font-serif text-2xl font-bold tracking-tight text-wood-text sm:text-3xl">
                                Misi
                            </h2>
                            <div class="mt-4 h-0.5 w-16 rounded-full bg-wood-secondary"></div>
                            @if ($profile->missions->count())
                                <ul class="mt-6 space-y-4">
                                    @foreach ($profile->missions as $mission)
                                        <li class="flex items-start gap-4">
                                            <span class="mt-1 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-wood-primary/10 text-wood-primary">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/>
                                                </svg>
                                            </span>
                                            <span class="text-base leading-relaxed text-wood-muted sm:text-lg">
                                                {!! nl2br(e($mission->content)) !!}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mt-6 text-base leading-relaxed text-wood-muted sm:text-lg">
                                    Misi perusahaan akan segera dilengkapi.
                                </p>
                            @endif
                        </section>
                    </div>
                </div>

                {{-- Statistik --}}
                @if ($stats->count())
                    <section class="mt-16">
                        <h2 class="text-center font-serif text-2xl font-bold tracking-tight text-wood-text sm:text-3xl">
                            Angka &amp; Pencapaian Kami
                        </h2>
<div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6">
                            @foreach ($stats as $stat)
                                <div
                                    data-reveal
                                    data-reveal-delay="{{ $loop->index * 100 }}"
                                    class="flex flex-col items-center rounded-3xl border border-wood-border/60 bg-wood-surface p-6 text-center shadow-sm"
                                >
                                    <div class="mb-4 flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-wood-primary/10 text-wood-primary">
                                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $statIconPath($stat->icon) }}"/>
                                        </svg>
                                    </div>
                                    <p class="text-2xl font-bold tracking-tight text-wood-text lg:text-3xl">
                                        {{ $stat->manual_value }}<span class="text-wood-primary">+</span>
                                    </p>
                                    <p class="mt-1 text-xs font-medium text-wood-muted lg:text-sm">{{ $stat->title }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- CTA --}}
                <div class="mt-16 text-center">
                    <a
                        href="{{ route('home') }}"
                        class="group inline-flex items-center gap-2 rounded-full bg-wood-primary px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-wood-primary/20 transition-all duration-300 ease-out hover:-translate-y-0.5 hover:bg-wood-primary-dark focus:outline-none focus-visible:ring-2 focus-visible:ring-wood-secondary focus-visible:ring-offset-2"
                    >
                        Kembali ke Beranda
                        <svg class="h-4 w-4 transition-transform duration-300 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                        </svg>
                    </a>
                </div>
            @else
                {{-- Empty state --}}
                <div class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-wood-border bg-wood-surface p-16 text-center shadow-sm">
                    <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-wood-primary/10 text-wood-primary">
                        <svg class="h-9 w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m3-9h.75m-.75 3h.75m3-6h.75m-.75 3h.75m3-6h.75m-.75 3h.75m3-6h.75m-.75 3h.75"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-wood-text">Informasi Perusahaan Belum Tersedia</h2>
                    <p class="mt-1 max-w-md text-sm text-wood-muted">Konten tentang perusahaan akan segera hadir. Silakan kembali lagi nanti.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
