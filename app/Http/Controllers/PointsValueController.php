<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePointsValueRequest;
use Illuminate\Support\Facades\Cache;

class PointsValueController extends Controller
{
    public function index(): int
    {
        return (int) Cache::get('points-value');
    }

    public function update(UpdatePointsValueRequest $request): int
    {
        $payload = $request->validated();

        Cache::forget('points-value');

        return (int) Cache::rememberForever('points-value', static fn () => $payload['points']);
    }
}
