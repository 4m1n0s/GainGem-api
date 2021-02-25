<?php

namespace App\Jobs;

use App\Models\RobuxAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class RefreshRobuxAccountsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $jobs = RobuxAccount::get()->map(static function (RobuxAccount $robuxAccount): RefreshRobuxAccountJob {
            return new RefreshRobuxAccountJob($robuxAccount);
        });

        Bus::batch($jobs)->allowFailures()->dispatch();
    }
}
