<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\CompletedTask;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(IndexUserRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $users = User::when(isset($payload['username']), static fn ($query) => $query->search(['username'], $payload['username']))
            ->when(isset($payload['filter']), static function ($query) use ($payload) {
                $query->orderBy(DB::raw("ISNULL({$payload['filter']})"))
                    ->orderBy($payload['filter'], isset($payload['filter_direction']) ? $payload['filter_direction'] : 'ASC');
            })->withAvailablePoints()
            ->with('referredBy:id,username')
            ->withCount(['referredUsers as referrals', 'transactions as withdraws'])
            ->orderByDesc('id')
            ->paginate(10);

        $usersArr = $users->append(['formatted_email_verified_at', 'formatted_available_points', 'formatted_total_points', 'formatted_banned_at', 'formatted_froze_at']);
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
                $payload['two_factor_enabled_at'] = null;
                $payload['two_factor_code'] = null;
                $payload['two_factor_expires_at'] = null;
            }
        } else {
            if (! is_null($payload['points'])) {
                $user->completedTasks()->create([
                    'type' => CompletedTask::TYPE_ADMIN,
                    'points' => $payload['points'],
                ]);
            }

            if (isset($payload['is_frozen']) && (bool) $user->froze_at !== $payload['is_frozen']) {
                $payload['froze_at'] = $payload['is_frozen'] ? now() : null;
            }

            if ($user->role !== $payload['role']) {
                $payload['banned_at'] = null;
                $payload['ban_reason'] = null;
                $payload['froze_at'] = null;
            }
        }

        $user->update($payload);

        return response()->json([
            'user' => new UserResource($user->loadAvailablePoints()),
        ]);
    }
}
