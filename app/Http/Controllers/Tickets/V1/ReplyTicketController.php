<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tickets\V1;

use App\Actions\Tickets\V1\ReplyTicket;
use App\Http\Requests\Tickets\V1\ReplyTicketRequest;
use App\Http\Responses\JsonDataResponse;

final readonly class ReplyTicketController
{
    public function __construct(
        private ReplyTicket $replyTicket
    ){}

    public function __invoke(string $id, ReplyTicketRequest $request): JsonDataResponse
    {
        $authUser = auth()->guard('api')->user()->load('role');
        $replyTicket = $this->replyTicket->handle($id, $request->payload(), $authUser);
        
        return new JsonDataResponse(
            data: [
                'id' => $replyTicket->id,
                'ticketId' => $replyTicket->ticket_id,
                'message' => $replyTicket->message,
                'createdAt' => $replyTicket->created_at->toDateString(),
                'createdBy' => [
                    'userId' => $replyTicket->user->id,
                    'name' => $replyTicket->user->name,
                    'email' => $replyTicket->user->email,
                    'role' => $replyTicket->user->role?->role,
                ],
            ],
            message: 'Reply added successfully',
        );
    }
}