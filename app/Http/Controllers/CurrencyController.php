<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexCurrencyRequest;
use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    public function index(IndexCurrencyRequest $request): JsonResponse
    {
        $payload = $request->validated();

        if (isset($payload['no_pagination']) && $payload['no_pagination']) {
            return response()->json(Currency::with('currencyValue')->get());
        }

        $currencies = Currency::orderByDesc('id')->paginate(10);

        $pagination = $currencies->toArray();
        $currenciesArr = $pagination['data'];
        unset($pagination['data']);

        return response()->json([
            'currencies' => $currenciesArr,
            'pagination' => $pagination,
        ]);
    }

    public function store(StoreCurrencyRequest $request): JsonResponse
    {
        $payload = $request->validated();

        return response()->json(Currency::create($payload));
    }

    public function update(Currency $currency, UpdateCurrencyRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $currency->update($payload);

        return response()->json($currency);
    }

    public function destroy(Currency $currency): void
    {
        $currency->delete();
    }
}
