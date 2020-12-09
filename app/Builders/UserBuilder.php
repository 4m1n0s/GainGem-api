<?php

namespace App\Builders;

class UserBuilder extends FullTextSearchBuilder
{
    public function withTotalPoints(): self
    {
        $this->withSum('completedTasks as total_points', 'points');

        return $this;
    }

    public function withWastedPoints(): self
    {
        $this->withSum('transactions as wasted_points', 'points');

        return $this;
    }

    public function withAvailablePoints(): self
    {
        $this->withTotalPoints()->withWastedPoints();

        return $this;
    }
}
