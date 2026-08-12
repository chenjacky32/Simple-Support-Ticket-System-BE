<?php

namespace App\Http\Controllers\Users\V1;

use App\Actions\Users\UpdateUserStatus;
use App\Http\Requests\Users\V1\UpdateUserStatusRequest;
use App\Http\Responses\JsonDataResponse;

final readonly class UpdateUserStatusController
{
    public function __construct(
        private UpdateUserStatus $updateUserStatus,    
    ){
    }

    public function __invoke(UpdateUserStatusRequest $request, string $id): JsonDataResponse
    {
        $updateUserStatus = $this->updateUserStatus->handle(
            $id,
            $request->payload(),
        );

        return new JsonDataResponse(
            data: [ 
                'id'=>$updateUserStatus->id,
                'name'=> $updateUserStatus->name,
                'email'=> $updateUserStatus->email,
                'role'=> $updateUserStatus->role?->role,
                'isActive'=> $updateUserStatus->is_active,
            ],
            message: 'user status updated successfully',
        );
    }
}
