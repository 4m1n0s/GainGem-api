<?php

namespace App\Jobs;

use App\Models\RobuxAccount;
use App\Services\Robux;
use ErrorException;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RefreshRobuxAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public RobuxAccount $robuxAccount;

    public int $tries = 10;

    public int $backoff = 60;

    public function __construct(RobuxAccount $robuxAccount)
    {
        $this->robuxAccount = $robuxAccount;
    }

    public function handle(): void
    {
        try {
            $currency = Robux::getCurrencyResponse($this->robuxAccount);
        } catch (RequestException $exception) {
            Log::error('Get currency failed', [
                'robux_account_id' => $this->robuxAccount->id,
                'response' => $exception->response->json(),
                'status' => $exception->response->status(),
            ]);

            if (isset($exception->response['errors']) && ($exception->response['errors'][0]['code'] === 0 || $exception->response['errors'][0]['code'] === 1)) {
                $this->robuxAccount->delete();

                return;
            }

            throw new Exception($exception);
        }

        $robuxUser = Robux::getUserById($this->robuxAccount->robux_account_id);

        $this->robuxAccount->update([
            'robux_amount' => $currency['robux'],
            'robux_account_username' => $robuxUser['Username'],
            'disabled_at' => $currency['robux'] >= RobuxAccount::MIN_ROBUX_AMOUNT ? null : now(),
        ]);
    }

    public function failed(ErrorException $exception): void
    {
        app('sentry')->captureException($exception);

        $this->robuxAccount->update([
            'robux_amount' => 0,
            'disabled_at' => now(),
        ]);
    }
}
