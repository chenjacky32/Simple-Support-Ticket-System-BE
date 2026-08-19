<?php

declare(strict_types=1);

namespace App\Http\Requests\Tickets\V1;

use App\Http\Payloads\Tickets\ListTicketPayload;
use Illuminate\Foundation\Http\FormRequest;

final class ListTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'size' => ['nullable', 'integer', 'min:1', 'max:100'],
            'status' => ['nullable', 'string', 'in:OPENED,INPROGRESS,RESOLVED'],
            'startDate' => ['nullable', 'date_format:Y-m-d'],
            'endDate' => ['nullable', 'date_format:Y-m-d'],
            'search' => ['nullable', 'string', 'min:1', 'max:255'],
        ];
    }

    public function payload(): ListTicketPayload
    {
        return new ListTicketPayload(
            page: (int) $this->query('page'),
            size: (int) $this->query('size'),
            status: $this->query('status'),
            startDate: $this->query('startDate'),
            endDate: $this->query('endDate'),
            search: $this->query('search'),
        );
    }
}