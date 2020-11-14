<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], static function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('user', [AuthController::class, 'getAuthUser']);
});

Route::group(['middleware' => 'auth:api'], static function () {
    //
});
