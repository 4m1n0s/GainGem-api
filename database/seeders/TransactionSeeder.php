<?php

namespace Database\Seeders;

use App\Models\RobuxGroup;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

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
        $robuxGroup = RobuxGroup::first();
        $secondRobuxGroup = RobuxGroup::skip(1)->first();

        if (! $user || ! $robuxGroup || ! $secondRobuxGroup) {
            return;
        }

        Transaction::create([
            'user_id' => $user->id,
            'type' => Transaction::TYPE_ROBUX,
            'points' => 10,
            'value' => 10,
            'robux_group_id' => $robuxGroup->id,
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'type' => Transaction::TYPE_ROBUX,
            'points' => 5,
            'value' => 5,
            'robux_group_id' => $secondRobuxGroup->id,
        ]);
    }
}
