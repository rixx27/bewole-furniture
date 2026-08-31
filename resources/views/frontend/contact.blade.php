@php
    $siteName = App\Helpers\WebsiteSettings::siteName();
    $address = App\Helpers\WebsiteSettings::get('address') ?: 'Tahunan, Kec. Tahunan, Kabupaten Jepara, Jawa Tengah 59427';
    $email = App\Helpers\WebsiteSettings::get('email') ?: 'info@bewolefurniture.com';
    $phone = App\Helpers\WebsiteSettings::get('phone') ?: '081234567890';
    $whatsapp = App\Helpers\WebsiteSettings::get('whatsapp') ?: $phone;
    $googleMapsEmbedUrl = App\Helpers\WebsiteSettings::googleMapsEmbedUrl();

    $cleanWa = preg_replace('/[^0-9]/', '', (string) $whatsapp);
    if (str_starts_with($cleanWa, '0')) {
        $cleanWa = '62' . substr($cleanWa, 1);
    }
    $waUrl = 'https://wa.me/' . $cleanWa . '?text=' . urlencode('Halo ' . $siteName . ', saya ingin bertanya mengenai produk dan layanan custom furniture.');

    $mapsDirectUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($address . ' Jepara');
@endphp

@extends('frontend.layouts.app')

@section('title', 'Hubungi Kami')

@section('content')
    {{-- ============================================================
         PAGE HERO
         ============================================================ --}}
    <section class="relative overflow-hidden bg-wood-primary-dark pt-44 pb-20 sm:pt-48 lg:pt-52 lg:pb-24">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="animate-blob absolute -top-24 -left-24 h-96 w-96 rounded-full bg-wood-secondary/20 blur-3xl"></div>
            <div class="animate-blob absolute bottom-0 right-0 h-[28rem] w-[28rem] rounded-full bg-wood-primary/30 blur-3xl" style="animation-delay: 3s;"></div>
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noise)%22/%3E%3C/svg%3E');"></div>
        </div>

        <div class="relative mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="mb-6 flex items-center gap-2 text-xs text-white/80">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
                <span>/</span>
                <span class="font-semibold text-white">Kontak</span>
            </nav>

            <div class="max-w-3xl">
                <span class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-white backdrop-blur-sm">
                    <span class="text-wood-secondary-light">✦</span>
                    Hubungi & Kunjungi Workshop
                </span>
                <h1 class="font-serif text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Hubungi {{ $siteName }}
                </h1>
                <p class="mt-4 max-w-2xl text-sm leading-relaxed text-white/85 sm:text-base">
                    Konsultasikan kebutuhan furniture impian Anda, diskusikan desain kustom bersama ahli pengrajin kayu kami, atau kunjungi workshop langsung di Jepara.
                </p>
            </div>
        </div>
    </section>

    {{-- ============================================================
         CONTACT CARDS & DETAILS
         ============================================================ --}}
    <section class="bg-wood-bg py-16 sm:py-20 lg:py-24">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-3">
                
                {{-- Card 1: Workshop & Alamat --}}
                <div class="rounded-3xl border border-wood-border/60 bg-white p-8 shadow-xl shadow-wood-primary/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-wood-light/60 text-wood-primary mb-6">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.5 10.5c0 6.5-7.5 12-7.5 12s-7.5-5.5-7.5-12a7.5 7.5 0 1115 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-wood-text">Lokasi Workshop</h2>
                    <p class="mt-2 text-xs text-wood-muted">Pusat produksi kayu jati Jepara:</p>
                    <p class="mt-3 text-sm font-medium leading-relaxed text-wood-text">{{ $address }}</p>
                    <div class="mt-6 pt-6 border-t border-wood-border/40">
                        <a
                            href="{{ $mapsDirectUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 text-xs font-bold text-wood-primary hover:text-wood-primary-dark transition-colors"
                        >
                            Buka di Google Maps
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Card 2: WhatsApp & Telepon --}}
                <div class="rounded-3xl border border-wood-border/60 bg-white p-8 shadow-xl shadow-wood-primary/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 mb-6">
                        <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-wood-text">WhatsApp & Telepon</h2>
                    <p class="mt-2 text-xs text-wood-muted">Respon cepat setiap hari kerja:</p>
                    <div class="mt-6 pt-6 border-t border-wood-border/40">
                        <a
                            href="{{ $waUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition-colors"
                        >
                            Chat via WhatsApp
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Card 3: Jam Operasional --}}
                <div class="rounded-3xl border border-wood-border/60 bg-white p-8 shadow-xl shadow-wood-primary/5 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-wood-light/60 text-wood-primary mb-6">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-wood-text">Jam Operasional</h2>
                    <p class="mt-2 text-xs text-wood-muted">Kami siap melayani kebutuhan furniture Anda selama jam operasional.</p>
                    <div class="mt-6 space-y-3 border-t border-wood-border/40 pt-5">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-wood-text">Senin – Sabtu</span>
                            <span class="font-semibold text-wood-primary">08:00 – 17:00 WIB</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-wood-text">Minggu</span>
                            <span class="font-semibold text-red-500">Tutup</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ============================================================
                 GOOGLE MAPS EMBED SECTION
                 ============================================================ --}}
            @if ($googleMapsEmbedUrl)
                <div class="mt-12 overflow-hidden rounded-3xl border border-wood-border/60 bg-white p-6 shadow-xl shadow-wood-primary/5 sm:p-8">
                    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <span class="inline-flex items-center gap-2 rounded-full border border-wood-border bg-wood-light/50 px-3 py-1 text-[11px] font-bold text-wood-primary">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Peta Lokasi Workshop
                            </span>
                            <h2 class="mt-2 font-serif text-xl font-bold text-wood-text sm:text-2xl">
                                Kunjungi Galeri & Workshop Kami
                            </h2>
                            <p class="mt-1 text-xs leading-relaxed text-wood-muted sm:text-sm">
                                Terbuka untuk konsultasi langsung, peninjauan material kayu jati, dan pengerjaan ukiran custom di Jepara.
                            </p>
                        </div>

                        <a
                            href="{{ $mapsDirectUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-full bg-wood-primary px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-wood-primary-dark transition-all"
                        >
                            <span>Buka di Aplikasi Google Maps</span>
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>

                    <div class="relative aspect-[16/10] sm:aspect-[21/9] w-full overflow-hidden rounded-2xl border border-wood-border/40 bg-wood-light/40 shadow-inner">
                        <iframe
                            src="{{ $googleMapsEmbedUrl }}"
                            width="100%"
                            height="100%"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            class="h-full w-full"
                            title="Peta Lokasi {{ $siteName }}"
                        ></iframe>
                    </div>
                </div>
            @endif

        </div>
    </section>

    {{-- ============================================================
         CTA SECTION
         ============================================================ --}}
    @include('frontend.partials.contact-cta')
@endsection
