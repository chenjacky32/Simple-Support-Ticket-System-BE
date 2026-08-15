<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tickets\V1;

use App\Actions\Tickets\V1\UpdateTicketStatus;
use App\Http\Requests\Tickets\V1\UpdateTicketStatusRequest;
use App\Http\Responses\JsonDataResponse;

final readonly class UpdateTicketStatusController
{
    public function __construct(
        private UpdateTicketStatus $updateTicketStatus
    ){}

    public function __invoke(string $id, UpdateTicketStatusRequest $request): JsonDataResponse
    {
        $authUser = auth()->guard('api')->user()->load('role');
        $updateTicketStatus = $this->updateTicketStatus->handle($id, $request->payload(), $authUser);
        
        return new JsonDataResponse(
            data: [
                'id' => $updateTicketStatus->id,
                'status' => $updateTicketStatus->status,
                'updatedAt' => $updateTicketStatus->updated_at->toDateString(),
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