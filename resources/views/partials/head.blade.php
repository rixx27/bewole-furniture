@php
    $siteName = App\Helpers\WebsiteSettings::siteName();
    $siteLogo = App\Helpers\WebsiteSettings::logoUrl();
@endphp

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title . ' - ' . $siteName : $siteName }}
</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}?v=4" sizes="any">
    <link rel="icon" href="{{ asset('favicon.svg') }}?v=4" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=4">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

