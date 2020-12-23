<?php

namespace App\Events;

use App\Models\CompletedTask;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompletedTaskCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public CompletedTask $activity;

    public function __construct(CompletedTask $completedTask)
    {
        if (! $completedTask->relationLoaded('user')) {
            $completedTask->load('user:id,username,profile_image');
        }

        $this->activity = $completedTask;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('activities');
    }

    public function broadcastAs(): string
    {
        return 'activities.created';
    }
}
