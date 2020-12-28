<?php

namespace App\Http\Controllers;

use App\Models\CompletedTask;
use Illuminate\Http\JsonResponse;

class CompletedTaskController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'activities' => CompletedTask::whereNotIn('type', [CompletedTask::TYPE_REFERRAL_INCOME, CompletedTask::TYPE_CHARGEBACK, CompletedTask::TYPE_ADMIN])
                ->whereNotNull('user_id')
                ->with('user:id,username,profile_image')
                ->orderByDesc('updated_at')
                ->limit(10)
                ->get(),
        ]);
    }
}
