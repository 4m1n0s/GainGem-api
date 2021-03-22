<?php

use App\Models\Currency;
use App\Models\GiftCard;
use Illuminate\Database\Migrations\Migration;

class CreateDollarCurrency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $currency = Currency::create([
            'currency' => 'USD',
            'name' => 'Dollar',
            'symbol' => '$',
        ]);

        $currencyValue = [];

        foreach (GiftCard::PROVIDERS as $provider) {
            $currencyValue[$provider] = 100;
        }

        $currency->currencyValue()->create($currencyValue);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Currency::where('currency', 'USD')->delete();
    }
}
