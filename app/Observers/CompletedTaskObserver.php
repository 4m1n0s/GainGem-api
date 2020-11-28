<?php

namespace App\Observers;

use App\Models\CompletedTask;

class CompletedTaskObserver
{
    public function created(CompletedTask $completedTask): void
    {
        $completedTaskByUser = $completedTask->user;

        if (! $completedTaskByUser->referredBy || ! $completedTask->isTypeAvailableForReferring()) {
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
