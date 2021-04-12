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
            ->paginate(10);

        $referralsArr = $referrals->each(static function (User $referredUser) {
            $referredUser['referral_points'] = currency_format($referredUser->total_points * CompletedTask::COMMISSION_PERCENT_REFERRAL);
        });

        $pagination = $referrals->toArray();
        unset($pagination['data']);

        return response()->json([
            'referrals' => $referralsArr,
            'pagination' => $pagination,
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
            'total_revenue' => currency_format($totalRevenue),
        ]);
    }
}
