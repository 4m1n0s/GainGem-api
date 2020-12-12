<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function update(User $authenticatedUser, User $user): bool
    {
        if ($authenticatedUser->isSuperAdminRole()) {
            return true;
        }

        if ($authenticatedUser->id === $user->id) {
            return true;
        }

        if ($authenticatedUser->isAdminRole() && $user->isAdminRole()) {
            return false;
        }

        if ($authenticatedUser->isAdminRole()) {
            return true;
        }

        return true;
    }
}
