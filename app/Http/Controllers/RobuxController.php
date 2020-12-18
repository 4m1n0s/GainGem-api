<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRobuxRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RobuxController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Cache::get('robux'));
    }

    public function store(StoreRobuxRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $response = Http::withHeaders([
            'cookie' => '.ROBLOSECURITY='.$payload['cookie'],
        ])->get("https://groups.roblox.com/v1/groups/{$payload['group_id']}/settings");

        abort_if($response->status() === 401, 422, 'Invalid cookie!');
        abort_if($response->failed(), 422, 'Invalid group!');

        Cache::forget('robux');

        return response()->json(Cache::rememberForever('robux', static fn () => $payload));
    }
}
