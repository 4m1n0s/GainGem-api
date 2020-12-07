<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;

class UserController extends Controller
{
    public function index(IndexUserRequest $request): Collection
    {
        $payload = $request->validated();

        return User::when(isset($payload['username']), static fn ($query) => $query->search($payload['username']))
            ->withAvailablePoints()
            ->get();
    }

    public function update(User $user, UpdateUserRequest $request): JsonResponse
    {
        $this->authorize('update', $user);

        $payload = $request->validated();
        $payload['profile_image'] = null;

        /** @var UploadedFile|null $profileImage */
        $profileImage = $request->file('profile_image');
        if ($profileImage) {
            $payload['profile_image'] = $profileImage->storeAs('profile-images', uniqid().'.png');
        }

        if ($user->email !== $payload['email']) {
            $payload['email_verified_at'] = null;
        }

        $user->update($payload);

        return response()->json([
            'user' => new UserResource($user->withAvailablePoints()),
        ]);
    }
}
