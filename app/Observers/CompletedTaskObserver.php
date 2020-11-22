<?php

namespace App\Observers;

use App\Models\CompletedTask;
use App\Models\User;

class CompletedTaskObserver
{
    public function created(CompletedTask $completedTask)
    {
        $completedTaskByUser = $completedTask->user;

        if (is_null($completedTaskByUser->referred_by) || ! $completedTask->isTypeAvailableForReferring()) {
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
