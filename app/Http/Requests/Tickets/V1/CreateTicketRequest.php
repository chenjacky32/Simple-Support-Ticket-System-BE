<?php

declare(strict_types=1);

namespace App\Http\Requests\Tickets\V1;

use App\Http\Payloads\Tickets\CreateTicketPayload;
use Illuminate\Foundation\Http\FormRequest;

final class CreateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'=>['required','string','min:5','max:100'],
            'description'=>['required','string','min:5','max:1000'],
            'attachmentPath'=>['nullable','string','max:255']
        ];
    }

    public function payload(): CreateTicketPayload
    {
        return new CreateTicketPayload(
            title: (string) $this->string('title'),
            description: (string) $this->string('description'),
            attachmentPath: (string) $this->string('attachmentPath'),
        );
    }
}