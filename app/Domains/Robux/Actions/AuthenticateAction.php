<?php

namespace App\Domains\Robux\Actions;

use App\Models\RobuxAccount;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class AuthenticateAction
{
    /**
     * @param RobuxAccount $robuxAccount
     * @return string
     * @throws RequestException
     */
    public function execute(RobuxAccount $robuxAccount): string
    {
        $csrfToken = '';

        try {
            Http::withOptions([
                'proxy' => config('app.proxy_url'),
                'headers' => [
                    'cookie' => '.ROBLOSECURITY='.$robuxAccount->cookie,
                ],
            ])->post('https://auth.roblox.com/v2/login')->throw();
        } catch (RequestException $exception) {
            $csrfToken = $exception->response->header('x-csrf-token');

            if (! $csrfToken) {
                throw $exception;
            }
        }

        return $csrfToken;
    }
}
