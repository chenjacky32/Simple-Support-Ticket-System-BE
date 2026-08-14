<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Http\Payloads\Users\UpdateUserPayload;
use App\Models\Role;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

final readonly class UpdateUser
{
    /**
     * Summary of handle
     *
     * @param string $id
     * @param UpdateUserPayload $payload
     * @return User|HttpException
     */
    public function handle(string $id, UpdateUserPayload $payload):User|HttpException
    {
        // Finding user by ID
        $findUserData = User::findOrFail($id);

        if (!$findUserData) {
            throw new HttpException(404, 'User Not Found');
        }

        // Checking if email already exists except the current user
        if (User::where('email', $payload->email)->where('id', '!=', $id)->exists()) {
            throw new HttpException(409, 'Email Already Exists');
        }

        // checking role to database
        $checkRole = Role::where('role', $payload->role)->first();
        
        if (!$checkRole) {
            throw new HttpException(404, "Role \"$payload->role\" Not Exists");
        }

        // Updating user data
        $findUserData->update([
            'name' => $payload->name,
            'email' => $payload->email,
            'role_id' => $checkRole->id,
            'is_active' => $payload->isActive,
        ]);

        return $findUserData;
    }
}
