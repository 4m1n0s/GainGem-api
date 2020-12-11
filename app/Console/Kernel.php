<?php

namespace App\Console;

use App\Jobs\RandomGiveawayWinnerJob;
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
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
