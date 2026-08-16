<?php

declare(strict_types=1);

namespace App\Actions\Auth\V1;

use App\Http\Payloads\Auth\LoginPayload;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;

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

        if (auth()->guard('api')->user()->is_active !== User::STATUS_IS_ACTIVE) {
            auth()->guard('api')->logout();
            throw new AuthenticationException(
                'Your account is not active, please contact super admin for more information.',
            );
        }

        return (string) $token;
    }
}
