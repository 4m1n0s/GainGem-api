<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBitcoinValueRequest;
use Illuminate\Support\Facades\Cache;

class BitcoinValueController extends Controller
{
    public function index(): int
    {
        return (int) Cache::get('bitcoin-value');
    }

    public function update(UpdateBitcoinValueRequest $request): int
    {
        $payload = $request->validated();

        Cache::forget('bitcoin-value');

        return (int) Cache::rememberForever('bitcoin-value', static fn () => $payload['bitcoin']);
    }
}
