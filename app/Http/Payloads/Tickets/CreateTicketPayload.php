<?php

declare(strict_types=1);

namespace App\Http\Payloads\Tickets;

final readonly class CreateTicketPayload
{
    public function __construct(
        public string $title,
        public string $description,
        public ?string $attachmentPath,
    ) {
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'attachmentPath' => $this->attachmentPath,
        ];
    }
}