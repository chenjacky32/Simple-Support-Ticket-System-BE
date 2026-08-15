<?php

declare(strict_types=1);

namespace App\Actions\Dashboard\V1;

use App\Models\Ticket;
use App\Http\Payloads\Dashboard\GetDashboardStatPayload;

final readonly class GetDashboardStat
{
    public function handle(GetDashboardStatPayload $payload): array
    {
        $query = Ticket::query();

        // Filter by start date and end date if provided from request payload
        if ($payload->startDate && $payload->endDate) {
            $query->whereBetween('created_at', [
                $payload->startDate . ' 00:00:00', 
                $payload->endDate . ' 23:59:59'
            ]);
        }

        // Get Total based on Status by using aggregation
        $statusCount = $query->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $OpenedCount = $statusCount[Ticket::STATUS_OPENED] ?? 0;
        $InProgressCount = $statusCount[Ticket::STATUS_INPROGRESS] ?? 0;
        $ResolvedCount = $statusCount[Ticket::STATUS_RESOLVED] ?? 0;

        // Count All Totals
        $totalTickets = $OpenedCount + $InProgressCount + $ResolvedCount;

        // Count percentage and create array for statusComposition
        $compositions = [];
        $statusMap = [
            Ticket::STATUS_OPENED => 'OPENED',
            Ticket::STATUS_INPROGRESS => 'INPROGRESS',
            Ticket::STATUS_RESOLVED => 'RESOLVED'
        ];

        foreach ($statusMap as $statusCode => $label) {
            $count = $statusCount[$statusCode] ?? 0;
            $percentage = $totalTickets > 0 ? round(($count / $totalTickets) * 100, 2) : 0;

            $compositions[] = [
                'status' => $statusCode,
                'percentage' => $percentage,
                'count' => $count
            ];
        }

        // Count average response time if 

        return [
            'startDate' => $payload->startDate,
            'endDate' => $payload->endDate,
            'totalTickets' => $totalTickets,
            'openedTickets' => $OpenedCount,
            'inprogressTickets' => $InProgressCount,
            'resolvedTickets' => $ResolvedCount,
            'statusCompositions' => $compositions
        ];
    }
}