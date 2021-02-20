<?php

namespace Database\Seeders;

use App\Models\RobuxAccount;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::first();
        $robuxGroup = RobuxAccount::first();
        $secondRobuxGroup = RobuxAccount::skip(1)->first();

        if (! $user || ! $robuxGroup || ! $secondRobuxGroup) {
            return;
        }

        $rate = Cache::get('robux-supplier-rate') ?? 6 / 1000;

        Transaction::create([
            'user_id' => $user->id,
            'type' => Transaction::TYPE_ROBUX,
            'points' => 10,
            'value' => 10 * $rate,
            'robux_group_id' => $robuxGroup->id,
            'robux_amount' => 10,
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'type' => Transaction::TYPE_ROBUX,
            'points' => 5,
            'value' => 5 * $rate,
            'robux_group_id' => $secondRobuxGroup->id,
            'robux_amount' => 5,
        ]);
    }
}
