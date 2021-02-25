<?php

namespace App\Services;

use App\Jobs\RefreshRobuxAccountJob;
use App\Models\RobuxAccount;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Robux
{
    /**
     * @param RobuxAccount|array $robuxAccount
     * @return Response
     * @throws RequestException
     */
    public static function getCurrencyResponse($robuxAccount): Response
    {
        return Http::withOptions([
            'proxy' => config('app.proxy_url'),
            'headers' => [
                'cookie' => '.ROBLOSECURITY='.$robuxAccount['cookie'],
            ],
        ])->get("https://economy.roblox.com/v1/users/{$robuxAccount['robux_account_id']}/currency")->throw();
    }

    /**
     * @param RobuxAccount|array $robuxAccount
     * @return int
     */
    public static function getCurrency($robuxAccount): int
    {
        try {
            $response = self::getCurrencyResponse($robuxAccount);
        } catch (RequestException $exception) {
            Log::error('Get currency failed', [
                'robux_account_id' => $robuxAccount['id'],
                'response' => $exception->response->json(),
                'status' => $exception->response->status(),
            ]);

            RefreshRobuxAccountJob::dispatch($robuxAccount);

            return 0;
        }

        return $response['robux'];
    }

    public static function getUserByUsername(string $username): array
    {
        $response = Http::get("https://api.roblox.com/users/get-by-username?username={$username}");

        abort_if(isset($response['success']) && ! $response['success'], 422, 'Incorrect username!');

        return $response->json();
    }

    public static function getUserById(int $id): array
    {
        $response = Http::get("https://api.roblox.com/users/{$id}");

        abort_if(isset($response['errors']), 422, 'Incorrect or invalid id!');

        return $response->json();
    }

    public static function getGamesByUserId(int $id): array
    {
        $response = Http::withOptions([
            'proxy' => config('app.proxy_url'),
        ])->get("https://games.roblox.com/v2/users/{$id}/games");

        abort_if(! count($response['data']), 404, 'No places found!');

        return $response->json();
    }

    public static function getGamesByUsername(string $username): array
    {
        $user = self::getUserByUsername($username);

        return self::getGamesByUserId($user['Id'])['data'];
    }

    public static function getPlacesIconsByIds(array $ids): array
    {
        $ids = implode(',', $ids);
        $response = Http::get("https://thumbnails.roblox.com/v1/places/gameicons?placeIds={$ids}&size=256x256&format=Png&isCircular=false");

        abort_if($response->failed(), 422, 'No places found!');

        return $response->json()['data'];
    }
}
