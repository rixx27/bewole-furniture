<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Switch the active session currency (IDR / USD).
     */
    public function switch(Request $request, ?string $currency = null, CurrencyService $currencyService)
    {
        $currency = $currency ?: $request->input('currency', CurrencyService::CURRENCY_IDR);
        $activeCurrency = $currencyService->setCurrency($currency);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'currency' => $activeCurrency,
            ]);
        }

        return redirect()->back();
    }
}
