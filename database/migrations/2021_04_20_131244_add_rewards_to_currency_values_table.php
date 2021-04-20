<?php

use App\Models\GiftCard;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRewardsToCurrencyValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('currency_values', function (Blueprint $table) {
            $table->unsignedTinyInteger(GiftCard::PROVIDER_STEAM);
            $table->unsignedTinyInteger(GiftCard::PROVIDER_FORTNITE);
            $table->unsignedTinyInteger(GiftCard::PROVIDER_VALORANT);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currency_values', function (Blueprint $table) {
            $table->dropColumn(GiftCard::PROVIDER_STEAM);
            $table->dropColumn(GiftCard::PROVIDER_FORTNITE);
            $table->dropColumn(GiftCard::PROVIDER_VALORANT);
        });
    }
}
