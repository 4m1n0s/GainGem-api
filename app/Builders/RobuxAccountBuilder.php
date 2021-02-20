<?php

namespace App\Builders;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method static \Illuminate\Database\Query\Builder|\App\Models\RobuxAccount withTrashed()
 */
class RobuxAccountBuilder extends Builder
{
    public function withTotalEarnings(): self
    {
        $this->withSum('transactions as total_earnings', 'value');

        return $this;
    }

    public function withTotalWithdrawn(): self
    {
        $this->withSum('transactions as total_withdrawn', 'robux_amount');

        return $this;
    }

    public function bestMatch(): self
    {
        $this->whereNull('disabled_at')
            ->addSelect([
                'total_monthly_earnings' => Transaction::selectRaw('CAST(IFNULL(SUM(`robux_amount`), 0) AS UNSIGNED)')
                    ->whereColumn('transactions.robux_account_id', 'robux_accounts.id')
                    ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]),
                ])
            ->orderBy('total_monthly_earnings')
            ->orderBy('created_at');

        return $this;
    }
}
