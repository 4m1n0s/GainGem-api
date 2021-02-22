<?php

namespace App\Console;

use App\Jobs\RandomGiveawayWinnerJob;
use App\Jobs\RefreshRobuxAccountJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /** @var string[] */
    protected $commands = [
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new RandomGiveawayWinnerJob)
            ->hourly()
            ->description('Pick a random winner to the latest giveaway and create a new one');

        $schedule->job(new RefreshRobuxAccountJob)
            ->everyTenMinutes()
            ->description('Refresh required robux accounts.');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
