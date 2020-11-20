<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserDataMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = auth()->user();

        if ($user && $user->isUserRole()) {
            $this->updateData($request, $user);
        }

        return $response;
    }

    private function updateData(Request $request, User $user): void
    {
        $data = [];

        $ip = Helper::instance()->getIp();

        if ($user->ip !== $ip) {
            $data['ip'] = $ip;
        }

        $user->update($data);
    }
}
