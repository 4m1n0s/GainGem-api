<?php

namespace App\Http\Controllers;

use App\Http\Requests\RedeemCouponRequest;
use App\Http\Resources\UserResource;
use App\Models\CompletedTask;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    public function redeem(RedeemCouponRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $coupon = Coupon::whereCode($payload['code'])->where('expires_at', '>=', now())->with('completedTasks')->first();

        /** @var User $user */
        $user = auth()->user();

        $hasCompletedOfferThisWeek = CompletedTask::query()
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subWeek())
            ->availableForReferring()
            ->exists();

        abort_if($coupon === null, 422, 'Promo code has expired!');
        abort_if(! $hasCompletedOfferThisWeek, 422, 'You must complete at least 1 offer this week!');
        abort_if($coupon->max_usages !== 0 && $coupon->completedTasks()->count() >= $coupon->max_usages, 422, 'Promo code has reached its max usages!');
        abort_if($coupon->completedTasks()->where('user_id', $user->id)->exists(), 422, "You've already redeemed this promo code!");

        $user->completedTasks()->create([
            'type' => CompletedTask::TYPE_COUPON,
            'points' => $coupon->points,
            'coupon_id' => $coupon->id,
        ]);

        return response()->json([
            'user' => new UserResource($user->withAvailablePoints()),
        ]);
    }
}
