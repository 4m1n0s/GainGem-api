<?php

namespace App\Domains\Robux\Actions;

use App\Models\RobuxAccount;
use App\Services\Robux;

class GetRelevantAccountAction
{
    public function execute(int $value): ?RobuxAccount
    {
        /** @var RobuxAccount|null */
        return RobuxAccount::bestMatch()
            ->where('robux_amount', '>', $value)
            ->get()
            ->first(static function (RobuxAccount $robuxAccount) use ($value): bool {
                $robuxAmount = Robux::getCurrency($robuxAccount);

                $robuxAccount->update([
                    'robux_amount' => $robuxAmount,
                    'disabled_at' => $robuxAmount < RobuxAccount::MIN_ROBUX_AMOUNT ? now() : null,
                ]);

                return $robuxAmount >= $value;
            });
    }
}
