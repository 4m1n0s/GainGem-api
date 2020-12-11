<?php

namespace App\Events;

use App\Models\CompletedTask;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GiveawayCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $current_giveaway;
    public array $recent_giveaway_winners;

    public function __construct(CompletedTask $completedTask)
    {
        $this->current_giveaway = $completedTask->only(['points', 'created_at']);

        $this->recent_giveaway_winners = CompletedTask::where('type', CompletedTask::TYPE_GIVEAWAY)
            ->whereNotNull('user_id')
            ->with('user:id,username,profile_image')
            ->orderByDesc('id')
            ->limit(10)
            ->get(['type', 'user_id', 'points', 'updated_at'])
            ->toArray();
    }

    public function broadcastOn(): Channel
    {
        return new Channel('giveaways');
    }

    public function broadcastAs(): string
    {
        return 'giveaway.created';
    }
}
