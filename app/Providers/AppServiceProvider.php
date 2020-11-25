<?php

namespace App\Providers;

use App\Models\CompletedTask;
use App\Observers\CompletedTaskObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        CompletedTask::observe(CompletedTaskObserver::class);
    }
}
