<?php

declare(strict_types=1);

namespace App\Actions\Tickets\V1;

use App\Http\Payloads\Tickets\CreateTicketPayload;
use App\Models\User;
use App\Helpers\TicketCodeHelper;
use App\Models\Ticket;
use Symfony\Component\HttpKernel\Exception\HttpException;

final readonly class CreateTicket
{
    public function __construct(
        private TicketCodeHelper $ticketCodeHelper,
    ) {
    }

    public function handle(CreateTicketPayload $payload, User $authUser): Ticket
    {   
        // Checking user authenticated
        if (!$authUser) {
            throw new HttpException(401, 'Unauthorized');
        }

        // Generating ticket code
        $ticketCode = $this->ticketCodeHelper->generate();

        // Creating new ticket
        $createNewTicket = Ticket::create([
            'ticket_code' => $ticketCode,
            'title' => $payload->title,
            'description' => $payload->description,
            'attachment_path' => $payload->attachmentPath,
            'status' => Ticket::STATUS_OPENED,
            'user_id' => $authUser->id,
        ]);

        return $createNewTicket;
    }
}