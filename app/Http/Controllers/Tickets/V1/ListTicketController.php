<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tickets\V1;

use App\Actions\Tickets\V1\GetTicketList;
use App\Http\Requests\Tickets\V1\ListTicketRequest;
use App\Http\Responses\JsonDataResponse;

final readonly class ListTicketController
{
    public function __construct(
        private GetTicketList $getTicketList,
    ){}

    public function __invoke(ListTicketRequest $request): JsonDataResponse
    {
        $paginator = $this->getTicketList->handle($request->payload());

        $data = collect($paginator->items())->map(fn($ticket) => [
            'id' => $ticket->id,
            'date' => $ticket->created_at->toIso8601ZuluString(),
            'ticketCode' => $ticket->ticket_code,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'attachmentPath' => $ticket->attachment_path,
            'status' => $ticket->status,
            'resolvedAt' => $ticket->resolved_at ? $ticket->resolved_at->toIso8601ZuluString() : null,
            'createdBy' => [
                'userId' => $ticket->user->id,
                'name' => $ticket->user->name,
                'email' => $ticket->user->email,
                'role' => $ticket->user->role?->role,
            ],
        ])->toArray();

        $meta = [
            'page' => $paginator->currentPage(),
            'size' => $paginator->perPage(),
            'totalRecord' => $paginator->total(),
            'totalPage' => $paginator->lastPage(),
            'hasPrev' => $paginator->currentPage() > 1,
            'hasNext' => $paginator->hasMorePages(),
        ];

        return new JsonDataResponse(
            data: $data,
            message: 'Fetch ticket list successfully',
            meta: $meta,
        );
    }
}