<?php

declare(strict_types=1);

namespace App\Http\Payloads\Users;

final readonly class ListUserPayload
{
    public function __construct(
        public int $page,
        public int $size,
        public ?string $status,
        public ?string $search,
    ) {
    }

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'size' => $this->size,
            'status' => $this->status,
            'search' => $this->search,
        ];
    }
}
