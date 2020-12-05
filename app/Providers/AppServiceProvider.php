<?php

namespace App\Providers;

use App\Models\CompletedTask;
use App\Observers\CompletedTaskObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        CompletedTask::observe(CompletedTaskObserver::class);

        Builder::macro('whereLike', function (string $attribute, string $searchTerm) {
            /* @var Builder $this */
            return $this->where($attribute, 'LIKE', "%{$searchTerm}%");
        });
    }
}
