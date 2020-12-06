<?php

use App\Http\Controllers\AnnouncementBannerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CouponRedeemController;
use App\Http\Controllers\DailyTaskController;
use App\Http\Controllers\ResendVerificationController;
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
        Route::post('{coupon:code}/redeems', [CouponRedeemController::class, 'store']);
    });

    Route::group(['prefix' => 'daily-tasks'], static function () {
        Route::get('', [DailyTaskController::class, 'index']);
        Route::post('', [DailyTaskController::class, 'store']);
    });

    Route::group(['prefix' => 'users'], static function () {
        Route::get('', [UserController::class, 'index'])->middleware('role:admin');
        Route::get('{user}/transactions', [UserTransactionController::class, 'show']);
        Route::get('{user}/activities', [UserCompletedTaskController::class, 'show']);
        Route::get('{user}/referrals', [UserReferralController::class, 'show']);
        Route::put('{user}', [UserController::class, 'update']);
    });

    Route::group(['prefix' => 'announcement-banner'], static function () {
        Route::get('', [AnnouncementBannerController::class, 'index']);
        Route::post('', [AnnouncementBannerController::class, 'store'])->middleware('role:admin');
    });
});
