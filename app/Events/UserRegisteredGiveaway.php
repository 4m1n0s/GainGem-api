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

    public array $user;

    public function __construct(User $user)
    {
        $this->user = $user->only(['username', 'profile_image_url', 'registered_giveaway_at']);
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
