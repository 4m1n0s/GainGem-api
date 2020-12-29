<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

class CreateGlobalRate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Cache::rememberForever('global-rate', static fn () => 6);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Cache::forget('global-rate');
    }
}
