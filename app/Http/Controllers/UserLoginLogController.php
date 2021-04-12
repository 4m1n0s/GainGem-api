<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserLoginLogController extends Controller
{
    public function show(User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $loginLogs = $user->loginLog()->orderByDesc('id')->paginate(10);

        $pagination = $loginLogs->toArray();
        $loginLogsArr = $pagination['data'];
        unset($pagination['data']);

        return response()->json([
            'logins' => $loginLogsArr,
            'pagination' => $pagination,
        ]);
    }
}
