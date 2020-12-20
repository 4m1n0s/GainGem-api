<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckTokenRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\SetPasswordRequest;
use App\Models\UrlToken;
use App\Models\User;
use App\Notifications\ForgotPasswordNotification;

class ForgotPasswordController extends Controller
{
    public function store(ForgotPasswordRequest $request): void
    {
        $payload = $request->validated();

        $user = User::whereEmail($payload['email'])->firstOrFail();

        abort_if(! $user->hasVerifiedEmail(), 422, 'Please verify your email before requesting a password reset');

        /** @var UrlToken $urlToken */
        $urlToken = $user->urlTokens()->create([
            'type' => UrlToken::TYPE_FORGOT_PASSWORD,
            'token' => UrlToken::getRandomToken(),
            'expires_at' => now()->addDay(),
        ]);

        $user->notify(new ForgotPasswordNotification($urlToken));
    }

    public function checkToken(CheckTokenRequest $request): void
    {
        $payload = $request->validated();

        UrlToken::whereToken($payload['token'])
            ->whereType(UrlToken::TYPE_FORGOT_PASSWORD)
            ->where('expires_at', '>', now())
            ->firstOrFail();
    }

    public function resetPassword(SetPasswordRequest $request): void
    {
        $payload = $request->validated();

        $urlToken = UrlToken::whereToken($payload['token'])
            ->whereType(UrlToken::TYPE_FORGOT_PASSWORD)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $user = $urlToken->user;

        $urlToken->update([
            'expires_at' => null,
        ]);

        $user->markNotificationAsRead($urlToken->id);
        $user->update([
            'password' => $payload['password'],
        ]);
    }
}
