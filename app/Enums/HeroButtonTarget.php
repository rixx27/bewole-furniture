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
            self::ProductPage => route('products.index'),
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

    /**
     * Resolve a raw admin-supplied value to its final href.
     *
     * This is fully dynamic based on what the admin stored, e.g.:
     *   "about"            → "#about"
     *   "products"         → "#products"
     *   "faq"              → "#faq"
     *   "contact"          → "#contact"
     *   "#about"           → "#about"            (no double "#")
     *   "/tentang-kami"    → "/tentang-kami"     (page path)
     *   "/produk"          → "/produk"           (page path)
     *   "https://..."      → unchanged           (external URL)
     *   "frontend.about"   → route("frontend.about")
     *
     * Returns null for empty values so the caller can hide the button.
     */
    public static function resolveHref(?string $value): ?string
    {
        $value = $value === null ? null : trim($value);

        if ($value === null || $value === '') {
            return null;
        }

        // Already a fragment anchor → keep as-is (never prepend "#" twice).
        if (Str::startsWith($value, '#')) {
            return $value;
        }

        // External URL → keep as-is.
        if (Str::startsWith($value, ['http://', 'https://', '//'])) {
            return $value;
        }

        // Page/route path (leading slash) → navigate to that path.
        if (Str::startsWith($value, '/')) {
            return $value;
        }

        // Canonical enum anchor value (about, products, faq, contact, ...).
        $enum = self::tryFrom($value);
        if ($enum !== null) {
            return $enum->href();
        }

        // Legacy enum value (e.g. "product-page", "tracking").
        $legacy = self::fromLegacy($value);
        if ($legacy !== null) {
            return $legacy->href();
        }

        // Valid Laravel route name → resolve to its URL.
        if (\Illuminate\Support\Facades\Route::has($value)) {
            return route($value);
        }

        // Fallback: treat any other value as a section anchor.
        return '#' . ltrim($value, '#');
    }
}
