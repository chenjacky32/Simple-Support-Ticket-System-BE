<?php

declare(strict_types=1);

namespace App\Http\Payloads\Tickets;

final readonly class ListTicketPayload
{
    public function __construct(
        public int $page,
        public int $size,
        public ?string $status,
        public ?string $startDate,
        public ?string $endDate,
        public ?string $search,
    ) {
    }

    public function toArray(): array
    {
        return [
            'page' =>  $this->page,
            'size' =>  $this->size,
            'status' => $this->status,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'search' => $this->search,
        ];
    }
}