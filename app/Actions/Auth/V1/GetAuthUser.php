<?php

declare(strict_types=1);

namespace App\Actions\Auth\V1;

use App\Models\User;

final readonly class GetAuthUser
{
    /**
     * Get authenticated user with role.
     *
     * @return User
     */
    public function handle(): User
    {
        return auth()->guard('api')->user()->load('role');
    }
}
