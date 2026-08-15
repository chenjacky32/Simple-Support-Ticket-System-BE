<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tickets\V1;

use App\Actions\Tickets\V1\CreateTicket;
use App\Http\Requests\Tickets\V1\CreateTicketRequest;
use App\Http\Responses\JsonDataResponse;

final readonly class CreateTicketController
{
    public function __construct(
        private CreateTicket $createTicket,
    ){}

    public function __invoke(CreateTicketRequest $request): JsonDataResponse
    {
        $authUser = auth()->guard('api')->user()->load('role');
        $createNewTicket = $this->createTicket->handle($request->payload(), $authUser);

        return new JsonDataResponse(
            data: [
                'id' => $createNewTicket->id,
                'date' => $createNewTicket->created_at->toDateString(),
                'ticketCode' => $createNewTicket->ticket_code,
                'title' => $createNewTicket->title,
                'description' => $createNewTicket->description,
                'attachmentPath'=> $createNewTicket->attachmentPath,
                'status' => $createNewTicket->status,
                'resolvedAt'=>  $createNewTicket->resolvedAt ? $createNewTicket->resolvedAt->toDateString() : null,
                'createdBy'=> [
                    'userId' => $createNewTicket->user->id,
                    'name' => $createNewTicket->user->name,
                    'email' => $createNewTicket->user->email,
                    'role' => $createNewTicket->user->role?->role,
                ],
            ],
            message: 'New ticket created successfully',
        );
    }
}