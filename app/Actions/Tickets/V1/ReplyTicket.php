<?php

declare(strict_types=1);

namespace App\Actions\Tickets\V1;

use App\Http\Payloads\Tickets\ReplyTicketPayload;
use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

final readonly class ReplyTicket
{
    public function handle(string $id, ReplyTicketPayload $payload, User $authUser): TicketResponse | HttpException
    {
        // Checking user authenticated
        if (!$authUser) {
            throw new HttpException(401, 'Unauthorized');
        }
        
        // Checking ticket exists
        $findTicket = Ticket::query()
            ->where('id', $id)
            ->first();

            if (!$findTicket) {
                throw new HttpException(404, 'Ticket not found');
            }

            $createNewReply = TicketResponse::create([
                'ticket_id' => $findTicket->id,
                'message' => $payload->message,
                'responded_by' => $authUser->id,
            ]);

            return $createNewReply;
    }
}