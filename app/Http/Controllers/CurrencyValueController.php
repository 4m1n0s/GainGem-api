<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCurrencyValueRequest;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;

class CurrencyValueController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Currency::has('currencyValue')->with('currencyValue')->get());
    }

    public function update(Currency $currency, UpdateCurrencyValueRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $currencyValue = $currency->currencyValue()->updateOrCreate(['currency_id' => $currency->id], $payload);

        return response()->json($currencyValue);
    }
}
