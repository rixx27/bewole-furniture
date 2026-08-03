@php
    $siteName = App\Helpers\WebsiteSettings::siteName();
    $siteLogo = App\Helpers\WebsiteSettings::logoUrl();
@endphp

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title . ' - ' . $siteName : $siteName }}
</title>

@if ($siteLogo)
    <link rel="icon" href="{{ $siteLogo }}" />
@else
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
@endif

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

