<?php

use Stevebauman\Location\Facades\Location;
use Stevebauman\Location\Position;

function get_ip(): string
{
    foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
        if (array_key_exists($key, $_SERVER)) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }

    /** @var string $ip */
    $ip = request()->ip();

    return $ip;
}

function currency_format(?float $points, int $maxDecimals = 2): string
{
    if (! $points) {
        return '0';
    }

    $points = sprintf((string) $points);
    $decimals = strrchr($points, '.') !== false ? strlen(substr(strrchr($points, '.'), 1)) : 0;

    if ((float) ($points) === 0.00) {
        return '0';
    }

    return number_format((float) ($points), $decimals <= $maxDecimals ? $decimals : $maxDecimals);
}

function get_countries(): array
{
    $file = file_get_contents(base_path().'/vendor/samayo/country-json/src/country-by-name.json');

    return array_column(json_decode((string) $file, true), 'country');
}

function get_bitcoin_value(): float
{
    $response = Http::get('https://bitpay.com/api/rates');
    $usd = 0;

    foreach ($response->json() as $obj) {
        if ($obj['code'] === 'USD') {
            $usd = $obj['rate'];
            break;
        }
    }

    return 1 / $usd;
}

function convert_satoshi_to_bitcoin(int $satoshi): float
{
    return $satoshi / pow(10, 8);
}

function convert_satoshi_to_usd(int $satoshi): float
{
    return convert_satoshi_to_bitcoin($satoshi) / get_bitcoin_value();
}

function get_full_location(string $ip): ?string
{
    $location = Location::get($ip);

    return $location instanceof Position ? "{$location->regionName}, {$location->cityName}, {$location->countryName}" : null;
}
