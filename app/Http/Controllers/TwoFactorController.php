<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class TwoFactorController extends Controller
{
    public function store(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        abort_if(! $user->email_verified_at, 422, 'Email must be verified in order to enable 2FA security.');
        abort_if((bool) $user->two_factor_enabled_at, 422, '2FA security is already enabled!');

        $user->update([
            'two_factor_enabled_at' => now(),
        ]);

        return response()->json([
            'user' => new UserResource($user->loadAvailablePoints()),
        ]);
    }

    public function destroy(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        abort_if(! $user->two_factor_enabled_at, 422, '2FA security is already disabled!');

        $user->update([
            'two_factor_enabled_at' => null,
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ]);

        return response()->json([
            'user' => new UserResource($user->loadAvailablePoints()),
        ]);
    }
}
