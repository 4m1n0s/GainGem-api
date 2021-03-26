<?php

namespace App\Domains\Robux\Actions;

use App\Models\RobuxAccount;
use ErrorException;
use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Spatie\QueueableAction\QueueableAction;

class UnsubscribePaymentAction
{
    use QueueableAction;

    public int $tries = 10;

    public int $backoff = 60;

    /**
     * @param RobuxAccount $robuxAccount
     * @param int $vipServerId
     * @param int $amount
     * @throws RequestException
     * @throws Exception
     */
    public function execute(RobuxAccount $robuxAccount, int $vipServerId, int $amount): void
    {
        $csrfToken = (new AuthenticateAction)->execute($robuxAccount);

        Http::withOptions([
            'proxy' => config('app.proxy_url'),
            'headers' => [
                'X-CSRF-TOKEN' => $csrfToken,
                'cookie' => '.ROBLOSECURITY='.$robuxAccount->cookie,
            ],
        ])->patch("https://games.roblox.com/v1/vip-servers/{$vipServerId}/subscription", [
            'active' => false,
            'price' => $amount,
        ])->throw();
    }

    /**
     * @param ErrorException $exception
     */
    public function failed(ErrorException $exception): void
    {
        app('sentry')->captureException($exception);
    }
}
