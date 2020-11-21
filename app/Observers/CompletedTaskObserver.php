<?php

namespace App\Observers;

use App\Models\CompletedTask;
use App\Models\User;

class CompletedTaskObserver
{
    public function created(CompletedTask $completedTask)
    {
        /** @var User $completedTaskByUser */
        $completedTaskByUser = $completedTask->user;

        if (auth()->id() !== $completedTaskByUser->id || is_null($completedTaskByUser->referred_by)) {
            return;
        }

        $completedTaskByUser->referredBy->completedTasks()->create([
            'type' => CompletedTask::TYPE_REFERRAL_INCOME,
            'points' => $completedTask->points / 10,
            'data' => [
                'completed_task_id' => $completedTask->id,
            ],
        ]);

    }
}
