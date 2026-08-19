<?php

declare(strict_types=1);

namespace App\Http\Payloads\Dashboard;

final readonly class GetDashboardStatPayload
{
    public function __construct(
        public ?string $startDate,
        public ?string $endDate,
    ) {
    }

    public function toArray(): array
    {
        return [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];
    }
}