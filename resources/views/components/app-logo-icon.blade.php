@php
    $logoUrl = \App\Helpers\WebsiteSettings::logoUrl() ?: asset('apple-touch-icon.png');
    $siteName = \App\Helpers\WebsiteSettings::siteName() ?: 'Bewole Jepara Furniture';
@endphp
<img src="{{ $logoUrl }}" alt="{{ $siteName }}" {{ $attributes->merge(['class' => 'object-contain rounded-md']) }} />

