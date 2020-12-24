<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLootablyPostbackRequest;
use App\Models\CompletedTask;
use Illuminate\Http\JsonResponse;
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

    public function store(): JsonResponse
    {
        return response()->json(['hello']);

        $payload = $request->validated();

        CompletedTask::create([
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
        ]);
    }

    public function lootably(StoreLootablyPostbackRequest $request): void
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
    }
}
