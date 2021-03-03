<?php

namespace App\Http\Controllers;

use App\Models\RobuxAccount;
use App\Services\Robux;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RefreshRobuxAccountController extends Controller
{
    public function store(RobuxAccount $robuxAccount): JsonResponse
    {
        try {
            $currency = Robux::getCurrencyResponse($robuxAccount);
        } catch (RequestException $exception) {
            Log::error('Get currency failed', [
                'robux_account_id' => $robuxAccount->id,
                'response' => $exception->response->json(),
                'status' => $exception->response->status(),
            ]);

            if (isset($exception->response['errors']) && ($exception->response['errors'][0]['code'] === 0 || $exception->response['errors'][0]['code'] === 1)) {
                $robuxAccount->delete();
                throw new HttpException(404, 'The account has been removed because of an invalid cookie or account id.');
            }

            $robuxAccount->update([
                'robux_amount' => 0,
                'disabled_at' => now(),
            ]);

            return response()->json([
                'message' => 'An error occurred while refreshing the account.',
                'account' => $robuxAccount->makeHidden('cookie'),
            ], 422);
        }

        $robuxUser = Robux::getUserById($robuxAccount->robux_account_id);

        $robuxAccount->update([
            'robux_amount' => $currency['robux'],
            'robux_account_username' => $robuxUser['Username'],
            'disabled_at' => $currency['robux'] >= RobuxAccount::MIN_ROBUX_AMOUNT ? null : now(),
        ]);

        return response()->json($robuxAccount->makeHidden('cookie'));
    }
}
