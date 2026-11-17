<?php

namespace App\Services;

use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    public const DEFAULT_USD_RATE = 18271.95;
    public const CURRENCY_IDR = 'IDR';
    public const CURRENCY_USD = 'USD';

    /**
     * Get the active currency code ('IDR' or 'USD').
     */
    public function getCurrentCurrency(): string
    {
        $sessionCurrency = session('currency');
        if ($sessionCurrency && in_array(strtoupper($sessionCurrency), [self::CURRENCY_IDR, self::CURRENCY_USD])) {
            return strtoupper($sessionCurrency);
        }

        $default = WebsiteSetting::first()?->default_currency ?? self::CURRENCY_IDR;
        return in_array(strtoupper($default), [self::CURRENCY_IDR, self::CURRENCY_USD])
            ? strtoupper($default)
            : self::CURRENCY_IDR;
    }

    /**
     * Set the active currency in session.
     */
    public function setCurrency(string $currency): string
    {
        $currency = strtoupper(trim($currency));
        if (!in_array($currency, [self::CURRENCY_IDR, self::CURRENCY_USD])) {
            $currency = self::CURRENCY_IDR;
        }

        session(['currency' => $currency]);
        return $currency;
    }

    /**
     * Check if the active currency is USD.
     */
    public function isUsd(): bool
    {
        return $this->getCurrentCurrency() === self::CURRENCY_USD;
    }

    /**
     * Check if the active currency is IDR.
     */
    public function isIdr(): bool
    {
        return $this->getCurrentCurrency() === self::CURRENCY_IDR;
    }

    /**
     * Get the USD to IDR exchange rate.
     */
    public function getUsdRate(): float
    {
        return (float) Cache::remember('currency_usd_rate', 3600, function () {
            $setting = WebsiteSetting::first();
            if ($setting && !empty($setting->usd_rate) && (float) $setting->usd_rate > 0) {
                return (float) $setting->usd_rate;
            }
            return self::DEFAULT_USD_RATE;
        });
    }

    /**
     * Convert USD to IDR.
     */
    public function toIdr(float $usdAmount, ?float $rate = null, bool $roundToThousand = true): int
    {
        $rate = $rate ?: $this->getUsdRate();
        $idr = $usdAmount * $rate;

        if ($roundToThousand && $idr >= 10000) {
            return (int) (round($idr / 1000) * 1000);
        }

        return (int) round($idr);
    }

    /**
     * Convert IDR to USD.
     */
    public function toUsd(float $idrAmount, ?float $rate = null): float
    {
        $rate = $rate ?: $this->getUsdRate();
        if ($rate <= 0) {
            return 0.0;
        }

        return round($idrAmount / $rate, 2);
    }

    /**
     * Format an amount based on currency.
     */
    public function format(float $amount, ?string $currency = null): string
    {
        $currency = $currency ? strtoupper($currency) : $this->getCurrentCurrency();

        if ($currency === self::CURRENCY_USD) {
            return '$ ' . number_format($amount, 2, '.', ',');
        }

        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Format a product price dynamically based on active currency.
     * Takes both IDR and USD stored values (or converts if one is missing).
     */
    public function formatProductPrice(?float $priceIdr, ?float $priceUsd = null, ?string $currency = null): string
    {
        $currency = $currency ? strtoupper($currency) : $this->getCurrentCurrency();

        if ($currency === self::CURRENCY_USD) {
            if ($priceUsd !== null && (float) $priceUsd > 0) {
                return $this->format((float) $priceUsd, self::CURRENCY_USD);
            }
            $convertedUsd = $this->toUsd((float) ($priceIdr ?? 0));
            return $this->format($convertedUsd, self::CURRENCY_USD);
        }

        // Default IDR
        if ($priceIdr !== null && (float) $priceIdr > 0) {
            return $this->format((float) $priceIdr, self::CURRENCY_IDR);
        }
        $convertedIdr = $this->toIdr((float) ($priceUsd ?? 0));
        return $this->format($convertedIdr, self::CURRENCY_IDR);
    }

    /**
     * Fetch the latest USD to IDR rate from an online exchange rate API and save it.
     */
    public function syncRateFromApi(): array
    {
        try {
            // Using open.er-api.com (free, reliable, no API key needed)
            $response = Http::withoutVerifying()->timeout(6)->get('https://open.er-api.com/v6/latest/USD');

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['rates']['IDR']) && (float) $data['rates']['IDR'] > 0) {
                    $newRate = round((float) $data['rates']['IDR'], 2);

                    $setting = WebsiteSetting::first();
                    if ($setting) {
                        $setting->usd_rate = $newRate;
                        $setting->save();
                    }

                    Cache::forget('currency_usd_rate');
                    Cache::forget('website_settings_frontend');

                    return [
                        'success' => true,
                        'rate' => $newRate,
                        'message' => 'Kurs berhasil diperbarui dari internet: 1 USD = Rp ' . number_format($newRate, 2, ',', '.'),
                    ];
                }
            }

            return [
                'success' => false,
                'rate' => $this->getUsdRate(),
                'message' => 'Gagal mendapatkan data kurs dari API. Nilai kurs tetap menggunakan yang ada.',
            ];
        } catch (\Throwable $e) {
            Log::warning('Currency sync error: ' . $e->getMessage());
            return [
                'success' => false,
                'rate' => $this->getUsdRate(),
                'message' => 'Koneksi ke API kurs gagal (' . $e->getMessage() . ').',
            ];
        }
    }
}
