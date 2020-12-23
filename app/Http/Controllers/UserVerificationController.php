<?php

namespace App\Http\Controllers;

use App\Builders\UserBuilder;
use App\Http\Requests\UserVerificationRequest;
use App\Http\Resources\UserResource;
use App\Models\CompletedTask;
use App\Models\UrlToken;
use Illuminate\Http\JsonResponse;

class UserVerificationController extends Controller
{
    public function __invoke(UserVerificationRequest $request): JsonResponse
    {
        $urlToken = UrlToken::whereToken($request->get('token'))
            ->where('type', UrlToken::TYPE_VERIFICATION)
            ->where('expires_at', '>', now())
            ->whereHas('user', static function (UserBuilder $userBuilder) {
                $userBuilder->whereNull('email_verified_at');
            })
            ->firstOrFail();

        $user = $urlToken->user;

        $user->markNotificationAsRead($urlToken->id);
        $user->markEmailAsVerified();
        $user->completedTasks()->create([
            'type' => CompletedTask::TYPE_EMAIL_VERIFICATION,
            'points' => CompletedTask::POINTS_EMAIL_VERIFICATION,
        ]);

        return response()->json([
            'user' => new UserResource($user->loadAvailablePoints()),
        ]);
    }
}
