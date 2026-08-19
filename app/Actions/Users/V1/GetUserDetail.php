<?php

declare(strict_types=1);

namespace App\Actions\Users\V1;

use App\Models\User;


final readonly class GetUserDetail
{
    public function handle(string $id):User
    {
        return User::findOrFail($id);
    }
}
