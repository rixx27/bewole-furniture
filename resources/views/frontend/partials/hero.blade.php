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

    // Position mapping & layout classes based on admin setting
    $position = $hero?->text_position ?? 'center';
    $opacity = ($hero?->overlay_opacity ?? 40) / 100;

    $positionMap = [
        'left' => [
            'wrapper' => 'justify-start',
            'box' => 'mr-auto items-start text-left',
            'badge' => 'self-start',
            'title' => 'text-left',
            'subtitle' => 'text-left mr-auto',
            'buttons' => 'justify-start sm:justify-start items-center sm:items-start',
            'button_item' => 'mx-auto sm:mx-0',
            'overlay_class' => 'bg-gradient-to-r from-black/75 via-black/50 to-black/20',
            'overlay_style' => 'background: linear-gradient(90deg, rgba(0,0,0,' . min(1, $opacity * 1.5) . ') 0%, rgba(0,0,0,' . min(1, $opacity * 1.1) . ') 45%, rgba(0,0,0,' . min(1, $opacity * 0.4) . ') 80%, rgba(0,0,0,0.05) 100%);',
        ],
        'center' => [
            'wrapper' => 'justify-center',
            'box' => 'mx-auto items-center text-center',
            'badge' => 'self-center mx-auto',
            'title' => 'text-center mx-auto',
            'subtitle' => 'text-center mx-auto',
            'buttons' => 'justify-center sm:justify-center items-center sm:items-center',
            'button_item' => 'mx-auto sm:mx-0',
            'overlay_class' => 'bg-gradient-to-b from-black/60 via-black/50 to-black/70',
            'overlay_style' => 'background: linear-gradient(180deg, rgba(0,0,0,' . min(1, $opacity * 1.3) . ') 0%, rgba(0,0,0,' . min(1, $opacity * 1.1) . ') 50%, rgba(0,0,0,' . min(1, $opacity * 1.4) . ') 100%);',
        ],
        'right' => [
            'wrapper' => 'justify-end',
            'box' => 'ml-auto items-end text-right',
            'badge' => 'self-end ml-auto',
            'title' => 'text-right ml-auto',
            'subtitle' => 'text-right ml-auto',
            'buttons' => 'justify-end sm:justify-end items-center sm:items-end',
            'button_item' => 'mx-auto sm:ml-auto sm:mr-0',
            'overlay_class' => 'bg-gradient-to-l from-black/75 via-black/50 to-black/20',
            'overlay_style' => 'background: linear-gradient(270deg, rgba(0,0,0,' . min(1, $opacity * 1.5) . ') 0%, rgba(0,0,0,' . min(1, $opacity * 1.1) . ') 45%, rgba(0,0,0,' . min(1, $opacity * 0.4) . ') 80%, rgba(0,0,0,0.05) 100%);',
        ],
    ];

    $layout = $positionMap[$position] ?? $positionMap['center'];
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

        {{-- Text readability overlay (gradient dinamis sesuai posisi dan opacity) --}}
        <div
            class="absolute inset-0 {{ $layout['overlay_class'] }}"
            style="{{ $layout['overlay_style'] }}"
        ></div>
    @else
        {{-- Fallback background bila admin belum upload gambar --}}
        <div class="absolute inset-0 bg-wood-bg">
            <div class="animate-blob absolute -top-24 -left-24 h-96 w-96 rounded-full bg-wood-secondary/25 blur-3xl"></div>
            <div class="animate-blob absolute bottom-0 right-0 h-[28rem] w-[28rem] rounded-full bg-wood-primary/20 blur-3xl" style="animation-delay: 3s;"></div>
        </div>
    @endif

    {{-- ============================================================
         CONTENT : Posisi dinamis (kiri, tengah, kanan) sesuai Admin
         ============================================================ --}}
    <div class="relative z-10 mx-auto flex w-full max-w-7xl px-6 sm:px-8 lg:px-12 {{ $layout['wrapper'] }}">
        <div class="flex w-full max-w-xl flex-col pt-32 pb-24 lg:max-w-[650px] lg:pt-28 lg:pb-24 {{ $layout['box'] }}">
            {{-- Badge --}}
            <div class="animate-hero-badge mb-6 inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-white backdrop-blur-sm {{ $layout['badge'] }}">
                <span class="text-wood-secondary-light">✦</span>
                {{ $hero?->badge_text ?? 'Furniture Premium' }}
            </div>

            {{-- Title: besar, serif, putih --}}
            <h1 class="animate-hero-up max-w-xs font-serif text-4xl font-bold leading-[1.15] tracking-normal text-white sm:max-w-lg sm:text-5xl lg:max-w-3xl lg:leading-[1.1] lg:tracking-tight lg:text-7xl {{ $layout['title'] }}" style="animation-delay: 0.1s;">
                {!! nl2br(e($hero?->title ?? 'Ciptakan Hunian Impian dengan Furniture Premium')) !!}
            </h1>

            {{-- Description --}}
            @if ($hero?->subtitle)
                <p class="animate-hero-up mt-6 max-w-[320px] text-sm leading-relaxed text-white/90 sm:max-w-xl sm:text-base {{ $layout['subtitle'] }}" style="animation-delay: 0.2s;">
                    {{ $hero->subtitle }}
                </p>
            @endif

            {{-- Buttons --}}
            <div class="animate-hero-zoom mt-9 flex w-full flex-col gap-3.5 sm:flex-row sm:gap-4 {{ $layout['buttons'] }}" style="animation-delay: 0.3s;">
                @if ($hero?->primary_button_text && $primaryHref)
                    <x-frontend.button :href="$primaryHref" variant="primary" size="lg" class="w-full max-w-[300px] sm:w-auto sm:max-w-none {{ $layout['button_item'] }}">
                        {{ $hero->primary_button_text }}
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </x-frontend.button>
                @endif

                @if ($hero?->secondary_button_text && $secondaryHref)
                    <x-frontend.button :href="$secondaryHref" variant="outline-light" size="lg" class="w-full max-w-[300px] sm:w-auto sm:max-w-none {{ $layout['button_item'] }}">
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
