<?php

declare(strict_types=1);

namespace App\Actions\Tickets\V1;

use App\Http\Payloads\Tickets\UpdateTicketStatusPayload;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

final readonly class UpdateTicketStatus
{
    public function handle(string $id, UpdateTicketStatusPayload $payload, User $authUser): Ticket | HttpException
    {
        // Checking user authenticated
        if (!$authUser) {
            throw new HttpException(401, 'Unauthorized');
        }

        // Checking ticket exists and update status
        return DB::transaction(function () use ($id, $payload) {
            $findTicketForUpdate = Ticket::query()
                ->where('id', $id)
                ->lockForUpdate()
                ->first();

            if (!$findTicketForUpdate) {
                throw new HttpException(404, 'Ticket not found');
            }
        
            if ($payload->status === Ticket::STATUS_RESOLVED) {
                $findTicketForUpdate->update([
                    'status' => $payload->status,
                    'resolved_at' => now(),
                ]);
            } else {
                $findTicketForUpdate->update([
                    'status' => $payload->status,
                    'resolved_at' => null,
                ]);
            }

            $findTicketForUpdate->refresh();
            
            return $findTicketForUpdate;
        });
    }
}