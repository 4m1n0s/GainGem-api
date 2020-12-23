<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRobuxRequest;
use App\Services\Robux;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class RobuxController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Cache::get('robux'));
    }

    public function store(StoreRobuxRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $response = Robux::getGroupSettingsResponse($payload['cookie'], $payload['group_id']);

        abort_if($response->status() === 401, 422, 'Invalid cookie!');
        abort_if($response->failed(), 422, 'Invalid group!');

        Cache::forget('robux');

        return response()->json(Cache::rememberForever('robux', static fn () => $payload));
    }
}
