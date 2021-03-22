<?php

use App\Models\GiftCard;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrencyValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('currency_id')->unique();

            foreach (GiftCard::PROVIDERS as $provider) {
                $table->unsignedTinyInteger($provider);
            }

            $table->timestamps();

            $table->foreign('currency_id')->references('id')->on('currencies')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_values');
    }
}
