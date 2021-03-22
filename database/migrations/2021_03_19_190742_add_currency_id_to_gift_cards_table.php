<?php

use App\Models\Currency;
use App\Models\GiftCard;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyIdToGiftCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gift_cards', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->constrained()->onDelete('set null');
        });

        $currency = Currency::where('currency', 'USD')->first();

        if ($currency) {
            GiftCard::get()->each(static function (GiftCard $giftCard) use ($currency) {
                $giftCard->update([
                    'currency_id' => $currency->id,
                ]);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gift_cards', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });
    }
}
