<?php

namespace App\Jobs;

use App\Models\RobuxAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RefreshRobuxAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        RobuxAccount::where('refresh_at', '<', now())
            ->get()
            ->each(static function (RobuxAccount $robuxAccount) {
                $robuxAccount->refreshData();
            });
    }
}
