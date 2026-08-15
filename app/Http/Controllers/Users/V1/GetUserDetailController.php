<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users\V1;

use App\Actions\Users\V1\GetUserDetail;
use App\Http\Responses\JsonDataResponse;

final readonly class GetUserDetailController
{
    public function __construct(
        private GetUserDetail $getUserDetail
    )
    {
    }

    public function __invoke(string $id): JsonDataResponse
    {
        $userDetails = $this->getUserDetail->handle($id);

        return new JsonDataResponse(
            data:[
                'id' => $userDetails->id,
                'name' => $userDetails->name,
                'email' => $userDetails->email,
                'role' => $userDetails->role?->role,
                'isActive' => $userDetails->is_active,
                'createdAt'=> $userDetails->created_at->toDateTimeString(),
                'updatedAt'=> $userDetails->updated_at->toDateTimeString()
            ],
            message: 'Fetch user details successfully',
        );
    }
}
