<?php

use App\Http\Controllers\AnnouncementBannerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BanController;
use App\Http\Controllers\CompletedTaskController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CouponRedeemController;
use App\Http\Controllers\DailyTaskController;
use App\Http\Controllers\GiftCardController;
use App\Http\Controllers\GiveawayController;
use App\Http\Controllers\PointsValueController;
use App\Http\Controllers\ResendVerificationController;
use App\Http\Controllers\RobuxController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\UserCompletedTaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserReferralController;
use App\Http\Controllers\UserTransactionController;
use App\Http\Controllers\UserVerificationController;
use Illuminate\Support\Facades\Route;

Route::post('resend-verification', ResendVerificationController::class);
Route::post('verify', UserVerificationController::class);

Route::group(['prefix' => 'auth'], static function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('user', [AuthController::class, 'getAuthUser'])->middleware('auth:api');
});

Route::get('stats', StatsController::class);

Route::group(['middleware' => 'auth:api'], static function () {
    Route::group(['prefix' => 'coupons'], static function () {
        Route::get('', [CouponController::class, 'index'])->middleware('role:super_admin,admin');
        Route::post('', [CouponController::class, 'store'])->middleware('role:super_admin,admin');
        Route::delete('{coupon:code}', [CouponController::class, 'destroy'])->middleware('role:super_admin,admin');
        Route::put('{coupon}', [CouponController::class, 'update'])->middleware('role:super_admin,admin');
        Route::post('{coupon:code}/redeems', [CouponRedeemController::class, 'store']);
    });

    Route::group(['prefix' => 'points'], static function () {
        Route::get('', [PointsValueController::class, 'index']);
        Route::put('', [PointsValueController::class, 'update'])->middleware('role:super_admin,admin');
    });

    Route::group(['prefix' => 'daily-tasks'], static function () {
        Route::get('', [DailyTaskController::class, 'index']);
        Route::post('', [DailyTaskController::class, 'store']);
    });

    Route::group(['prefix' => 'giveaway'], static function () {
        Route::get('', [GiveawayController::class, 'recently']);
        Route::post('', [GiveawayController::class, 'enter']);
    });

    Route::group(['prefix' => 'activities'], static function () {
        Route::get('', [CompletedTaskController::class, 'index']);
    });

    Route::group(['prefix' => 'gift-cards'], static function () {
        Route::get('', [GiftCardController::class, 'index'])->middleware('role:super_admin,admin');
        Route::post('', [GiftCardController::class, 'store'])->middleware('role:super_admin,admin');
        Route::put('{giftCard}', [GiftCardController::class, 'update'])->middleware('role:super_admin,admin');
        Route::delete('{giftCard}', [GiftCardController::class, 'destroy'])->middleware('role:super_admin,admin');
    });

    Route::group(['prefix' => 'robux'], static function () {
        Route::get('', [RobuxController::class, 'index'])->middleware('role:super_admin');
        Route::post('', [RobuxController::class, 'store'])->middleware('role:super_admin');
    });

    Route::group(['prefix' => 'users'], static function () {
        Route::get('', [UserController::class, 'index'])->middleware('role:super_admin,admin');
        Route::get('{user}/transactions', [UserTransactionController::class, 'show']);
        Route::get('{user}/activities', [UserCompletedTaskController::class, 'show']);
        Route::get('{user}/referrals', [UserReferralController::class, 'show']);
        Route::get('{user}/referrals/stats', [UserReferralController::class, 'stats']);
        Route::put('{user}', [UserController::class, 'update']);
        Route::post('{user}/bans', [BanController::class, 'store'])->middleware('role:super_admin,admin');
        Route::delete('{user}/bans', [BanController::class, 'destroy'])->middleware('role:super_admin,admin');
    });

    Route::group(['prefix' => 'announcement-banner'], static function () {
        Route::get('', [AnnouncementBannerController::class, 'index']);
        Route::post('', [AnnouncementBannerController::class, 'store'])->middleware('role:super_admin,admin');
    });
});
