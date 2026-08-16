<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tickets\V1;

use App\Actions\Auth\V1\GetAuthUser;
use App\Actions\Tickets\V1\CreateTicket;
use App\Http\Requests\Tickets\V1\CreateTicketRequest;
use App\Http\Responses\JsonDataResponse;

final readonly class CreateTicketController
{
    public function __construct(
        private CreateTicket $createTicket,
        private GetAuthUser $getAuthUser
    ){}

    public function __invoke(CreateTicketRequest $request): JsonDataResponse
    {
        $authUser = $this->getAuthUser->handle();
        $createNewTicket = $this->createTicket->handle($request->payload(), $authUser);

        return new JsonDataResponse(
            data: [
                'id' => $createNewTicket->id,
                'date' => $createNewTicket->created_at->toIso8601ZuluString(),
                'ticketCode' => $createNewTicket->ticket_code,
                'title' => $createNewTicket->title,
                'description' => $createNewTicket->description,
                'attachmentPath'=> $createNewTicket->attachmentPath,
                'status' => $createNewTicket->status,
                'resolvedAt'=>  $createNewTicket->resolvedAt ? $createNewTicket->resolvedAt->toIso8601ZuluString() : null,
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