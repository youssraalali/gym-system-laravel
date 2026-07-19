<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MembershipPlan;
use Illuminate\Support\Facades\Http;

class MembershipPlanController extends Controller
{
    public function convert(Request $request, $id)
{
    $membershipPlan = MembershipPlan::findOrFail($id);
    $targetCurrency = $request->input('currency');

    if (!$targetCurrency) {
        return response()->json(['error' => 'Currency parameter is required'], 400);
    }

    try {
        $exchangeRates = Http::get("https://api.frankfurter.app/latest?from=USD&to={$targetCurrency}")
            ->throw()
            ->json();
    } catch (\Illuminate\Http\Client\RequestException $e) {
        return response()->json(['error' => 'Invalid currency or exchange rate service unavailable'], 400);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Unable to fetch exchange rate at this time'], 503);
    }

    if (!isset($exchangeRates['rates'][$targetCurrency])) {
        return response()->json(['error' => 'Invalid target currency'], 400);
    }

    $convertedPrice = $membershipPlan->price * $exchangeRates['rates'][$targetCurrency];

    return response()->json([
        'original_price' => $membershipPlan->price,
        'original_currency' => 'USD',
        'converted_price' => round($convertedPrice, 2),
        'target_currency' => $targetCurrency
    ]);
}
}
