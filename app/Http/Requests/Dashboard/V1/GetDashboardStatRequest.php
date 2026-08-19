<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard\V1;

use App\Http\Payloads\Dashboard\GetDashboardStatPayload;
use Illuminate\Foundation\Http\FormRequest;

final class GetDashboardStatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'startDate' => ['nullable', 'date', 'max:255'],
            'endDate' => ['nullable', 'date', 'max:255'],
        ];
    }

    public function payload(): GetDashboardStatPayload
    {
        return new GetDashboardStatPayload(
            startDate: $this->query('startDate') !== null ? (string) $this->query('startDate') : null,
            endDate: $this->query('endDate') !== null ? (string) $this->query('endDate') : null,
        );
    }
}