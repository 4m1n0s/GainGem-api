<?php

namespace App\Http\Controllers;

use App\Models\UrlToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserVerificationController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $urlToken = UrlToken::whereToken($request->token)
            ->whereType(UrlToken::TYPE_VERIFICATION)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $user = $urlToken->user;

        abort_if((bool) $user->email_verified_at, 422, 'verified');

        $user->markUnreadNotificationAsRead($urlToken->id);
        $user->markEmailAsVerified();
        $user->increment('points', 2);
        $user->increment('total_points_earned', 2);

        return response()->json([
            'user' => $user
        ]);
    }
}
