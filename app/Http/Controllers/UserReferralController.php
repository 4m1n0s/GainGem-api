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
                $referredUser['formatted_created_at'] = $referredUser->created_at ? $referredUser->created_at->format('M d Y') : $referredUser->created_at;
                $referredUser['formatted_total_points'] = $referredUser->total_points * CompletedTask::COMMISSION_PERCENT_REFERRAL;
            });

        return response()->json([
            'referrals' => $referrals,
        ]);
    }
}
