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
            'points' => $completedTask->points * CompletedTask::COMMISSION_PERCENT_REFERRAL,
            'data' => [
                'completed_task_id' => $completedTask->id,
            ],
        ]);
    }
}
