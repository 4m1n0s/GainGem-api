<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

class UpdateDefaultPointsValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Cache::forget('points-value');
        Cache::rememberForever('points-value', static fn () => 80);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Cache::forget('points-value');
        Cache::rememberForever('points-value', static fn () => 40);
    }
}
