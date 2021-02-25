<?php

namespace App\Domains\Robux\Actions;

use App\Models\RobuxAccount;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PayoutAction
{
    /**
     * @param RobuxAccount $robuxAccount
     * @param int $gameId
     * @param int $amount
     * @throws RequestException
     */
    public function execute(RobuxAccount $robuxAccount, int $gameId, int $amount): void
    {
        try {
            $game = $this->getGame($gameId);
        } catch (RequestException $e) {
            throw new HttpException(422, 'Game not found.');
        }

        try {
            $csrfToken = (new AuthenticateAction)->execute($robuxAccount);
        } catch (RequestException $e) {
            throw new HttpException(422, 'An error occurred while claiming the robux.');
        }

        try {
            $response = Http::withOptions([
                'proxy' => config('app.proxy_url'),
                'headers' => [
                    'X-CSRF-TOKEN' => $csrfToken,
                    'cookie' => '.ROBLOSECURITY='.$robuxAccount->cookie,
                ],
            ])->post("https://games.roblox.com/v1/games/vip-servers/{$gameId}", [
                'name' => $game['name'],
                'expectedPrice' => $amount,
            ])->throw();
        } catch (RequestException $exception) {
            $errors = $exception->response['errors'];

            abort_if($errors[0]['code'] === 15, 422, "Make sure that the price is {$amount}.");
            abort_if($errors[0]['code'] === 17, 422, "Couldn't find game, please try again later.");

            if ($errors[0]['code'] === 16) {
                $robuxAccount->update(['disabled_at' => now()]);
            }

            abort_if($exception->response->status() === 400, 422, 'Game is invalid or does not exist.');

            throw new HttpException(422, 'Payout has been failed, please try again later.');
        }

        $robuxAccount->update([
            'robux_amount' => $robuxAccount->robux_amount - $amount,
            'disabled_at' => $robuxAccount->robux_amount - $amount < RobuxAccount::MIN_ROBUX_AMOUNT ? now() : null,
        ]);

        (new UnsubscribePaymentAction())->onQueue()->execute($robuxAccount, $response['vipServerId'], $amount);
    }

    /**
     * @param int $id
     * @return array
     * @throws RequestException
     */
    private function getGame(int $id): array
    {
        $response = Http::withOptions([
            'proxy' => config('app.proxy_url'),
        ])->get("https://games.roblox.com/v1/games?universeIds={$id}")->throw()->json();

        if (! $response['data']) {
            throw new HttpException(422, 'Game not found.');
        }

        return $response['data'][0];
    }
}
