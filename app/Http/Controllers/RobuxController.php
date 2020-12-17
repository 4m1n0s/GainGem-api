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
        return response()->json([
            'robux' => Cache::get('robux'),
        ]);
    }

    public function store(StoreRobuxRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $response = Http::get("https://groups.roblox.com/v1/groups/{$payload['group_id']}");

        abort_if($response->failed(), 422, 'Invalid group!');

        Cache::forget('robux');

        return response()->json([
            'robux' => Cache::rememberForever('robux', static fn () => $payload),
        ]);
    }
}
