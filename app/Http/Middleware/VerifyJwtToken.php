<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\AuthenticationException;

class VerifyJwtToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->guard('api')->check()) {
            throw new AuthenticationException('Invalid credentials');
        }

        return $next($request);
    }
}
