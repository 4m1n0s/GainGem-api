<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        /** @var User|null $user */
        $user = auth()->user();

        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        if (! collect($roles)->contains($user->role)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
