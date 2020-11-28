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
        $user = User::where('email', $request->get('email'))
            ->whereNull('email_verified_at')
            ->firstOrFail();

        /** @var UrlToken $urlToken */
        $urlToken = $user->urlTokens()->create([
            'type' => UrlToken::TYPE_VERIFICATION,
            'token' => UrlToken::getRandomToken(),
            'expires_at' => now()->addDay(),
        ]);

        $user->notify(new VerifyUserNotification($urlToken));
    }
}
