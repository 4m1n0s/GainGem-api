<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\CompletedTask;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class GiveawayController extends Controller
{
    public function recently(): JsonResponse
    {
        $currentGiveaway = CompletedTask::where('type', CompletedTask::TYPE_GIVEAWAY)
            ->whereNull('user_id')
            ->select(['points', 'created_at'])
            ->orderByDesc('id')
            ->first();
        $recentGiveawayEntries = User::whereNotNull('registered_giveaway_at')
            ->orderByDesc('registered_giveaway_at')
            ->limit(10)
            ->get(['username', 'profile_image', 'registered_giveaway_at']);
        $recentGiveawayWinners = CompletedTask::where('type', CompletedTask::TYPE_GIVEAWAY)
            ->whereNotNull('user_id')
            ->with('user:id,username,profile_image')
            ->orderByDesc('id')
            ->limit(10)
            ->get(['type', 'user_id', 'points', 'created_at'])
            ->append('won_at');

        return response()->json([
            'current_giveaway' => $currentGiveaway,
            'recent_giveaway_entries' => $recentGiveawayEntries,
            'recent_giveaway_winners' => $recentGiveawayWinners,
        ]);
    }

    public function enter(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        abort_if((bool) $user->registered_giveaway_at, 422, 'Already entered the giveaway!');

        $user->update([
            'registered_giveaway_at' => now(),
        ]);
        // @todo websockets
        return response()->json([
            'user' => new UserResource($user->withAvailablePoints()),
        ]);
    }
}
