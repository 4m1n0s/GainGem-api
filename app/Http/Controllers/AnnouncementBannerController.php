<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnnouncementBannerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AnnouncementBannerController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'announcement_banner' => Cache::get('announcement-banner'),
        ]);
    }

    public function store(StoreAnnouncementBannerRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $payload['is_enabled'] = $request->boolean('is_enabled');

        Cache::forget('announcement-banner');

        return response()->json([
            'announcement_banner' => Cache::rememberForever('announcement-banner', static fn () => $payload),
        ]);
    }
}
