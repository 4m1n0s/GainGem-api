<?php

namespace App\Listeners;

use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

class GenerateModelsIdeHelperListener
{
    public function handle(MigrationsEnded $event): void
    {
        if (! App::isLocal()) {
            return;
        }

        Artisan::call('ide-helper:models', [
            '--reset' => true,
            '--write' => true,
        ]);
    }
}
