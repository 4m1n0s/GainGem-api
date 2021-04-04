<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSocialMediaTaskRequest;
use App\Http\Resources\UserResource;
use App\Models\CompletedTask;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SocialMediaTaskController extends Controller
{
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $options = CompletedTask::SOCIAL_MEDIA_TASK_OFFERS_OPTIONS;

        $user->completedTasks()
            ->where('type', CompletedTask::TYPE_SOCIAL_MEDIA)
            ->whereIn('data->type', array_keys(CompletedTask::SOCIAL_MEDIA_TASK_OFFERS_OPTIONS))
            ->get(['user_id', 'data'])
            ->each(static function (CompletedTask $completedTask) use (&$options) {
                if (isset($completedTask->data['type'])) {
                    unset($options[$completedTask->data['type']]);
                }
            });

        return response()->json([
            'social_media_tasks_options' => $options,
        ]);
    }

    public function store(StoreSocialMediaTaskRequest $request): JsonResponse
    {
        $payload = $request->validated();

        /** @var User $user */
        $user = auth()->user();

        $lock = Cache::lock("{$payload['social_media']}-task.{$user->id}", 10);

        abort_if(! $lock->get(), 422, "You're already in the process of redeeming!");

        $hasAlreadyStored = $user->completedTasks()
            ->where('type', CompletedTask::TYPE_SOCIAL_MEDIA)
            ->where('data->type', $payload['social_media'])
            ->exists();

        abort_if($hasAlreadyStored, 422, "You've already redeemed this social media!");

        $user->completedTasks()->create([
            'type' => CompletedTask::TYPE_SOCIAL_MEDIA,
            'points' => CompletedTask::SOCIAL_MEDIA_TASK_OFFERS_OPTIONS[$payload['social_media']],
            'data' => [
                'type' => $payload['social_media'],
            ],
        ]);

        return response()->json([
            'user' => new UserResource($user->loadAvailablePoints()),
        ], 201);
    }
}
