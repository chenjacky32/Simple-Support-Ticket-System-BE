<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tickets\V1;

use App\Actions\Auth\V1\GetAuthUser;
use App\Actions\Tickets\V1\UpdateTicketStatus;
use App\Http\Requests\Tickets\V1\UpdateTicketStatusRequest;
use App\Http\Responses\JsonDataResponse;

final readonly class UpdateTicketStatusController
{
    public function __construct(
        private UpdateTicketStatus $updateTicketStatus,
        private GetAuthUser $getAuthUser
    ){}

    public function __invoke(string $id, UpdateTicketStatusRequest $request): JsonDataResponse
    {
        $authUser = $this->getAuthUser->handle();
        $updateTicketStatus = $this->updateTicketStatus->handle($id, $request->payload(), $authUser);
        
        return new JsonDataResponse(
            data: [
                'id' => $updateTicketStatus->id,
                'status' => $updateTicketStatus->status,
                'updatedAt' => $updateTicketStatus->updated_at->toIso8601ZuluString(),
                'updatedBy' => [
                    'userId' => $updateTicketStatus->user->id,
                    'name' => $updateTicketStatus->user->name,
                    'email' => $updateTicketStatus->user->email,
                    'role' => $updateTicketStatus->user->role?->role,
                ],
            ],
            message: 'Ticket status updated successfully',
        );
    }
}