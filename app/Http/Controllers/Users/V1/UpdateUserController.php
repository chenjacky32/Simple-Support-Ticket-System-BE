<?php

// declare(strict_types=1);

namespace App\Http\Controllers\Users\V1;

use App\Actions\Users\UpdateUser;
use App\Http\Requests\Users\V1\UpdateUserRequest;
use App\Http\Responses\JsonDataResponse;

final readonly class UpdateUserController
{
    public function __construct(
        private UpdateUser $updateUser,
    ){
    }

    public function __invoke(UpdateUserRequest $request, string $id): JsonDataResponse
    {
        $updateUser = $this->updateUser->handle(
            $id,
            $request->payload(),
        );

        return new JsonDataResponse(
            data: [
                'id' => $updateUser->id,
                'name' => $updateUser->name,
                'email' => $updateUser->email,
                'role' => $updateUser->role?->role,
                'isActive' => $updateUser->is_active,
            ],
            message: 'users detail updated successfully',
        );
    }
}
