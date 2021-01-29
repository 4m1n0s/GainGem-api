<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;

    public function mail(User $authenticatedUser, Transaction $transaction): bool
    {
        if ($authenticatedUser->isSuperAdminRole()) {
            return true;
        }

        if ($authenticatedUser->id === $transaction->user_id) {
            return true;
        }

        return false;
    }
}
