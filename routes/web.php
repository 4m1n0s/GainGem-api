<?php

use App\Http\Controllers\TelescopeController;

Route::get('login-telescope', [TelescopeController::class, 'login']);
