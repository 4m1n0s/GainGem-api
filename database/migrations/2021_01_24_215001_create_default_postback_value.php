<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

class CreateDefaultPostbackValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Cache::rememberForever('postback-value', static fn () => 40);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Cache::forget('postback-value');
    }
}
