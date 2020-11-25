<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\UrlToken;
use App\Models\User;
use App\Notifications\VerifyUserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $payload['profile_image'] = asset('assets/user.png');
        $payload['ip'] = Helper::instance()->getIp();

        if ($payload['referral_token']) {
            $referredBy = User::whereReferralToken($payload['referral_token'])->first();
        }

        $payload['referred_by'] = $referredBy->id ?? null;

//        $referredBy = $payload['referral_token'] ? User::whereReferralToken($payload['referral_token'])->first() : null;
//
//        $payload['referred_by'] = $referredBy->id ?? null;

        do {
            $referralToken = Str::random(5);
            $isReferralTokenExists = User::whereReferralToken($referralToken)->exists();
        } while ($isReferralTokenExists);

        $payload['referral_token'] = $referralToken;

        $user = User::create($payload);

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

        abort_if(! $token, 422, 'Incorrect password');

        return response()->json([
            'token' => $token,
            'user' => new UserResource(auth()->user()),
        ]);
    }

    public function getAuthUser(): JsonResponse
    {
        return response()->json([
            'user' => new UserResource(auth()->user()),
        ]);
    }
}
