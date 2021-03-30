<?php

namespace App\Builders;

use App\Models\CompletedTask;
use Illuminate\Database\Eloquent\Builder;

class CompletedTaskBuilder extends Builder
{
    public function availableForReferring(): self
    {
        $this->whereNotIn('type', [CompletedTask::TYPE_REFERRAL_INCOME, CompletedTask::TYPE_PROMO_CODE, CompletedTask::TYPE_DAILY_TASK, CompletedTask::TYPE_CHARGEBACK, CompletedTask::TYPE_ADMIN, CompletedTask::TYPE_SOCIAL_MEDIA]);

        return $this;
    }

    public function todayOffers(): self
    {
        $startOfDay = now()->startOfDay();
        $endOfDay = now()->endOfDay();

        $this->where('type', CompletedTask::TYPE_OFFER)->whereBetween('created_at', [$startOfDay, $endOfDay]);

        return $this;
    }

    public function lastHourOffers(): self
    {
        $previousHour = now()->subHour();
        $now = now();

        $this->where('type', CompletedTask::TYPE_OFFER)->whereBetween('created_at', [$previousHour, $now]);

        return $this;
    }

    public function todayDailyTasks(): self
    {
        $startOfDay = now()->startOfDay();
        $endOfDay = now()->endOfDay();

        $this->where('type', CompletedTask::TYPE_DAILY_TASK)->whereBetween('created_at', [$startOfDay, $endOfDay]);

        return $this;
    }
}
