<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;

final readonly class GetUserProfile
{
    public function handle():User
    {
        return auth()->guard('api')->user();
    }
}
