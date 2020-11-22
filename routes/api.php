<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ResendVerificationController;
use App\Http\Controllers\UserVerificationController;
use Illuminate\Support\Facades\Route;

Route::post('resend-verification', [ResendVerificationController::class, '__invoke']);
Route::post('verify', [UserVerificationController::class, '__invoke']);

Route::group(['prefix' => 'auth'], static function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('user', [AuthController::class, 'getAuthUser']);
});

Route::group(['middleware' => 'auth:api'], static function () {
    Route::group(['prefix' => 'coupons'], static function () {
        Route::post('redeem', [CouponController::class, 'redeem']);
    });
});
