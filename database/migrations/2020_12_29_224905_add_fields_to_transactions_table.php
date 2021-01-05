<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedDecimal('value')->change();
            $table->foreignId('robux_group_id')->nullable()->index()->constrained();
            $table->unsignedInteger('robux_amount')->nullable();
            $table->unsignedDecimal('bitcoin_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('value');
            $table->dropForeign(['robux_group_id']);
            $table->dropColumn('robux_group_id');
            $table->dropColumn('robux_amount');
            $table->dropColumn('bitcoin_amount');
        });
    }
}
