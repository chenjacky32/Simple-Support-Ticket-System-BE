<?php

declare(strict_types=1);

namespace App\Actions\Tickets\V1;

use App\Http\Payloads\Tickets\ListTicketPayload;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class GetTicketList
{
    public function handle(ListTicketPayload $payload): LengthAwarePaginator
    {
        // Implement action logic
        $query = Ticket::query()
                    ->with('user')
                    ->orderBy('created_at', 'DESC');

        if ($payload->status !== null) {
            $ticketStatus = match ($payload->status) {
                "OPENED" => Ticket::STATUS_OPENED,
                "INPROGRESS" => Ticket::STATUS_INPROGRESS,
                "RESOLVED" => Ticket::STATUS_RESOLVED,
                default => null,
            };
            $query->where('status', $ticketStatus);
        }

        if ($payload->startDate !== null && $payload->endDate !== null) {
            $query->whereBetween('created_at', [
                Carbon::parse($payload->startDate)->format('Y-m-d'),
                Carbon::parse($payload->endDate)->format('Y-m-d'),
            ]);
        }

        if ($payload->search !== null) {
            $search = $payload->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->paginate(
            perPage: $payload->size,
            columns: ['*'],
            pageName: 'page',
            page: $payload->page
        );
    }
}