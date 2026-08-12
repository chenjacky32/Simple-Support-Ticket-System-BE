<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\Response;

class AllowOnlyRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->guard('api')->user();

        // Checking if user role is allowed to access the resource
        if (! in_array($user->role->role, $roles, true)) {
            throw new AuthorizationException('You don\'t have permission to access this resource.');
        }

        return $next($request);
    }
}
