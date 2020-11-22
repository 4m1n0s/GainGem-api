<?php

namespace App\Http\Controllers;

use App\Http\Requests\RedeemCouponRequest;
use App\Models\CompletedTask;
use App\Models\Coupon;
use App\Models\User;

class CouponController extends Controller
{
    public function redeem(RedeemCouponRequest $request)
    {
        $payload = $request->validated();

        $coupon = Coupon::whereCode($payload['code'])->whereRaw('expires_at > now()')->with('completedTasks')->first();

        /** @var User $user */
        $user = auth()->user();

        abort_if($coupon === null, 422, 'Promo code has expired!');
        abort_if($coupon->max_usages !== 0 && $coupon->completedTasks()->count() >= $coupon->max_usages, 422, 'Promo code has reached its max usages!');
        abort_if($coupon->completedTasks()->where('user_id', $user->id)->exists(), 422, "You've already redeemed this promo code!");

        $user->completedTasks()->create([
            'type' => CompletedTask::TYPE_COUPON,
            'points' => $coupon->points,
            'coupon_id' => $coupon->id,
        ]);

        return response()->json([
            'user' => $user,
        ]);
    }
}
