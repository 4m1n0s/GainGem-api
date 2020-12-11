<?php

namespace App\Jobs;

use App\Events\GiveawayCreated;
use App\Models\CompletedTask;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RandomGiveawayWinnerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $winUser = User::whereNotNull('registered_giveaway_at')->inRandomOrder()->first();

        $latestGiveawayQuery = CompletedTask::where('type', CompletedTask::TYPE_GIVEAWAY)
            ->whereNull('user_id')
            ->orderByDesc('id')
            ->first();

        if ($latestGiveawayQuery) {
            if (! $winUser) {
                $latestGiveawayQuery->delete();
            } else {
                $latestGiveawayQuery->update([
                    'user_id' => $winUser->id,
                ]);
            }
        }

        User::whereNotNull('registered_giveaway_at')->update([
            'registered_giveaway_at' => null,
        ]);

        $giveaway = CompletedTask::create([
            'type' => CompletedTask::TYPE_GIVEAWAY,
            'points' => rand(CompletedTask::GIVEAWAY_MIN_POINTS, CompletedTask::GIVEAWAY_MAX_POINTS),
        ]);

        GiveawayCreated::dispatch($giveaway);
    }
}
