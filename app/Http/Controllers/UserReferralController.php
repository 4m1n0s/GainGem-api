<?php

namespace App\Http\Controllers;

use App\Models\CompletedTask;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserReferralController extends Controller
{
    public function show(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $referrals = $user->referredUsers()
            ->withTotalPoints()
            ->get()
            ->each(static function (User $referredUser) {
                $referredUser['referral_points'] = points_format($referredUser->total_points * CompletedTask::COMMISSION_PERCENT_REFERRAL);
            });

        return response()->json([
            'referrals' => $referrals,
        ]);
    }

    public function stats(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $user->loadCount('referredUsers');

        $totalRevenue = $user->completedTasks()
            ->where('type', CompletedTask::TYPE_REFERRAL_INCOME)
            ->sum('points');

        return response()->json([
            'total_referrals' => $user->referred_users_count,
            'total_revenue' => points_format($totalRevenue),
        ]);
    }
}
