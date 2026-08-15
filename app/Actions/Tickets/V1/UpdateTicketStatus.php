<?php

declare(strict_types=1);

namespace App\Actions\Tickets\V1;

use App\Http\Payloads\Tickets\UpdateTicketStatusPayload;
use App\Models\Ticket;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

final readonly class UpdateTicketStatus
{
    public function handle(string $id, UpdateTicketStatusPayload $payload, User $authUser): Ticket | HttpException
    {
        // Checking user authenticated
        if (!$authUser) {
            throw new HttpException(401, 'Unauthorized');
        }

        // Checking ticket exists
        $findTicketForUpdate = Ticket::query()
            ->where('id', $id)
            ->firstOrFail();

            if (!$findTicketForUpdate) {
                throw new HttpException(404, 'Ticket not found');
            }
        
            $findTicketForUpdate->update([
                'status' => $payload->status,
            ]);

            $findTicketForUpdate->refresh();
            
            return $findTicketForUpdate;
    }
}