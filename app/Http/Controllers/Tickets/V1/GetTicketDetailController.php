<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tickets\V1;

use App\Actions\Tickets\V1\GetTicketDetail;
use App\Http\Responses\JsonDataResponse;

final readonly class GetTicketDetailController
{
    public function __construct(
        private GetTicketDetail $getTicketDetail,
    ){}

    public function __invoke(string $id): JsonDataResponse
    {
        $ticketDetail = $this->getTicketDetail->handle($id);

        return new JsonDataResponse(
            data: [
                'id' => $ticketDetail->id,
                'date'=> $ticketDetail->created_at->toIso8601ZuluString(),
                'ticketCode' => $ticketDetail->ticket_code,
                'title' => $ticketDetail->title,
                'description' => $ticketDetail->description,
                'attachmentPath'=> $ticketDetail->attachment_path,
                'status' => $ticketDetail->status,
                'resolvedAt'=>  $ticketDetail->resolved_at ? $ticketDetail->resolved_at->toIso8601ZuluString() : null,
                'createdBy'=> [
                    'userId' => $ticketDetail->user?->id,
                    'name' => $ticketDetail->user?->name,
                    'email' => $ticketDetail->user?->email,
                    'role' => $ticketDetail->user?->role?->role,
                ],
                'replies'=> $ticketDetail->responses->map(function ($response) : array {
                    return [
                        'id'=> $response->id,
                        'message'=> $response->message,
                        'createdAt'=> $response->created_at->toDateTimeString(),
                        'createdBy'=> [
                            'userId' => $response->user?->id,
                            'name' => $response->user?->name,
                            'email' => $response->user?->email,
                            'role' => $response->user?->role?->role,
                        ],
                    ];
                })->toArray()
            ],
            message: 'Fetch ticket detail successfully',
        );
    }
}