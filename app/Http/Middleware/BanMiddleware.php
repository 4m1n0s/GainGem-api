<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BanMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->banned_at) {
            auth()->logout();
            abort(403, 'Your user is banned for the reason: '.$user->ban_reason);
        }

        return $next($request);
    }
}
