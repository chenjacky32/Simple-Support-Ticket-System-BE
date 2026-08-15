<?php

declare(strict_types=1);

namespace App\Actions\Auth\V1;

use App\Http\Payloads\Auth\LoginPayload;
use Illuminate\Auth\AuthenticationException;

final readonly class AttemptLogin
{
    /**
     * Attempt authentication and return JWT token.
     *
     * @throws AuthenticationException
     */
    public function handle(LoginPayload $payload): string
    {
        $token = auth()->guard('api')->attempt([
            'email' => $payload->email,
            'password' => $payload->password,
        ]);

        if (! $token) {
            throw new AuthenticationException('Invalid credentials');
        }

        return (string) $token;
    }
}
