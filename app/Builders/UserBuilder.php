<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class UserBuilder extends Builder
{
    public function WithTotalPoints(): self
    {
        $this->withSum('completedTasks as total_points', 'points');

        return $this;
    }

    public function WithWastedPoints(): self
    {
        $this->withSum('transactions as wasted_points', 'points');

        return $this;
    }

    public function WithAvailablePoints(): self
    {
        $this->withTotalPoints()->withWastedPoints();

        return $this;
    }
}
