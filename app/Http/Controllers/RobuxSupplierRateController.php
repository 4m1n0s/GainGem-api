<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRobuxSupplierRateRequest;
use Illuminate\Support\Facades\Cache;

class RobuxSupplierRateController extends Controller
{
    public function index(): float
    {
        return (float) bcmul(Cache::get('robux-supplier-rate'), 1000, 2);
    }

    public function update(StoreRobuxSupplierRateRequest $request): float
    {
        $payload = $request->validated();

        Cache::forget('robux-supplier-rate');

        return (float) bcmul(Cache::rememberForever('robux-supplier-rate', static fn () => bcdiv($payload['rate'], 1000, 5)), 1000, 2);
    }
}
