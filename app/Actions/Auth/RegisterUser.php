<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Http\Payloads\Auth\RegisterPayload;
use App\Http\Responses\JsonDataResponse;
use App\Models\Role;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

final readonly class RegisterUser
{
    /**
     * Register a new user and assign default USERS role.
     *
     * @return User|HttpException
     */
    public function handle(RegisterPayload $payload): User|HttpException
    {
        if (User::where('email', $payload->email)->exists()) {
            throw new HttpException(409, 'Email Already Exists');
        }

        $role = Role::firstOrCreate(['role' => 'USERS']);

        return User::create([
            'name' => $payload->name,
            'email' => $payload->email,
            'password' => $payload->password,
            'role_id' => $role->id,
            'is_active' => false, // default active
        ]);
    }
}
