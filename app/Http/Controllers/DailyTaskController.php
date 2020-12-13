<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyTasksStoreRequest;
use App\Http\Resources\UserResource;
use App\Models\CompletedTask;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DailyTaskController extends Controller
{
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        return response()->json([
            'completed_offers_count' => $user->completedTasks()->todayOffers()->count(),
            'completed_daily_tasks' => $user->completedTasks()->todayDailyTasks()->get()->pluck('offers_count'),
            'daily_tasks_options' => CompletedTask::DAILY_TASK_OFFERS_OPTIONS,
        ]);
    }

    public function store(DailyTasksStoreRequest $request): JsonResponse
    {
        $payload = $request->validated();

        /** @var User $user */
        $user = auth()->user();

        $todayCompletedOffersCount = $user->completedTasks()->todayOffers()->count();
        $hasAlreadyStored = $user->completedTasks()->todayDailyTasks()->where('data->offers_count', $payload['offers_count'])->exists();

        abort_if($hasAlreadyStored, 422, "You've already redeemed this task");
        abort_if($todayCompletedOffersCount < $payload['offers_count'], 422, 'You need to complete '.($payload['offers_count'] - $todayCompletedOffersCount).' more offers!');

        $user->completedTasks()->create([
            'type' => CompletedTask::TYPE_DAILY_TASK,
            'points' => CompletedTask::DAILY_TASK_OFFERS_OPTIONS[$payload['offers_count']],
            'data' => [
                'offers_count' => $payload['offers_count'],
            ],
        ]);

        return response()->json([
            'user' => new UserResource($user->loadAvailablePoints()),
        ], 201);
    }
}
