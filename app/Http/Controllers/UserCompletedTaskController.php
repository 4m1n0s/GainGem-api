<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserCompletedTaskController extends Controller
{
    public function show(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        /** @var User $authUser */
        $authUser = auth()->user();

        return response()->json([
            'activities' => $user->completedTasks()
                ->when($authUser->id === $user->id, static function ($query) {
                    $query->select(['id', 'type', 'provider', 'user_id', 'points', 'created_at']);
                })
                ->orderByDesc('id')
                ->get(),
        ]);
    }
}
