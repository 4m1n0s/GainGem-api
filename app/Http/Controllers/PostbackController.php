<?php

namespace App\Http\Controllers;

use App\Domains\Postback\Actions\FraudAction;
use App\Http\Requests\StoreLootablyPostbackRequest;
use App\Http\Requests\StorePostbackRequest;
use App\Models\CompletedTask;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class PostbackController extends Controller
{
    private int $postbackValue;

    public function __construct()
    {
        $this->postbackValue = (int) Cache::get('postback-value');

        if (! $this->postbackValue) {
            $this->postbackValue = 40;
        }
    }

    public function store(StorePostbackRequest $request): int
    {
        $payload = $request->validated();

        $data = [
            'type' => CompletedTask::TYPE_OFFER,
            'provider' => $payload['app'],
            'user_id' => $payload['user_id'],
            'points' => $payload['payout'] * $this->postbackValue,
            'data' => [
                'transaction_id' => $payload['transaction_id'],
                'offer_name' => $payload['offername'],
                'revenue' => $payload['payout'],
                'user_ip' => $payload['user_ip'],
            ],
        ];

        $isChargeback = isset($payload['status']) && ($payload['app'] === 'CPX Research' && (int) $payload['status'] === 2 || $payload['app'] === 'Adgate Media' && (int) $payload['status'] === 0);

        if ($payload['payout'] < 0 || $isChargeback) {
            $data['type'] = CompletedTask::TYPE_CHARGEBACK;
            $data['points'] = -abs($data['points']);
        }

        CompletedTask::create($data);

        /** @var User $user */
        $user = User::find($payload['user_id']);
        (new FraudAction($user))->execute();

        return 1;
    }

    public function lootably(StoreLootablyPostbackRequest $request): int
    {
        $payload = $request->validated();

        CompletedTask::create([
            'type' => $payload['payout'] > 0 ? CompletedTask::TYPE_OFFER : CompletedTask::TYPE_CHARGEBACK,
            'provider' => 'Lootably',
            'user_id' => $payload['user_id'],
            'points' => $payload['payout'] * $this->postbackValue,
            'data' => [
                'transaction_id' => $payload['transaction_id'],
                'offer_name' => $payload['offername'],
                'revenue' => $payload['payout'],
                'user_ip' => $payload['user_ip'],
            ],
        ]);

        /** @var User $user */
        $user = User::find($payload['user_id']);
        (new FraudAction($user))->execute();

        return 1;
    }
}
