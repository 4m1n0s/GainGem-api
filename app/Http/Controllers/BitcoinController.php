<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBitcoinRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BitcoinController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Cache::get('bitcoin'));
    }

    public function store(StoreBitcoinRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $response = Http::get("https://api.smartbit.com.au/v1/blockchain/address/{$payload['wallet']}?tx=0");

        $btcValue = get_bitcoin_value();

        abort_if(! $response['success'], 422, 'Wrong wallet!');
        abort_if((float) $response['address']['total']['balance'] / $btcValue < (int) $payload['stock_amount'], 422, "You don't have enough cash!");

        Cache::forget('bitcoin');

        return response()->json(Cache::rememberForever('bitcoin', static fn () => $payload));
    }
}
