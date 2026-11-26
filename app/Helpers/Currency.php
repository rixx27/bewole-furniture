<?php

namespace App\Helpers;

use App\Services\CurrencyService;

class Currency
{
    protected static ?CurrencyService $service = null;

    protected static function service(): CurrencyService
    {
        if (!static::$service) {
            static::$service = app(CurrencyService::class);
        }

        return static::$service;
    }

    public static function current(): string
    {
        return static::service()->getCurrentCurrency();
    }

    public static function isUsd(): bool
    {
        return static::service()->isUsd();
    }

    public static function isIdr(): bool
    {
        return static::service()->isIdr();
    }

    public static function rate(): float
    {
        return static::service()->getUsdRate();
    }

    public static function format(float $amount, ?string $currency = null): string
    {
        return static::service()->format($amount, $currency);
    }

    public static function formatPrice(?float $priceIdr, ?float $priceUsd = null, ?string $currency = null): string
    {
        return static::service()->formatProductPrice($priceIdr, $priceUsd, $currency);
    }

    public static function toIdr(float $usdAmount, ?float $rate = null): int
    {
        return static::service()->toIdr($usdAmount, $rate);
    }

    public static function toUsd(float $idrAmount, ?float $rate = null): float
    {
        return static::service()->toUsd($idrAmount, $rate);
    }

    public static function hasVisibleProductPrices(): bool
    {
        return static::service()->hasVisibleProductPrices();
    }

    public static function forgetPriceVisibilityCache(): void
    {
        static::service()->forgetPriceVisibilityCache();
    }
}
