<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegisteredGiveaway implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $recent_giveaway_entries;

    public function __construct()
    {
        $this->recent_giveaway_entries = User::whereNotNull('registered_giveaway_at')
            ->orderByDesc('registered_giveaway_at')
            ->limit(10)
            ->get(['username', 'profile_image', 'registered_giveaway_at'])
            ->toArray();
    }

    public function broadcastOn(): Channel
    {
        return new Channel('giveaways');
    }

    public function broadcastAs(): string
    {
        return 'giveaway.registration';
    }
}
