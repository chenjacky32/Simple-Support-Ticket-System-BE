<?php

declare(strict_types=1);

namespace App\Http\Requests\Tickets\V1;

use App\Http\Payloads\Tickets\UpdateTicketStatusPayload;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateTicketStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'=>['required','string','in:INPROGRESS,RESOLVED']
        ];
    }

    public function payload(): UpdateTicketStatusPayload
    {
        return new UpdateTicketStatusPayload(
            status: $this->input('status'),
        );
    }
}