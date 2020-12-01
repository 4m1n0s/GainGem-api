<?php

namespace App\Builders;

use App\Models\CompletedTask;
use Illuminate\Database\Eloquent\Builder;

class CompletedTaskBuilder extends Builder
{
    public function availableForReferring(): self
    {
        $this->whereNotIn('type', [CompletedTask::TYPE_REFERRAL_INCOME, CompletedTask::TYPE_COUPON, CompletedTask::TYPE_TASK]);

        return $this;
    }
}
