<?php

namespace App\Http\Controllers;

use App\Models\CompletedTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class StatsController extends Controller
{
    public function index(): JsonResponse
    {
        $totalPointsEarned = Cache::remember('total_points_earned', 60 * 5, static function (): string {
            return number_format(CompletedTask::sum('points'), 2, '.', ',');
        });

        $totalOffersCompleted = Cache::remember('total_offers_completed', 60 * 5, static function (): int {
            return CompletedTask::whereType(CompletedTask::TYPE_OFFER)->count();
        });

        return response()->json([
            'total_points_earned' => $totalPointsEarned,
            'total_offers_completed' => $totalOffersCompleted,
        ]);
    }
}
