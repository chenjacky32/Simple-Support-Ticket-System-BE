<?php

declare(strict_types=1);

namespace App\Http\Requests\Users\V1;

use App\Http\Payloads\Users\UpdateUserStatusPayload;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateUserStatusRequest extends FormRequest
{
    public function authorize():bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'isActive'=>['required', 'boolean'],
        ];
    }

    public function payload(): UpdateUserStatusPayload
    {
        return new UpdateUserStatusPayload(
            isActive: (bool) $this->boolean('isActive')
        );
    }
}