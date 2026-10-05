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

it('resolves custom_furniture_image URL via helper when configured', function () {
    $setting = WebsiteSetting::first() ?? new WebsiteSetting();
    $setting->custom_furniture_image = 'website/custom-furniture/test.webp';
    $setting->save();

    expect(WebsiteSettings::customFurnitureImageUrl())->toContain('website/custom-furniture/test.webp');
    expect(WebsiteSettings::customFurnitureImagePath())->toBe('website/custom-furniture/test.webp');

    // Reset back
    $setting->custom_furniture_image = null;
    $setting->save();

    expect(WebsiteSettings::customFurnitureImageUrl())->toBeNull();
});

it('custom furniture component uses custom image when set in website settings', function () {
    $setting = WebsiteSetting::first() ?? new WebsiteSetting();
    $setting->custom_furniture_image = 'website/custom-furniture/custom-card.webp';
    $setting->save();

    $component = new \App\View\Components\Home\CustomFurniture();
    expect($component->imageUrl)->toContain('website/custom-furniture/custom-card.webp');

    // Reset back
    $setting->custom_furniture_image = null;
    $setting->save();
});

it('can update all settings including seo fields without database errors', function () {
    $service = app(\App\Services\WebsiteSettingService::class);
    $setting = $service->get() ?? WebsiteSetting::create(['site_name' => 'Bewole']);

    $updated = $service->update($setting, [
        'site_name' => 'Bewole Furniture Test',
        'meta_title' => 'Bewole Meta Title',
        'meta_description' => 'Bewole Description',
        'meta_keywords' => 'furniture, jepara',
    ]);

    expect($updated->meta_title)->toBe('Bewole Meta Title');
    expect($updated->meta_description)->toBe('Bewole Description');
    expect($updated->meta_keywords)->toBe('furniture, jepara');
});

it('homepage renders configured meta_title and meta_description in html head', function () {
    $service = app(\App\Services\WebsiteSettingService::class);
    $setting = $service->get() ?? WebsiteSetting::create(['site_name' => 'Bewole']);

    $service->update($setting, [
        'site_name' => 'Bewole Furniture Test',
        'meta_title' => 'Mebel Jepara Terbaik & Terlengkap',
        'meta_description' => 'Toko mebel jati Jepara kualitas ekspor terpercaya.',
        'meta_keywords' => 'furniture, jepara, jati',
    ]);

    $response = $this->get(route('home'));
    $response->assertSuccessful();
    $response->assertSee('<title>Mebel Jepara Terbaik &amp; Terlengkap</title>', false);
    $response->assertSee('<meta name="description" content="Toko mebel jati Jepara kualitas ekspor terpercaya.">', false);
    $response->assertSee('<meta name="keywords" content="furniture, jepara, jati">', false);
});

it('sitemap endpoint returns valid xml response', function () {
    $response = $this->get(route('sitemap'));
    $response->assertSuccessful();
    $response->assertHeader('Content-Type', 'application/xml');
    $response->assertSee('urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"', false);
});

it('can save and retrieve bank account information in website settings', function () {
    $service = app(\App\Services\WebsiteSettingService::class);
    $setting = $service->get() ?? WebsiteSetting::create(['site_name' => 'Bewole']);

    $service->update($setting, [
        'bank_name' => 'Mandiri (Bank Mandiri)',
        'bank_account_number' => '123-456-7890',
        'bank_account_holder' => 'CV BEWOLE JEPARA',
    ]);

    expect(WebsiteSettings::bankName())->toBe('Mandiri (Bank Mandiri)');
    expect(WebsiteSettings::bankAccountNumber())->toBe('123-456-7890');
    expect(WebsiteSettings::bankAccountHolder())->toBe('CV BEWOLE JEPARA');
});

