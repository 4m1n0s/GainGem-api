<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'username' => '123123',
            'password' => '123123',
            'email' => 'adir.yed@gmail.com',
            'email_verified_at' => now(),
            'role' => User::ROLE_SUPER_ADMIN,
            'referral_token' => '123123',
        ]);

        User::create([
            'username' => '321321',
            'password' => '123123',
            'email' => 'adir.yed15@gmail.com',
            'email_verified_at' => now(),
            'role' => User::ROLE_SUPPLIER,
            'referral_token' => '321321',
        ]);

        User::create([
            'username' => '321321s',
            'password' => '123123',
            'email' => 'adir.yed15s@gmail.com',
            'email_verified_at' => now(),
            'role' => User::ROLE_SUPPLIER,
            'referral_token' => '321321s',
        ]);
    }
}
