<?php

namespace App\Observers;

use App\Events\CompletedTaskCreated;
use App\Models\CompletedTask;

class CompletedTaskObserver
{
    public function created(CompletedTask $completedTask): void
    {
        $completedTaskByUser = $completedTask->user;

        if ($completedTaskByUser && ! $completedTask->isTypeReferralIncome() && ! $completedTask->isTypeAdmin()) {
            CompletedTaskCreated::dispatch($completedTask);
        }

        if (! $completedTaskByUser || ! $completedTaskByUser->referredBy || ! $completedTask->isAvailableForReferring()) {
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
