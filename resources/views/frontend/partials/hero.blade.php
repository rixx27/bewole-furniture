@php
    use App\Enums\HeroButtonTarget;

    $siteName = App\Helpers\WebsiteSettings::siteName();

    // Resolve button targets dynamically from admin data.
    // Empty/unknown targets resolve to null → button is hidden.
    $primaryHref = $hero?->primary_button_link
        ? HeroButtonTarget::resolveHref($hero->primary_button_link)
        : null;
    $secondaryHref = $hero?->secondary_button_link
        ? HeroButtonTarget::resolveHref($hero->secondary_button_link)
        : null;

    $hasImage = $hero && $hero->image;
    $imageUrl = $hasImage ? asset('storage/' . $hero->image) : null;
@endphp

<section
    id="home"
    class="relative flex min-h-screen w-full items-center overflow-hidden bg-wood-primary-dark"
>
    {{-- ============================================================
         BACKGROUND : Full-width banner image (dari Admin Hero)
         ============================================================ --}}
    @if ($hasImage)
        <div
            class="animate-hero-kenburns absolute inset-0 h-full w-full bg-cover bg-center bg-no-repeat"
            style="background-image: url('{{ $imageUrl }}');"
            role="img"
            aria-label="{{ $hero->title }}"
        ></div>

        {{-- Text readability overlay (gradient, tidak terlalu gelap) --}}
        <div
            class="absolute inset-0 bg-gradient-to-r from-black/65 via-black/45 to-black/15"
            style="background: linear-gradient(90deg, rgba(0,0,0,.65) 0%, rgba(0,0,0,.45) 40%, rgba(0,0,0,.15) 70%, rgba(0,0,0,.05) 100%);"
        ></div>
    @else
        {{-- Fallback background bila admin belum upload gambar --}}
        <div class="absolute inset-0 bg-wood-bg">
            <div class="animate-blob absolute -top-24 -left-24 h-96 w-96 rounded-full bg-wood-secondary/25 blur-3xl"></div>
            <div class="animate-blob absolute bottom-0 right-0 h-[28rem] w-[28rem] rounded-full bg-wood-primary/20 blur-3xl" style="animation-delay: 3s;"></div>
        </div>
    @endif

    {{-- ============================================================
         CONTENT : kiri, vertical center, lebar 500–650px
         ============================================================ --}}
    <div class="relative z-10 mx-auto w-full max-w-7xl px-6 sm:px-8 lg:px-12">
<div class="max-w-xl lg:max-w-[650px] pt-32 pb-24 lg:pt-28 lg:pb-24">
            {{-- Badge --}}
            <div class="animate-hero-badge mb-6 inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-white backdrop-blur-sm">
                <span class="text-wood-secondary-light">✦</span>
                {{ $hero?->badge_text ?? 'Furniture Premium' }}
            </div>

            {{-- Title: besar, serif, putih --}}
<h1 class="animate-hero-up max-w-xs font-serif text-4xl font-bold leading-[1.15] tracking-normal text-white sm:max-w-lg sm:text-5xl lg:max-w-3xl lg:leading-[1.1] lg:tracking-tight lg:text-7xl" style="animation-delay: 0.1s;">
                {!! nl2br(e($hero?->title ?? 'Ciptakan Hunian Impian dengan Furniture Premium')) !!}
            </h1>

            {{-- Description --}}
            @if ($hero?->subtitle)
<p class="animate-hero-up mt-6 max-w-[320px] text-sm leading-relaxed text-white/90 sm:max-w-xl sm:text-base" style="animation-delay: 0.2s;">
                    {{ $hero->subtitle }}
                </p>
            @endif

            {{-- Buttons --}}
<div class="animate-hero-zoom mt-9 flex flex-col gap-3.5 sm:flex-row sm:items-center sm:gap-4" style="animation-delay: 0.3s;">
                @if ($hero?->primary_button_text && $primaryHref)
<x-frontend.button :href="$primaryHref" variant="primary" size="lg" class="mx-auto w-full max-w-[300px] sm:mx-0 sm:w-auto sm:max-w-none">
                        {{ $hero->primary_button_text }}
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </x-frontend.button>
                @endif

                @if ($hero?->secondary_button_text && $secondaryHref)
<x-frontend.button :href="$secondaryHref" variant="outline-light" size="lg" class="mx-auto w-full max-w-[300px] sm:mx-0 sm:w-auto sm:max-w-none">
                        {{ $hero->secondary_button_text }}
                    </x-frontend.button>
                @endif
            </div>
        </div>
    </div>

    {{-- ============================================================
         SCROLL INDICATOR : floating, klik → scroll ke section
         ============================================================ --}}
    <div class="absolute bottom-8 left-1/2 z-10 -translate-x-1/2">
        <a href="#products" class="group flex flex-col items-center gap-2 text-white/80 transition-colors hover:text-white" aria-label="Gulir ke bawah">
            <span class="animate-scroll-bounce flex h-10 w-6 items-start justify-center rounded-full border-2 border-current p-1.5">
                <span class="h-2 w-1 rounded-full bg-current"></span>
            </span>
            <span class="text-[11px] font-medium uppercase tracking-[0.2em]">Scroll</span>
        </a>
    </div>
</section>
