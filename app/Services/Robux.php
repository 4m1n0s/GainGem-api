<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Robux
{
    public static function getGroupSettingsResponse(string $cookie, int $groupId): Response
    {
        return Http::withHeaders([
            'cookie' => '.ROBLOSECURITY='.$cookie,
        ])->get("https://groups.roblox.com/v1/groups/{$groupId}/settings");
    }

    public static function getCurrency(): int
    {
        $robux = Cache::get('robux');

        if (! $robux) {
            return 0;
        }

        $response = Http::withHeaders([
            'cookie' => '.ROBLOSECURITY='.$robux['cookie'],
        ])->get("https://economy.roblox.com/v1/groups/{$robux['group_id']}/currency");

        if ($response->failed()) {
            return 0;
        }

        return $response['robux'];
    }
}
