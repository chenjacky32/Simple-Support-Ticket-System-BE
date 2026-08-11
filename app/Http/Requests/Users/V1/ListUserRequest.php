<?php

declare(strict_types=1);

namespace App\Http\Requests\Users\V1;

use App\Http\Payloads\Users\ListUserPayload;
use Illuminate\Foundation\Http\FormRequest;

final class ListUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'size' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'string', 'in:ACTIVE,INACTIVE'],
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function payload(): ListUserPayload
    {
        return new ListUserPayload(
            page: (int) $this->query('page', 1),
            size: (int) $this->query('size', 10),
            status: $this->query('status') !== null ? (string) $this->query('status') : null,
            search: $this->query('search') !== null ? (string) $this->query('search') : null,
        );
    }
}
