<?php

declare(strict_types=1);

namespace App\Actions\Users\V1;

use App\Models\User;
use App\Http\Payloads\Users\UpdateUserStatusPayload;
use Symfony\Component\HttpKernel\Exception\HttpException;

final readonly class UpdateUserStatus
{
    /**
     * Summary of handle
     *
     * @param string $id
     * @param UpdateUserStatusPayload $payload
     * @return User|HttpException
     */
    public function handle(string $id, UpdateUserStatusPayload $payload):User|HttpException
    {
        $findUserData = User::findOrFail($id);

        if (!$findUserData) {
            throw new HttpException(404, 'User Not Found');
        }

        $findUserData->update([
            'is_active' => $payload->isActive,
        ]);

        return $findUserData;
    }
}
