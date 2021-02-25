<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveRefreshAtFromRobuxAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('robux_accounts', function (Blueprint $table) {
            $table->dropColumn('refresh_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('robux_accounts', function (Blueprint $table) {
            $table->timestamp('refresh_at')->nullable();
        });
    }
}
