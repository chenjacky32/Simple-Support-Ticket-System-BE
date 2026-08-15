<?php

declare(strict_types=1);

namespace App\Http\Requests\Tickets\V1;

use App\Http\Payloads\Tickets\ReplyTicketPayload;
use Illuminate\Foundation\Http\FormRequest;

final class ReplyTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => ['required','string','min:2','max:255']
        ];
    }

    public function payload(): ReplyTicketPayload
    {
        return new ReplyTicketPayload(
            message: $this->input('message'),
        );
    }
}