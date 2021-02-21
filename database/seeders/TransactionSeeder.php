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
        $robuxAccount = RobuxAccount::first();
        $secondRobuxAccount = RobuxAccount::skip(1)->first();

        if (! $user || ! $robuxAccount || ! $secondRobuxAccount) {
            return;
        }

        $rate = Cache::get('robux-supplier-rate') ?? 6 / 1000;

        Transaction::create([
            'user_id' => $user->id,
            'type' => Transaction::TYPE_ROBUX,
            'points' => 10,
            'value' => 10 * $rate,
            'robux_account_id' => $robuxAccount->id,
            'robux_amount' => 10,
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'type' => Transaction::TYPE_ROBUX,
            'points' => 5,
            'value' => 5 * $rate,
            'robux_account_id' => $secondRobuxAccount->id,
            'robux_amount' => 5,
        ]);
    }
}
