<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResendVerificationRequest;
use App\Models\UrlToken;
use App\Models\User;
use App\Notifications\VerifyUserNotification;

class ResendVerificationController extends Controller
{
    public function __invoke(ResendVerificationRequest $request): void
    {
        $payload = $request->validated();

        $user = User::whereEmail($payload['email'])->firstOrFail();

        abort_if((bool) $user->email_verified_at, 422, 'verified');

        /** @var UrlToken $urlToken */
        $urlToken = $user->urlTokens()->create([
            'type' => UrlToken::TYPE_VERIFICATION,
            'token' => UrlToken::getRandomToken(),
            'expires_at' => now()->addDay(),
        ]);

        $user->notify(new VerifyUserNotification($urlToken));
    }
}
