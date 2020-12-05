<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function update(User $authUser, User $user): bool
    {
        if ($authUser->isAdminRole()) {
            return true;
        }

        return $authUser->id === $user->id;
    }
}
