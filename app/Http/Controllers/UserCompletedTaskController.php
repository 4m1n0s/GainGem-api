<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserCompletedTaskController extends Controller
{
    public function show(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        return response()->json([
            'activities' => $user->completedTasks()
                ->when(auth()->user()->id === $user->id, static function ($query) {
                    $query->select(['id', 'type', 'provider', 'user_id', 'points', 'created_at']);
                })
                ->orderByDesc('id')
                ->get(),
        ]);
    }
}
