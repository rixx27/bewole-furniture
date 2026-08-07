<?php

namespace App\Enums;

use Illuminate\Support\Str;

/**
 * Enum representing the allowed "Tujuan Tombol" (button target) values
 * for a Hero banner button.
 *
 * The database stores only these simple values (never a full URL/route).
 * The frontend translates the value into the final href via {@see href()}.
 */
enum HeroButtonTarget: string
{
    case About = 'about';
    case Products = 'products';
    case WhyUs = 'why-us';
    case Reviews = 'reviews';
    case Faq = 'faq';
    case Contact = 'contact';
    case Tracking = 'tracking';
    case ProductPage = 'product-page';
    case Login = 'login';
    case Register = 'register';

    /**
     * Human readable label in Bahasa Indonesia.
     */
    public function label(): string
    {
        return match ($this) {
            self::About => 'Tentang Kami',
            self::Products => 'Produk',
            self::WhyUs => 'Keunggulan',
            self::Reviews => 'Testimoni',
            self::Faq => 'FAQ',
            self::Contact => 'Kontak',
            self::Tracking => 'Tracking Pesanan',
            self::ProductPage => 'Halaman Produk',
            self::Login => 'Login',
            self::Register => 'Register',
        };
    }

    /**
     * Resolve the enum instance to its final href.
     *
     * Section (in-page anchor) targets resolve to a "#slug" href,
     * while pages resolve to their named route.
     */
    public function href(): string
    {
        return match ($this) {
            self::About => '#about',
            self::Products => '#products',
            self::WhyUs => '#why-us',
            self::Reviews => '#reviews',
            self::Faq => '#faq',
            self::Contact => '#contact',
            self::Tracking => route('frontend.tracking'),
            self::ProductPage => route('frontend.catalog'),
            self::Login => route('login'),
            self::Register => route('register'),
        };
    }

    /**
     * All available options keyed by value, convenient for dropdowns:
     * ['about' => 'Tentang Kami', ...]
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $target) => [$target->value => $target->label()])
            ->all();
    }

    /**
     * Normalize a legacy stored value into a valid enum instance.
     *
     * Legacy data may contain a full path such as "/products" or "/contact".
     * This converts those to their canonical value. Returns null when the
     * value is not recognized so the caller can apply a fallback.
     */
    public static function fromLegacy(?string $value): ?self
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = trim($value);

        // Try direct match first (already a canonical value).
        $direct = self::tryFrom($value);
        if ($direct !== null) {
            return $direct;
        }

// Try matching a leading-slash path (legacy format like "/products").
        if (Str::startsWith($value, '/')) {
            $slug = trim($value, '/');
            $segment = Str::before($slug, '/');

            // Legacy "/catalog" historically pointed to the product catalog page.
            if ($segment === 'catalog') {
                return self::ProductPage;
            }

            $legacy = self::tryFrom($segment);
            if ($legacy !== null) {
                return $legacy;
            }
        }

        return null;
    }

    /**
     * Resolve a raw DB value to a valid target, falling back to null.
     */
    public static function resolve(?string $value): ?self
    {
        return self::fromLegacy($value);
    }
}
