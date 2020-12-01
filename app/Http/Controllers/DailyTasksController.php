<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyTasksStoreRequest;
use App\Http\Resources\UserResource;
use App\Models\CompletedTask;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DailyTasksController extends Controller
{
    private Carbon $now;
    private Carbon $end;
    private int $completedOffersCount;
    private ?User $user;

    public function __construct()
    {
        $this->now = now()->startOfDay();
        $this->end = now()->endOfDay();

        /* @var User $user */
        $this->user = auth()->user();

        $this->completedOffersCount = $this->user->completedTasks()
            ->where('type', CompletedTask::TYPE_OFFER)
            ->whereBetween('created_at', [$this->now, $this->end])
            ->count();
    }

    public function index(): JsonResponse
    {
        $completedTasks = $this->user->completedTasks()
            ->where('type', CompletedTask::TYPE_TASK)
            ->whereBetween('created_at', [$this->now, $this->end])
            ->select('data->offers_count as offers_count')
            ->get();

        for ($i = 0; $i < $completedTasks->count(); $i++) {
            $completedTasks[$i] = $completedTasks[$i]->offers_count;
        }

        return response()->json([
            'completed_offers_count' => $this->completedOffersCount,
            'completed_tasks' => $completedTasks,
        ]);
    }

    public function store(DailyTasksStoreRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $hasAlreadyRedeemed = $this->user->completedTasks()
            ->where('type', CompletedTask::TYPE_TASK)
            ->whereBetween('created_at', [$this->now, $this->end])
            ->where('data->offers_count', $payload['offers_count'])
            ->exists();

        abort_if($hasAlreadyRedeemed, 422, "You've already redeemed this task");
        abort_if($this->completedOffersCount < $payload['offers_count'], 422, 'You need to complete '.($payload['offers_count'] - $this->completedOffersCount).' more offers!');

        $this->user->completedTasks()->create([
            'type' => CompletedTask::TYPE_TASK,
            'points' => CompletedTask::TASKS[$payload['offers_count']],
            'data' => [
                'offers_count' => $payload['offers_count'],
            ],
        ]);

        return response()->json([
            'user' => new UserResource($this->user->withAvailablePoints()),
        ], 201);
    }
}
