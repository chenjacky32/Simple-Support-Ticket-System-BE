<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Ticket;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class TicketCodeHelper
{
    /**
     * Generate a new ticket code with format: 001/TICKET/MM/YYYY
     * Uses DB transaction and lockForUpdate to prevent race conditions.
     */
    public static function generate(): string
    {
        $now = Carbon::now();
        $currentMonth = $now->format('m');
        $currentYear = $now->format('Y');

        return DB::transaction(function () use ($currentMonth, $currentYear) {
            $lastTicket = Ticket::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->lockForUpdate()
                ->latest('created_at')
                ->first();
            
            $nextRegisterNumber = 1;
            
            if ($lastTicket && $lastTicket->ticket_code) {
                // Extract sequence from e.g. "001/TICKET/07/2026"
                $lastTicketCodeParts = explode('/', $lastTicket->ticket_code);
                $lastSequence = $lastTicketCodeParts[0] ?? '0';
                $nextRegisterNumber = (int) $lastSequence + 1;
            }
            
            // Add Padding '0' in front of numbers (001, 002, etc.)
            $sequence = str_pad((string) $nextRegisterNumber, 3, '0', STR_PAD_LEFT);

            // Create final ticket code
            return sprintf('%s/TICKET/%s/%s', $sequence, $currentMonth, $currentYear);
        });
    }
}
