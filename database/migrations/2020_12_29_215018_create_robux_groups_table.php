<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRobuxGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('robux_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_user_id')->index()->constrained('users');
            $table->text('cookie');
            $table->unsignedBigInteger('robux_group_id');
            $table->unsignedBigInteger('robux_owner_id');
            $table->string('robux_owner_username');
            $table->unsignedBigInteger('robux_amount');
            $table->timestamp('disabled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('robux_groups');
    }
}
