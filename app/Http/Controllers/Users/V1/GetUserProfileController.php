<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users\V1;

use App\Actions\Users\GetUserProfile;
use App\Http\Responses\JsonDataResponse;

final readonly class GetUserProfileController
{
    public function __construct(
        private GetUserProfile $getUserProfile
    ){
    }

    public function __invoke()
    {
        $userProfile = $this->getUserProfile->handle();

        return new JsonDataResponse(
            data:[
                'id' => $userProfile->id,
                'name' => $userProfile->name,
                'email' => $userProfile->email,
                'role' => $userProfile->role?->role,
                'isActive' => $userProfile->is_active,
            ],
            message: 'Fetch profile successfully',
        );
    }
}
