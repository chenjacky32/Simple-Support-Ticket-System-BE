<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\V1;

use App\Actions\Auth\V1\RegisterUser;
use App\Http\Requests\Auth\V1\RegisterRequest;
use App\Http\Responses\JsonDataResponse;
use Illuminate\Http\JsonResponse;

final readonly class RegisterController
{
    public function __construct(
        private RegisterUser $registerUser,
    ) {
    }

    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $user = $this->registerUser->handle($request->payload());

        return new JsonDataResponse(
            data: [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role?->role ?? 'USERS',
                'isActive' => $user->is_active,
            ],
            message: 'Register Successfull',
        );
    }
}
