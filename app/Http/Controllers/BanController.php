<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBanRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class BanController extends Controller
{
    public function store(User $user, StoreBanRequest $request): JsonResponse
    {
        $payload = $request->validated();

        abort_if((bool) $user->banned_at, 422, 'User is already banned');
        abort_if($user->isSuperAdminRole() || $user->isAdminRole(), 422, 'Cannot ban admins!');

        $user->update([
            'banned_at' => now(),
            'ban_reason' => $payload['ban_reason'],
            'froze_at' => null,
        ]);

        return response()->json([
            'user' => $user->append('formatted_banned_at'),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        abort_if(! $user->banned_at, 422, 'User is not banned');

        $user->update([
            'banned_at' => null,
            'ban_reason' => null,
            'froze_at' => null,
        ]);

        return response()->json([
            'user' => $user,
        ]);
    }
}
