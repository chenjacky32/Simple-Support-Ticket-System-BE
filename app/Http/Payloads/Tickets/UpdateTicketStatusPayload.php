<?php

declare(strict_types=1);

namespace App\Http\Payloads\Tickets;

final readonly class UpdateTicketStatusPayload
{
    public function __construct(
        public string $status,
    ) {
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
        ];
    }
}