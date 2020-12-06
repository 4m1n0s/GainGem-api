<?php

namespace App\Http\Controllers;

use App\Models\CompletedTask;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserCompletedTaskController extends Controller
{
    public function show(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $completedTasks = $user->completedTasks()
            ->get()
            ->each(static function (CompletedTask $completedTask) {
                $completedTask['formatted_created_at'] = $completedTask->created_at ? $completedTask->created_at->format('M d Y') : $completedTask->created_at;
            });

        return response()->json([
            'activities' => $completedTasks,
        ]);
    }
}
