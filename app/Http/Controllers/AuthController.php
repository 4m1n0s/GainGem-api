<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\UrlToken;
use App\Models\User;
use App\Notifications\VerifyUserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $payload['profile_image'] = asset('assets/user.png'); // @TODO should be in storage/public, not in assets
        $payload['ip'] = get_ip();

        if (Arr::get($payload, 'referral_token')) {
            $referredBy = User::where('referral_token', $payload['referral_token'])->first();
            $payload['referred_by'] = optional($referredBy)->id;
        }

        do {
            $referralToken = Str::random(5);
            $isReferralTokenExists = User::where('referral_token', $referralToken)->exists();
        } while ($isReferralTokenExists);

        $payload['referral_token'] = $referralToken;

        $user = User::create($payload)->withAvailablePoints();

        /** @var UrlToken $urlToken */
        $urlToken = $user->urlTokens()->create([
            'type' => UrlToken::TYPE_VERIFICATION,
            'token' => UrlToken::getRandomToken(),
            'expires_at' => now()->addDay(),
        ]);

        $user->notify(new VerifyUserNotification($urlToken));

        $token = auth()->login($user);

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $token = auth()->attempt($payload);

        abort_if(! $token, 422, 'Incorrect email or password');

        /** @var User $user */
        $user = auth()->user();

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user->withAvailablePoints()),
        ]);
    }

    public function getAuthUser(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        return response()->json([
            'user' => new UserResource($user->withAvailablePoints()),
        ]);
    }
}
