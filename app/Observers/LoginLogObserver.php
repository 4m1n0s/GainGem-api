<?php

namespace App\Observers;

use App\Models\LoginLog;
use App\Notifications\LoginLogChangedNotification;

class LoginLogObserver
{
    public function created(LoginLog $loginLog): void
    {
        $user = $loginLog->user;

        if (! $user->email_verified_at || $loginLog->ip === $loginLog->previous_ip) {
            return;
        }

        $user->notify(new LoginLogChangedNotification($loginLog));
    }
}
