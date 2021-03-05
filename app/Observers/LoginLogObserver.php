<?php

namespace App\Observers;

use App\Models\LoginLog;
use App\Notifications\LoginLogChangedNotification;
use Jenssegers\Agent\Agent;

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

    public function creating(LoginLog $loginLog): void
    {
        $agent = new Agent();

        $loginLog->location = get_full_location($loginLog->ip);
        $loginLog->device = is_string($agent->platform()) ? $agent->platform() : null;
        $loginLog->browser = is_string($agent->browser()) ? $agent->browser() : null;
    }
}
