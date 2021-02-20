<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameRobuxGroupsToRobuxAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('robux_groups', function (Blueprint $table) {
            $table->dropColumn('robux_group_id');
            $table->renameColumn('robux_owner_id', 'robux_account_id');
            $table->renameColumn('robux_owner_username', 'robux_account_username');
        });

        Schema::rename('robux_groups', 'robux_accounts');

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('robux_group_id', 'robux_account_id');
        });
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
