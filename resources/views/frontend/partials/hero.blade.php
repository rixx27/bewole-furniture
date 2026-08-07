@php
    use App\Enums\HeroButtonTarget;

    $siteName = App\Helpers\WebsiteSettings::siteName();

    // Resolve button targets from admin data.
    $primaryHref = $hero && $hero->primary_button_link
        ? (HeroButtonTarget::resolve($hero->primary_button_link)?->href() ?? '#')
        : '#';
    $secondaryHref = $hero && $hero->secondary_button_link
        ? (HeroButtonTarget::resolve($hero->secondary_button_link)?->href() ?? '#')
        : '#';

    $hasImage = $hero && $hero->image;
    $imageUrl = $hasImage ? asset('storage/' . $hero->image) : null;

    // Optional mini statistics (static premium flourish).
    $stats = [
        ['value' => '10+', 'label' => 'Tahun Pengalaman'],
        ['value' => '500+', 'label' => 'Produk Terjual'],
        ['value' => '4.9/5', 'label' => 'Rating Pelanggan'],
    ];
@endphp

<section id="home" class="relative flex min-h-screen items-center overflow-hidden pt-28 pb-20 lg:pt-32">
    {{-- ============================================================
         BACKGROUND : light + gradient blobs + subtle pattern
         ============================================================ --}}
    <div class="pointer-events-none absolute inset-0 -z-10">
        {{-- Base light wood background --}}
        <div class="absolute inset-0 bg-wood-bg"></div>

        {{-- Blur gradient blobs --}}
        <div class="animate-blob absolute -top-24 -left-24 h-96 w-96 rounded-full bg-wood-secondary/20 blur-3xl"></div>
        <div class="animate-blob absolute top-1/3 -right-24 h-[28rem] w-[28rem] rounded-full bg-wood-primary/15 blur-3xl" style="animation-delay: 3s;"></div>
        <div class="animate-blob absolute bottom-0 left-1/3 h-80 w-80 rounded-full bg-wood-secondary-light/20 blur-3xl" style="animation-delay: 6s;"></div>

        {{-- Subtle dotted pattern --}}
        <div class="absolute inset-0 opacity-[0.04]"
             style="background-image: radial-gradient(#5B3A29 1px, transparent 1px); background-size: 28px 28px;"></div>
    </div>

    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
            {{-- ============================================================
                 LEFT : Text content
                 ============================================================ --}}
            <div class="text-center lg:text-left">
                {{-- Badge --}}
                @if ($hero?->badge_text)
                    <div class="animate-fade-slide-left mb-6 inline-flex items-center gap-2 rounded-full border border-wood-secondary/40 bg-white/70 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-wood-primary shadow-sm backdrop-blur-sm">
                        <span class="text-wood-secondary">✦</span>
                        {{ $hero->badge_text }}
                    </div>
                @else
                    <div class="animate-fade-slide-left mb-6 inline-flex items-center gap-2 rounded-full border border-wood-secondary/40 bg-white/70 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-wood-primary shadow-sm backdrop-blur-sm">
                        <span class="text-wood-secondary">✦</span>
                        Furniture Berkualitas Sejak 2015
                    </div>
                @endif

                {{-- Title (single H1) --}}
                <h1 class="animate-fade-slide-left text-4xl font-bold leading-tight tracking-tight text-wood-text sm:text-5xl lg:text-6xl" style="animation-delay: 0.1s;">
                    {!! nl2br(e($hero?->title ?? 'Ciptakan Hunian Impian dengan Furniture Premium')) !!}
                </h1>

                {{-- Subtitle --}}
                @if ($hero?->subtitle)
                    <p class="animate-fade-slide-left mx-auto mt-5 max-w-xl text-base leading-relaxed text-wood-muted sm:text-lg lg:mx-0" style="animation-delay: 0.2s;">
                        {{ $hero->subtitle }}
                    </p>
                @endif

                {{-- Buttons --}}
                <div class="animate-fade-slide-left mt-8 flex flex-wrap items-center justify-center gap-4 lg:justify-start" style="animation-delay: 0.3s;">
                    @if ($hero?->primary_button_text)
                        <x-frontend.button :href="$primaryHref" variant="primary" size="lg">
                            {{ $hero->primary_button_text }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                            </svg>
                        </x-frontend.button>
                    @endif

                    @if ($hero?->secondary_button_text)
                        <x-frontend.button :href="$secondaryHref" variant="outline" size="lg">
                            {{ $hero->secondary_button_text }}
                        </x-frontend.button>
                    @endif
                </div>

                {{-- Optional mini statistics --}}
                <div class="animate-fade-slide-left mt-10 grid max-w-md grid-cols-3 gap-4 border-t border-wood-border pt-8 mx-auto lg:mx-0" style="animation-delay: 0.4s;">
                    @foreach ($stats as $stat)
                        <div class="text-center lg:text-left">
                            <p class="text-2xl font-bold text-wood-primary">{{ $stat['value'] }}</p>
                            <p class="mt-1 text-xs text-wood-muted">{{ $stat['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ============================================================
                 RIGHT : Image with floating animation
                 ============================================================ --}}
            <div class="animate-fade-slide-right relative" style="animation-delay: 0.2s;">
                <div class="animate-hero-float relative">
                    {{-- Decorative frame --}}
                    <div class="absolute -inset-4 -z-10 rounded-[2.5rem] bg-gradient-to-br from-wood-secondary/30 via-transparent to-wood-primary/20 blur-xl"></div>

                    {{-- Image or placeholder --}}
                    <div class="relative overflow-hidden rounded-[2rem] border border-white/60 shadow-2xl shadow-wood-primary/20">
                        @if ($hasImage)
                            <img
                                src="{{ $imageUrl }}"
                                alt="{{ $hero->title }}"
                                class="h-[26rem] w-full object-cover sm:h-[30rem] lg:h-[34rem]"
                                loading="eager"
                            >
                        @else
                            {{-- Elegant placeholder --}}
                            <div class="flex h-[26rem] w-full flex-col items-center justify-center bg-gradient-to-br from-wood-secondary-light/40 to-wood-bg p-10 text-center sm:h-[30rem] lg:h-[34rem]">
                                <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-3xl bg-white/70 shadow-lg backdrop-blur-sm">
                                    <svg class="h-12 w-12 text-wood-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125"/>
                                    </svg>
                                </div>
                                <p class="text-lg font-semibold text-wood-text">{{ $siteName }}</p>
                                <p class="mt-2 max-w-xs text-sm text-wood-muted">Tambahkan gambar hero melalui dashboard admin untuk menampilkan visual yang menarik.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Floating accent card --}}
                    <div class="absolute -bottom-5 -left-5 hidden rounded-2xl border border-white/60 bg-white/80 px-5 py-4 shadow-xl backdrop-blur-sm sm:block">
                        <p class="text-2xl font-bold text-wood-primary">100%</p>
                        <p class="text-xs font-medium text-wood-muted">Kayu Pilihan</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             SCROLL INDICATOR
             ============================================================ --}}
        <div class="mt-16 flex justify-center">
            <a href="#products" class="group flex flex-col items-center gap-2 text-wood-muted transition-colors hover:text-wood-primary" aria-label="Gulir ke bawah">
                <span class="text-xs font-medium uppercase tracking-widest">Scroll</span>
                <span class="animate-scroll-bounce flex h-10 w-6 items-start justify-center rounded-full border-2 border-current p-1.5">
                    <span class="h-2 w-1 rounded-full bg-current"></span>
                </span>
            </a>
        </div>
    </div>
</section>
