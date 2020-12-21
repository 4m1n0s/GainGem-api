<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Bitcoin
{
    public static function getBalance(string $guid, string $password): Response
    {
        return Http::get(config('app.blockchain_app_url')."/merchant/{$guid}/balance", [
            'password' => $password,
        ]);
    }

    public static function getCurrency(): float
    {
        $bitcoin = Cache::get('bitcoin');

        if (! $bitcoin) {
            return 0;
        }

        $response = self::getBalance($bitcoin['guid'], $bitcoin['password']);

        if ($response->failed()) {
            return 0;
        }

        return convert_satoshi_to_usd($response['balance']);
    }

    public static function payout(string $to, int $amount): void
    {
        $bitcoin = Cache::get('bitcoin');

        abort_if(! $bitcoin, 422, 'Bitcoin is out of stock');

        $response = Http::post(config('app.blockchain_app_url')."/merchant/{$bitcoin['guid']}/payment", [
            'password' => $bitcoin['password'],
            'amount' => (int) (get_bitcoin_value() * $amount * pow(10, 8)),
            'to' => $to,
            'from' => 0,
        ]);

        abort_if($response->status() === 500, 422, 'Incorrect wallet!');
        abort_if($response->failed(), 422, 'Payout has been failed, please try again later');

        $bitcoin['stock_amount'] -= (int) round(convert_satoshi_to_usd($response['amounts'][0] + $response['fee']));

        if ($bitcoin['stock_amount'] < 0) {
            $bitcoin['stock_amount'] = 0;
        }

        Cache::forget('bitcoin');
        Cache::rememberForever('bitcoin', static fn () => $bitcoin);
    }
}
