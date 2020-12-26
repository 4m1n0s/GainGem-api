<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLootablyPostbackRequest;
use App\Http\Requests\StorePostbackRequest;
use App\Models\CompletedTask;
use Illuminate\Support\Facades\Cache;

class PostbackController extends Controller
{
    private int $pointsValue;

    public function __construct()
    {
        $this->pointsValue = (int) Cache::get('points-value');

        if (! $this->pointsValue) {
            $this->pointsValue = 40;
        }
    }

    public function store(StorePostbackRequest $request): int
    {
        $payload = $request->validated();

        $data = [
            'type' => CompletedTask::TYPE_OFFER,
            'provider' => $payload['app'],
            'user_id' => $payload['user_id'],
            'points' => $payload['payout'] * $this->pointsValue,
            'data' => [
                'transaction_id' => $payload['transaction_id'],
                'offer_name' => $payload['offername'],
                'revenue' => $payload['payout'],
                'user_ip' => $payload['user_ip'],
            ],
        ];

        if ($payload['payout'] < 0 || isset($payload['status']) && (int) $payload['status'] === 2) {
            $data['type'] = CompletedTask::TYPE_CHARGEBACK;
            $data['points'] = -abs($data['points']);
        }

        CompletedTask::create($data);

        return 1;
    }

    public function lootably(StoreLootablyPostbackRequest $request): int
    {
        $payload = $request->validated();

        CompletedTask::create([
            'type' => CompletedTask::TYPE_OFFER,
            'provider' => 'lootably',
            'user_id' => $payload['user_id'],
            'points' => $payload['payout'] * $this->pointsValue,
            'data' => [
                'transaction_id' => $payload['transaction_id'],
                'offer_name' => $payload['offername'],
                'revenue' => $payload['payout'],
                'user_ip' => $payload['user_ip'],
            ],
        ]);

        return 1;
    }
}
