<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    public function index(): JsonResponse
    {
        $coupons = Coupon::withCount('completedTasks as uses')->orderByDesc('id')->paginate(10);

        $pagination = $coupons->toArray();
        $coupons = $pagination['data'];
        unset($pagination['data']);

        return response()->json([
            'promo_codes' => $coupons,
            'pagination' => $pagination,
        ]);
    }

    public function store(StoreCouponRequest $request): JsonResponse
    {
        $payload = $request->validated();

        return response()->json([
            'promo_code' => Coupon::create($payload),
        ]);
    }

    public function update(Coupon $coupon, UpdateCouponRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $coupon->loadCount('completedTasks as uses');

        abort_if((int) $payload['max_usages'] !== 0 && $coupon->uses > $payload['max_usages'], 422, 'Cannot set max usages below the current uses');

        $coupon->update($payload);

        return response()->json([
            'promo_code' => $coupon,
        ]);
    }

    public function destroy(Coupon $coupon): void
    {
        $coupon->delete();
    }
}
