<?php

namespace App\Console;

use App\Jobs\RandomGiveawayWinnerJob;
use App\Jobs\RefreshRobuxAccountsJob;
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

        $schedule->job(new RefreshRobuxAccountsJob)
            ->hourly()
            ->description('Refresh robux accounts.');

        $schedule->command('telescope:prune --hours=48')
            ->hourly()
            ->description('Prune old Telescope records.');

        $schedule->command('backup:clean')->daily()->at('01:00');
        $schedule->command('backup:run --only-db')->daily()->at('01:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
