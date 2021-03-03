<?php

namespace App\Domains\Postback\Actions;

use App\Models\CompletedTask;
use App\Models\User;

class FraudAction
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function execute(): void
    {
        if ($this->user->froze_at) {
            return;
        }

        if ($this->user->created_at > now()->subMonth() && $this->lastOfferRevenueHigherThan(7.5) ||
            $this->lastDayRevenue() > 20 ||
            $this->amountOfCompletedOffersFromLastMinutes(1) >= 10
        ) {
            $this->user->update(['froze_at' => now()]);

            return;
        }
    }

    protected function lastOfferRevenueHigherThan(float $value): bool
    {
        /** @var CompletedTask $lastOffer */
        $lastOffer = $this->user->completedTasks()
            ->where('type', CompletedTask::TYPE_OFFER)
            ->whereNotNull('data->revenue')
            ->latest()
            ->first(['user_id', 'data']);

        return isset($lastOffer->data['revenue']) && (float) $lastOffer->data['revenue'] > $value;
    }

    protected function lastDayRevenue(): float
    {
        return $this->user->completedTasks()
            ->where('type', CompletedTask::TYPE_OFFER)
            ->whereNotNull('data->revenue')
            ->where('created_at', '>', now()->subDay())
            ->sum('data->revenue');
    }

    protected function amountOfCompletedOffersFromLastMinutes(int $minutes): int
    {
        return $this->user->completedTasks()
            ->where('type', CompletedTask::TYPE_OFFER)
            ->where('created_at', '>', now()->subMinutes($minutes))
            ->count();
    }
}
