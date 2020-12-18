<?php

use Illuminate\Support\Facades\Request;

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

function points_format(?float $points): string
{
    if (! $points) {
        return '0';
    }

    $points = sprintf((string) $points);
    $decimals = strrchr($points, '.') !== false ? strlen(substr(strrchr($points, '.'), 1)) : 0;

    if ((float) ($points) === 0.00) {
        return '0';
    }

    return number_format((float) ($points), $decimals <= 2 ? $decimals : 2);
}

function get_countries(): array
{
    $file = file_get_contents(base_path()."\\vendor\samayo\country-json\src\country-by-name.json");

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
