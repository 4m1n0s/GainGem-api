<?php

namespace App\Services;

use App\Models\RobuxAccount;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Robux
{
    public static function getUserByGroupId(int $groupId): array
    {
        $response = Http::get("https://groups.roblox.com/v1/groups/{$groupId}");

        abort_if($response->failed(), 422, isset($response['errors']) ? $response['errors'][0]['message'] : 'Group not found!');

        return $response['owner'];
    }

    public static function getGroupSettingsResponse(string $cookie, int $groupId): Response
    {
        return Http::withHeaders([
            'cookie' => '.ROBLOSECURITY='.$cookie,
        ])->get("https://groups.roblox.com/v1/groups/{$groupId}/settings");
    }

    /**
     * @param RobuxAccount|array $robuxAccount
     * @return Response
     */
    public static function getCurrencyResponse($robuxAccount): Response
    {
        return Http::withHeaders([
            'cookie' => '.ROBLOSECURITY='.$robuxAccount['cookie'],
        ])->get("https://economy.roblox.com/v1/users/{$robuxAccount['robux_account_id']}/currency");
    }

    /**
     * @param RobuxAccount|array $robuxAccount
     * @return int
     */
    public static function getCurrency($robuxAccount): int
    {
        $response = self::getCurrencyResponse($robuxAccount);

        if ($response->failed()) {
            return 0;
        }

        return $response['robux'];
    }

    public static function payout(RobuxAccount $robuxAccount, int $gameId, int $amount): void
    {
        $game = self::getGameById($gameId)['data'];

        $authResponse = Http::withHeaders([
            'cookie' => '.ROBLOSECURITY='.$robuxAccount->cookie,
        ])->post('https://auth.roblox.com/v2/login');

        $response = Http::withHeaders([
            'X-CSRF-TOKEN' => $authResponse->headers()['x-csrf-token'],
            'cookie' => '.ROBLOSECURITY='.$robuxAccount->cookie,
        ])->post("https://games.roblox.com/v1/games/vip-servers/{$gameId}", [
            'name' => $game[0]['name'],
            'expectedPrice' => $amount,
        ]);

        if ($response->failed()) {
            abort_if($response['errors'][0]['code'] === 15, 422, "Make sure that the price is {$amount}.");
            abort_if($response['errors'][0]['code'] === 17, 422, "Couldn't find game, please try again later.");

            if ($response['errors'][0]['code'] === 16) {
                $robuxAccount->update([
                    'disabled_at' => now(),
                    'refresh_at' => now()->addMinutes(10),
                ]);
            }
        }

        abort_if($response->status() === 400, 422, 'Game is invalid or does not exist.');
        abort_if($response->failed(), 422, 'Payout has been failed, please try again later.');

        $robuxAccount->update([
            'robux_amount' => $robuxAccount->robux_amount - $amount,
            'disabled_at' => $robuxAccount->robux_amount - $amount < RobuxAccount::MIN_ROBUX_AMOUNT ? now() : null,
        ]);

        self::unsubscribePayment($robuxAccount, $response, $amount);
    }

    private static function unsubscribePayment(RobuxAccount $robuxAccount, Response $paymentResponse, int $amount): void
    {
        $authResponse = Http::withHeaders([
            'cookie' => '.ROBLOSECURITY='.$robuxAccount->cookie,
        ])->post('https://auth.roblox.com/v2/login');

        Http::withHeaders([
            'X-CSRF-TOKEN' => $authResponse->headers()['x-csrf-token'],
            'cookie' => '.ROBLOSECURITY='.$robuxAccount->cookie,
        ])->patch("https://games.roblox.com/v1/vip-servers/{$paymentResponse['vipServerId']}/subscription", [
            'active' => false,
            'price' => $amount,
        ]);
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

    public static function getGameById(int $id): array
    {
        $response = Http::get("https://games.roblox.com/v1/games?universeIds={$id}");

        abort_if(! count($response['data']), 422, 'Game not found!');

        return $response->json();
    }

    public static function getGamesByUserId(int $id): array
    {
        $response = Http::get("https://games.roblox.com/v2/users/{$id}/games");

        abort_if(! count($response['data']), 404, 'No places found!');

        return $response->json();
    }

    public static function getGamesByUsername(string $username): array
    {
        $user = self::getUserByUsername($username);

        return self::getGamesByUserId($user['Id']);
    }

    public static function getPlacesIconsByIds(array $ids): array
    {
        $ids = implode(',', $ids);
        $response = Http::get("https://thumbnails.roblox.com/v1/places/gameicons?placeIds={$ids}&size=256x256&format=Png&isCircular=false");

        abort_if($response->failed(), 422, 'No places found!');

        return $response->json();
    }
}
