<?php

namespace App\Providers;

use App\Listeners\GenerateModelsIdeHelperListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /** @var array<string, array<string>> */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        MigrationsEnded::class => [
            GenerateModelsIdeHelperListener::class,
        ],
    ];

    public function boot(): void
    {
    }
}
