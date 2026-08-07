@php
    $siteName = App\Helpers\WebsiteSettings::siteName();
    $siteLogo = App\Helpers\WebsiteSettings::logoUrl();
    $siteTagline = App\Helpers\WebsiteSettings::get('site_tagline');
    $address = App\Helpers\WebsiteSettings::get('address');
    $email = App\Helpers\WebsiteSettings::get('email');
    $phone = App\Helpers\WebsiteSettings::get('phone');
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
@endphp

<footer class="mt-auto border-t border-wood-border bg-wood-surface">
    <div class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
            {{-- Brand --}}
            <div class="lg:col-span-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    @if ($siteLogo)
                        <img src="{{ $siteLogo }}" alt="Logo {{ $siteName }}" class="h-10 w-10 rounded-full object-contain">
                    @else
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-wood-primary text-lg font-bold text-white">
                            {{ strtoupper(substr($siteName, 0, 1)) }}
                        </span>
                    @endif
                    <span class="text-xl font-bold tracking-tight text-wood-text">{{ $siteName }}</span>
                </a>

                @if ($siteTagline)
                    <p class="mt-4 max-w-md text-sm leading-relaxed text-wood-muted">{{ $siteTagline }}</p>
                @endif

                @if ($socials->isNotEmpty())
                    <div class="mt-6 flex items-center gap-3">
                        @foreach ($socials as $key => $url)
                            <a
                                href="{{ $url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="{{ ucfirst($key) }}"
                                class="flex h-10 w-10 items-center justify-center rounded-full border border-wood-border bg-wood-bg text-wood-muted transition-all duration-300 hover:-translate-y-0.5 hover:bg-wood-primary hover:text-white"
                            >
                                <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="{{ $socialIcons[$key] ?? '' }}"/>
                                </svg>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Quick Links --}}
            <div>
                <h4 class="text-sm font-bold uppercase tracking-wider text-wood-text">Tautan Cepat</h4>
                <ul class="mt-4 space-y-3">
                    @foreach ([
                        ['label' => 'Home', 'route' => 'home'],
                        ['label' => 'Tentang Kami', 'route' => 'frontend.about'],
                        ['label' => 'Katalog', 'route' => 'frontend.catalog'],
                        ['label' => 'FAQ', 'route' => 'frontend.faq'],
                        ['label' => 'Kontak', 'route' => 'frontend.contact'],
                    ] as $link)
                        <li>
                            <a href="{{ route($link['route']) }}" class="inline-flex items-center gap-2 text-sm text-wood-muted transition-colors duration-200 hover:text-wood-primary">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                </svg>
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <h4 class="text-sm font-bold uppercase tracking-wider text-wood-text">Kontak</h4>
                <ul class="mt-4 space-y-3 text-sm text-wood-muted">
                    @if ($address)
                        <li class="flex items-start gap-3">
                            <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 6.5-7.5 12-7.5 12s-7.5-5.5-7.5-12a7.5 7.5 0 1115 0z"/>
                            </svg>
                            <span>{{ $address }}</span>
                        </li>
                    @endif
                    @if ($email)
                        <li class="flex items-center gap-3">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
                            <a href="mailto:{{ $email }}" class="transition-colors hover:text-wood-primary">{{ $email }}</a>
                        </li>
                    @endif
                    @if ($phone)
                        <li class="flex items-center gap-3">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                            </svg>
                            <a href="tel:{{ preg_replace('/[^0-9]/', '', $phone) }}" class="transition-colors hover:text-wood-primary">{{ $phone }}</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-wood-border pt-6 sm:flex-row">
            <p class="text-sm text-wood-muted">
                &copy; {{ date('Y') }} {{ $siteName }}. Hak Cipta Dilindungi.
            </p>
            <p class="text-sm text-wood-muted">Dibuat dengan <span class="text-wood-secondary">♥</span> untuk hunian Anda.</p>
        </div>
    </div>
</footer>
