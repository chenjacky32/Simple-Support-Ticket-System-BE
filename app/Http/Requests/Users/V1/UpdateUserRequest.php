<?php

declare(strict_types=1);

namespace App\Http\Requests\Users\V1;

use App\Http\Payloads\Users\UpdateUserPayload;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class UpdateUserRequest extends FormRequest
{
    public function authorize():bool
    {
        return true;
    }

    public function rules(): array
    {
        return[
            'name'=>['required','string','max:255'],
            'email'=>['required','email','max:255'],
            'role'=> ['required', 'string', 'in:USERS,ADMIN,SUPERADMIN'],
            'isActive'=>['required','boolean'],
        ];
    }

    public function payload(): UpdateUserPayload
    {
        return new UpdateUserPayload(

            name: $this->string('name')->toString(),
            email: $this->string('email')->toString(),
            role: $this->string('role')->toString(),
            isActive: $this->boolean('isActive'),
        );
    }
}