@php
    $siteName = App\Helpers\WebsiteSettings::siteName();
    $hasImage = $profile && $profile->company_image;
    $imageUrl = $hasImage ? asset('storage/' . $profile->company_image) : null;
    $hasAbout = $profile && filled($profile->about);
@endphp

<section
    id="philosophy"
    aria-labelledby="philosophy-heading"
    class="relative overflow-hidden bg-wood-bg py-20 sm:py-24 lg:py-28"
>
    {{-- Ambient background accents (premium, subtle) --}}
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
        <div class="animate-blob absolute -top-32 -left-32 h-96 w-96 rounded-full bg-wood-secondary/15 blur-3xl"></div>
        <div class="animate-blob absolute -bottom-40 -right-32 h-[28rem] w-[28rem] rounded-full bg-wood-primary/10 blur-3xl" style="animation-delay: 4s;"></div>
    </div>

    <div class="relative mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- ============================================================
             EDITORIAL TWO-COLUMN LAYOUT
             Desktop/Tablet : Teks kiri | Foto kanan
             Mobile         : Satu kolom (teks → foto)
             ============================================================ --}}
        <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
            {{-- LEFT : Text --}}
            <div
                data-reveal-side="left"
                class="order-1"
            >
                {{-- Label --}}
                <span
                    class="mb-5 inline-flex items-center gap-2 rounded-full border border-wood-primary/15 bg-wood-primary/5 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-wood-primary"
                >
                    <span class="text-wood-secondary">✦</span>
                    {{ $badge }}
                </span>

                {{-- Heading --}}
                <h2
                    id="philosophy-heading"
                    class="font-serif text-3xl font-bold tracking-tight text-wood-text sm:text-4xl lg:text-5xl"
                >
                    {{ $heading }}
                </h2>

                {{-- Tentang Kami (About) --}}
                @if ($hasAbout)
                    <div class="mt-6 max-w-xl space-y-4 text-base leading-relaxed text-wood-muted sm:text-lg">
                        {!! nl2br(e($profile->about)) !!}
                    </div>
                @else
                    <p class="mt-6 max-w-xl text-base leading-relaxed text-wood-muted sm:text-lg">
                        {{ $siteName }} menghadirkan furniture Jepara premium yang dibuat dengan tangan, material terpilih, dan detail yang penuh ketelitian.
                    </p>
                @endif

                {{-- Button --}}
                <div class="mt-8">
                    <a
                        href="{{ route('frontend.about') }}"
                        class="group inline-flex items-center gap-2 rounded-full bg-wood-primary px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-wood-primary/20 transition-all duration-300 ease-out hover:-translate-y-0.5 hover:bg-wood-primary-dark focus:outline-none focus-visible:ring-2 focus-visible:ring-wood-secondary focus-visible:ring-offset-2"
                    >
                        {{ $buttonText }}
                        <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- RIGHT : Company Photo --}}
            <div
                data-reveal-side="right"
                class="order-2"
            >
                <div class="group relative aspect-[4/5] w-full max-w-md overflow-hidden rounded-[2rem] border border-wood-border/60 bg-wood-surface shadow-xl shadow-wood-primary/10 sm:max-w-lg lg:ml-auto">
                    @if ($hasImage)
                        <img
                            src="{{ $imageUrl }}"
                            alt="Foto {{ $siteName }}"
                            loading="lazy"
                            class="philosophy-image h-full w-full object-cover object-center"
                        >
                    @else
                        <div
                            class="flex h-full w-full items-center justify-center bg-gradient-to-br from-wood-primary/80 to-wood-primary-dark"
                            aria-hidden="true"
                        >
                            <span class="font-serif text-7xl font-bold text-white/25">{{ strtoupper(mb_substr($siteName, 0, 1)) }}</span>
                        </div>
                    @endif

                    {{-- Subtle bottom gradient overlay for premium depth --}}
                    <div class="absolute inset-x-0 bottom-0 h-2/5 bg-gradient-to-t from-black/25 to-transparent opacity-60" aria-hidden="true"></div>
                </div>
            </div>
        </div>
    </div>
</section>
