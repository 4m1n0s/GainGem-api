<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserLoginLogController extends Controller
{
    public function show(User $user): Collection
    {
        $this->authorize('update', $user);

        return $user->loginLog()->orderByDesc('id')->get();
    }
}
