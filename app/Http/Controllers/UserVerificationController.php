<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
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
        $user->incrementPoints(2);

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }
}
