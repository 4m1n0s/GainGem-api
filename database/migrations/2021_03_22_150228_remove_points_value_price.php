<?php

use Illuminate\Database\Migrations\Migration;

class RemovePointsValuePrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Cache::forget('points-value');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
