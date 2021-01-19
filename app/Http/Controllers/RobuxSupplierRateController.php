<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRobuxSupplierRateRequest;
use Illuminate\Support\Facades\Cache;

class RobuxSupplierRateController extends Controller
{
    public function index(): int
    {
        return (int) Cache::get('robux-supplier-rate') * 1000;
    }

    public function update(StoreRobuxSupplierRateRequest $request): int
    {
        $payload = $request->validated();

        Cache::forget('robux-supplier-rate');

        return (int) Cache::rememberForever('robux-supplier-rate', static fn () => $payload['rate'] / 1000) * 1000;
    }
}
