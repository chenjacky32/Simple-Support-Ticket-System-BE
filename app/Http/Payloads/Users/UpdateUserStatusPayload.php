<?php

declare(strict_types=1);

namespace App\Http\Payloads\Users;

final readonly class UpdateUserStatusPayload 
{
    public function __construct(
        public bool $isActive
    ){
    }

    public function toArray(): array
    {
        return [
            'isActive' => $this->isActive
        ];
    }
}