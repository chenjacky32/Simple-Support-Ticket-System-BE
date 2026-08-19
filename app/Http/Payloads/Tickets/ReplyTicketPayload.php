<?php

declare(strict_types=1);

namespace App\Http\Payloads\Tickets;

final readonly class ReplyTicketPayload
{
    public function __construct(
        public string $message,
    ) {
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}