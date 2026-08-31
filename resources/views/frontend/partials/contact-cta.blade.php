@php
    $siteName = App\Helpers\WebsiteSettings::siteName();

    // Build WhatsApp URL using the same logic as CustomFurniture component
    $rawWa = App\Helpers\WebsiteSettings::get('whatsapp') ?: App\Helpers\WebsiteSettings::get('phone');
    $whatsappUrl = null;
    if ($rawWa) {
        $cleanNumber = preg_replace('/[^0-9]/', '', (string) $rawWa);
        if (str_starts_with($cleanNumber, '0')) {
            $cleanNumber = '62' . substr($cleanNumber, 1);
        }
        $message = urlencode('Halo ' . $siteName . ', saya ingin berkonsultasi mengenai produk kebutuhan saya.');
        $whatsappUrl = 'https://wa.me/' . $cleanNumber . '?text=' . $message;
    }
@endphp

<section
    id="contact-cta"
    aria-labelledby="contact-cta-heading"
    class="relative overflow-hidden bg-wood-primary-dark py-20 sm:py-24 lg:py-32"
>
    {{-- Premium ambient blobs --}}
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
        <div class="animate-blob absolute -top-40 -left-40 h-[30rem] w-[30rem] rounded-full bg-wood-secondary/10 blur-3xl"></div>
        <div class="animate-blob absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-wood-primary/20 blur-3xl" style="animation-delay: 5s;"></div>
        {{-- Subtle grain overlay for premium texture --}}
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noise)%22/%3E%3C/svg%3E');"></div>
    </div>

    <div class="relative mx-auto w-full max-w-4xl px-4 text-center sm:px-6 lg:px-8">
        {{-- Badge --}}
        <div
            data-reveal
            class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-white/90 backdrop-blur-sm"
        >
            <span class="text-wood-secondary-light">✦</span>
            Hubungi Kami
        </div>

        {{-- Heading --}}
        <h2
            id="contact-cta-heading"
            data-reveal
            data-reveal-delay="100"
            class="font-serif text-3xl font-bold leading-tight tracking-tight text-white sm:text-4xl lg:text-5xl xl:text-6xl"
        >
            Wujudkan Furniture Impian<br class="hidden sm:block">
            Bersama Kami.
        </h2>

        {{-- Description --}}
        <p
            data-reveal
            data-reveal-delay="200"
            class="mx-auto mt-6 max-w-xl text-base leading-relaxed text-white/75 sm:text-lg"
        >
            Punya kebutuhan furniture tertentu? Konsultasikan desain, ukuran, atau ide furniture impian Anda bersama tim {{ $siteName }}.
        </p>

        {{-- CTA Button --}}
        @if ($whatsappUrl)
            <div
                data-reveal
                data-reveal-delay="300"
                class="mt-10"
            >
                <a
                    href="{{ $whatsappUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    id="contact-cta-button"
                    class="group inline-flex items-center gap-3 rounded-full bg-white px-8 py-4 text-base font-semibold text-wood-primary shadow-xl shadow-black/20 transition-all duration-300 ease-out hover:-translate-y-1 hover:bg-wood-bg hover:shadow-2xl focus:outline-none focus-visible:ring-2 focus-visible:ring-wood-secondary focus-visible:ring-offset-2 focus-visible:ring-offset-wood-primary-dark sm:px-10 sm:py-4 sm:text-base"
                >
                    Mulai Konsultasi
                    <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </a>
            </div>
        @endif
    </div>
</section>
