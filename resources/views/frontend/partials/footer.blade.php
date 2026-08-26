@php
    $siteName = App\Helpers\WebsiteSettings::siteName();
    $siteLogo = App\Helpers\WebsiteSettings::logoUrl();
    $siteTagline = App\Helpers\WebsiteSettings::get('site_tagline');
    $address = App\Helpers\WebsiteSettings::get('address');
    $email = App\Helpers\WebsiteSettings::get('email');
    $phone = App\Helpers\WebsiteSettings::get('phone');
    $whatsapp = App\Helpers\WebsiteSettings::get('whatsapp');

    // Build WhatsApp URL
    $whatsappUrl = null;
    if ($whatsapp) {
        $cleanNumber = preg_replace('/[^0-9]/', '', (string) $whatsapp);
        if (str_starts_with($cleanNumber, '0')) {
            $cleanNumber = '62' . substr($cleanNumber, 1);
        }
        $whatsappUrl = 'https://wa.me/' . $cleanNumber;
    }

    $socials = collect([
        'facebook' => App\Helpers\WebsiteSettings::get('facebook'),
        'instagram' => App\Helpers\WebsiteSettings::get('instagram'),
        'tiktok' => App\Helpers\WebsiteSettings::get('tiktok'),
    ])->filter();

    $socialIcons = [
        'facebook' => 'M13.5 21v-7.5h2.5l.5-3h-3V8.5c0-.9.3-1.5 1.6-1.5H16.5V4.2c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.5-4 4.2V10.5H7.5v3H10V21',
        'instagram' => 'M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.8.2 2.2.4.6.2 1 .5 1.4.9.4.4.7.8.9 1.4.2.4.4 1 .4 2.2.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.2 1.8-.4 2.2-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.2-1 .4-2.2.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.8-.2-2.2-.4-.6-.2-1-.5-1.4-.9-.4-.4-.7-.8-.9-1.4-.2-.4-.4-1-.4-2.2C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c.1-1.2.2-1.8.4-2.2.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.2 1-.4 2.2-.4C8.4 2.2 8.8 2.2 12 2.2zm0 3.6a6.2 6.2 0 100 12.4 6.2 6.2 0 000-12.4zm0 10.2a4 4 0 110-8 4 4 0 010 8zm6.4-10.4a1.4 1.4 0 11-2.9 0 1.4 1.4 0 012.9 0z',
        'tiktok' => 'M16.6 5.8c.8 1 1.9 1.7 3.2 1.9v3c-1.3 0-2.5-.4-3.5-1.1v5.1c0 3-2.4 5.4-5.4 5.4S5.5 17.7 5.5 14.7c0-3 2.4-5.4 5.4-5.4.3 0 .6 0 .9.1v3.1c-.3-.1-.6-.2-.9-.2-1.3 0-2.3 1-2.3 2.3s1 2.3 2.3 2.3 2.3-1 2.3-2.3V2h3c.2 1.5 1 2.8 2.4 3.8z',
    ];

    // Explore links — existing routes only
    $exploreLinks = [
        ['label' => 'Produk', 'route' => 'products.index'],
        ['label' => 'Tentang Kami', 'route' => 'frontend.about'],
        ['label' => 'Tracking', 'route' => 'frontend.tracking'],
    ];
@endphp

<footer class="mt-auto border-t border-wood-border bg-[#1C1917]">
    <div class="mx-auto w-full max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
        <div
            data-reveal
            class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4"
        >
            {{-- ============================================================
                 COLUMN 1 — BRAND
                 ============================================================ --}}
            <div class="sm:col-span-2 lg:col-span-1">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3" aria-label="{{ $siteName }}">
                    @if ($siteLogo)
                        <img
                            src="{{ $siteLogo }}"
                            alt="Logo {{ $siteName }}"
                            class="h-11 w-11 rounded-full object-contain"
                        >
                    @else
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-wood-primary text-lg font-bold text-white">
                            {{ strtoupper(substr($siteName, 0, 1)) }}
                        </span>
                    @endif
                    <span class="text-xl font-bold tracking-tight text-white">{{ $siteName }}</span>
                </a>

                @if ($siteTagline)
                    <p class="mt-4 max-w-xs text-sm leading-relaxed text-white/50">{{ $siteTagline }}</p>
                @endif
            </div>

            {{-- ============================================================
                 COLUMN 2 — EXPLORE
                 ============================================================ --}}
            <div data-reveal data-reveal-delay="100">
                <h4 class="text-xs font-bold uppercase tracking-[0.15em] text-white/40">Explore</h4>
                <ul class="mt-5 space-y-3.5">
                    @foreach ($exploreLinks as $link)
                        <li>
                            <a
                                href="{{ isset($link['route']) ? route($link['route']) : url($link['hash']) }}"
                                class="group inline-flex items-center gap-2 text-sm text-white/65 transition-colors duration-200 hover:text-wood-secondary-light focus:outline-none focus-visible:text-wood-secondary-light"
                            >
                                <span class="h-px w-3 bg-white/25 transition-all duration-300 group-hover:w-5 group-hover:bg-wood-secondary-light"></span>
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- ============================================================
                 COLUMN 3 — CONTACT
                 ============================================================ --}}
            <div data-reveal data-reveal-delay="200">
                <h4 class="text-xs font-bold uppercase tracking-[0.15em] text-white/40">Kontak</h4>
                <ul class="mt-5 space-y-3.5 text-sm text-white/65">
                    @if ($whatsappUrl)
                        <li class="flex items-center gap-3">
                            {{-- WhatsApp icon --}}
                            <svg class="h-4 w-4 shrink-0 text-white/35" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <a
                                href="{{ $whatsappUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="transition-colors duration-200 hover:text-wood-secondary-light"
                            >
                                WhatsApp
                            </a>
                        </li>
                    @endif

                    @if ($email)
                        <li class="flex items-center gap-3">
                            <svg class="h-4 w-4 shrink-0 text-white/35" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
                            <a
                                href="mailto:{{ $email }}"
                                class="break-all transition-colors duration-200 hover:text-wood-secondary-light"
                            >
                                {{ $email }}
                            </a>
                        </li>
                    @endif

                    @if ($address)
                        <li class="flex items-start gap-3">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-white/35" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 6.5-7.5 12-7.5 12s-7.5-5.5-7.5-12a7.5 7.5 0 1115 0z"/>
                            </svg>
                            <span class="break-words">{{ $address }}</span>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- ============================================================
                 COLUMN 4 — SOCIAL
                 ============================================================ --}}
            @if ($socials->isNotEmpty())
                <div data-reveal data-reveal-delay="300">
                    <h4 class="text-xs font-bold uppercase tracking-[0.15em] text-white/40">Social</h4>
                    <div class="mt-5 flex items-center gap-3">
                        @foreach ($socials as $key => $url)
                            <a
                                href="{{ $url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="{{ ucfirst($key) }}"
                                class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white/50 transition-all duration-300 hover:-translate-y-0.5 hover:scale-110 hover:border-wood-secondary/40 hover:bg-wood-secondary/15 hover:text-wood-secondary-light focus:outline-none focus-visible:ring-2 focus-visible:ring-wood-secondary focus-visible:ring-offset-2 focus-visible:ring-offset-[#1C1917]"
                            >
                                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="{{ $socialIcons[$key] ?? '' }}"/>
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- ============================================================
             FOOTER BOTTOM — Separator + Copyright
             ============================================================ --}}
        <div class="mt-14 border-t border-white/10 pt-6">
            <div class="flex flex-col items-center justify-between gap-3 sm:flex-row">
                <p class="text-sm text-white/35">
                    &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>
