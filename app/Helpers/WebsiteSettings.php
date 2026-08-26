<?php

namespace App\Helpers;

use App\Services\WebsiteSettingService;

class WebsiteSettings
{
    /**
     * The service instance.
     */
    protected static ?WebsiteSettingService $service = null;

    /**
     * Get the service instance.
     */
    protected static function service(): WebsiteSettingService
    {
        if (!static::$service) {
            static::$service = app(WebsiteSettingService::class);
        }

        return static::$service;
    }

    /**
     * Get all settings for frontend.
     */
    public static function all(): array
    {
        return static::service()->getForFrontend();
    }

    /**
     * Get a specific setting value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = static::all();

        return $settings[$key] ?? $default;
    }

    /**
     * Get the site name.
     */
    public static function siteName(): string
    {
        return static::get('site_name', config('app.name', 'Bewole Furniture'));
    }

    /**
     * Get the logo URL.
     */
    public static function logoUrl(): ?string
    {
        return static::get('logo_url');
    }

    /**
     * Get the logo storage path.
     */
    public static function logoPath(): ?string
    {
        return static::get('logo');
    }

    /**
     * Check if maintenance mode is active.
     */
    public static function isMaintenance(): bool
    {
        return static::get('is_maintenance', false);
    }

    /**
     * Get maintenance message.
     */
    public static function maintenanceMessage(): ?string
    {
        return static::get('maintenance_message');
    }

    /**
     * Get the WhatsApp number.
     */
    public static function whatsapp(): ?string
    {
        return static::get('whatsapp') ?: static::get('phone');
    }

    /**
     * Get the WhatsApp number (alias).
     */
    public static function whatsappNumber(): ?string
    {
        return static::whatsapp();
    }

    /**
     * Get the phone number.
     */
    public static function phone(): ?string
    {
        return static::get('phone');
    }

    /**
     * Get clean Google Maps Embed URL for iframe src attribute.
     */
    public static function googleMapsEmbedUrl(): ?string
    {
        $embed = static::get('google_maps_embed');

        if (! $embed) {
            return null;
        }

        $embed = trim((string) $embed);

        if ($embed === '') {
            return null;
        }

        // Extract src attribute if full <iframe src="..."> tag was pasted
        if (preg_match('/src=["\']([^"\']+)["\']/', $embed, $matches)) {
            return $matches[1];
        }

        return $embed;
    }
}

