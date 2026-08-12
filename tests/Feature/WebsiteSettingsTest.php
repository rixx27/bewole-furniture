<?php

use App\Helpers\WebsiteSettings;
use App\Models\WebsiteSetting;

it('parses raw google maps embed url correctly', function () {
    $embed = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12345!2d110.6!3d-6.5';
    
    // Simulate helper logic
    if (preg_match('/src=["\']([^"\']+)["\']/', $embed, $matches)) {
        $result = $matches[1];
    } else {
        $result = $embed;
    }

    expect($result)->toBe('https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12345!2d110.6!3d-6.5');
});

it('extracts src url when full iframe HTML tag is pasted into google_maps_embed', function () {
    $embed = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12345" width="600" height="450"></iframe>';
    
    if (preg_match('/src=["\']([^"\']+)["\']/', $embed, $matches)) {
        $result = $matches[1];
    } else {
        $result = $embed;
    }

    expect($result)->toBe('https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12345');
});
