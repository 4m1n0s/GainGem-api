<?php

namespace Database\Seeders;

use App\Models\RobuxAccount;
use App\Models\User;
use Illuminate\Database\Seeder;

class RobuxGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $supplier = User::where('role', User::ROLE_SUPPLIER)->first();
        $secondSupplier = User::where('role', User::ROLE_SUPPLIER)->skip(1)->first();

        if (! $supplier || ! $secondSupplier) {
            return;
        }

        RobuxAccount::create([
            'supplier_user_id' => $supplier->id,
            'cookie' => config('app.robux_group_cookie'),
            'robux_group_id' => 2820850,
            'robux_owner_id' => 859953059,
            'robux_owner_username' => 'adiryed',
            'robux_amount' => 3000,
        ]);

        RobuxAccount::create([
            'supplier_user_id' => $secondSupplier->id,
            'cookie' => config('app.robux_group_cookie'),
            'robux_group_id' => 123123,
            'robux_owner_id' => 859953059,
            'robux_owner_username' => 'adiryed',
            'robux_amount' => 3000,
            'created_at' => now()->addHour(),
            'updated_at' => now()->addHour(),
        ]);
    }
}
