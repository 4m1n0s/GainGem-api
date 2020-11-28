<?php

namespace App\Providers;

use App\Models\CompletedTask;
use App\Observers\CompletedTaskObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        CompletedTask::observe(CompletedTaskObserver::class);
    }
}
