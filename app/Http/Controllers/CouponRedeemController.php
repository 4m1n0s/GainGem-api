<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\CompletedTask;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class CouponRedeemController extends Controller
{
    public function store(Coupon $coupon): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $hadCompletedOfferThisWeek = CompletedTask::query()
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subWeek())
            ->availableForReferring()
            ->exists();

        abort_if(! $hadCompletedOfferThisWeek, 422, 'You must complete at least 1 offer this week!');

        $coupon->loadCount('completedTasks');

        abort_if($coupon->expires_at->isPast(), 422, 'Invalid or expired promo code!');
        abort_if($coupon->max_usages !== 0 && $coupon->completed_tasks_count >= $coupon->max_usages, 422, 'Invalid or expired promo code!');
        abort_if($coupon->completedTasks()->where('user_id', $user->id)->exists(), 422, "You've already redeemed this promo code!");

        $user->completedTasks()->create([
            'type' => CompletedTask::TYPE_PROMO_CODE,
            'points' => $coupon->points,
            'coupon_id' => $coupon->id,
        ]);

        return response()->json([
            'user' => new UserResource($user->withAvailablePoints()),
        ]);
    }
}
