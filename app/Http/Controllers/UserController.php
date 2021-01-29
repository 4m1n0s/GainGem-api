<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\CompletedTask;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;

class UserController extends Controller
{
    public function index(IndexUserRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $users = User::when(isset($payload['username']), static fn ($query) => $query->search(['username'], $payload['username']))
            ->withAvailablePoints()
            ->with('referredBy:id,username')
            ->withCount(['referredUsers as referrals', 'transactions as withdraws'])
            ->orderByDesc('id')
            ->paginate(10);

        $usersArr = $users->append(['formatted_email_verified_at', 'formatted_available_points', 'formatted_total_points', 'formatted_banned_at']);
        $pagination = $users->toArray();
        unset($pagination['data']);

        return response()->json([
            'users' => $usersArr,
            'pagination' => $pagination,
        ]);
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        return response()->json(new UserResource($user->loadAvailablePoints()));
    }

    public function update(User $user, UpdateUserRequest $request): JsonResponse
    {
        $this->authorize('update', $user);

        $payload = $request->validated();
        $authenticatedUser = auth()->user();

        if ($authenticatedUser && $authenticatedUser->id === $user->id) {
            unset($payload['profile_image']);

            /** @var UploadedFile|null $profileImage */
            $profileImage = $request->file('profile_image');
            if ($profileImage) {
                $payload['profile_image'] = $profileImage->storeAs('profile-images', uniqid().'.png');
            }

            if ($user->email !== $payload['email']) {
                $payload['email_verified_at'] = null;
            }
        } else {
            if (! is_null($payload['points'])) {
                $user->completedTasks()->create([
                    'type' => CompletedTask::TYPE_ADMIN,
                    'points' => $payload['points'],
                ]);
            }

            if ($user->role !== $payload['role']) {
                $payload['banned_at'] = null;
                $payload['ban_reason'] = null;
            }
        }

        $user->update($payload);

        return response()->json([
            'user' => new UserResource($user->loadAvailablePoints()),
        ]);
    }
}
