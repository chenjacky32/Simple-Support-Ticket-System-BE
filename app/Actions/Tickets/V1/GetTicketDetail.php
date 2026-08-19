<?php

declare(strict_types=1);

namespace App\Actions\Tickets\V1;

use App\Models\Ticket;

final readonly class GetTicketDetail
{
    public function handle(string $id): Ticket
    {
        return Ticket::with([
            'user',
            'responses.user',
        ])->findOrFail($id);
    }
}