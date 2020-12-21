<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBitcoinRequest;
use App\Services\Bitcoin;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class BitcoinController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Cache::get('bitcoin'));
    }

    public function store(StoreBitcoinRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $response = Bitcoin::getBalance($payload['guid'], $payload['password']);

        abort_if($response->failed(), 422, isset($response['error']) ? $response['error'] : 'Unexpected error, please try again');
        abort_if(convert_satoshi_to_usd($response['balance']) < (int) $payload['stock_amount'], 422, "You don't have enough cash!");

        Cache::forget('bitcoin');

        return response()->json(Cache::rememberForever('bitcoin', static fn () => $payload));
    }
}
