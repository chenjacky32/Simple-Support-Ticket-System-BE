<?php

declare(strict_types=1);

namespace App\Http\Payloads\Users;

final readonly class UpdateUserPayload 
{
    public function __construct(
        public string $name,
        public string $email,
        public string $role,
        public bool $isActive
    ) {   
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'isActive' => $this->isActive
        ];
    }
}