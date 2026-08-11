<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\V1;

use App\Actions\Auth\AttemptLogin;
use App\Http\Requests\Auth\V1\LoginRequest;
use App\Http\Responses\JsonDataResponse;
use Illuminate\Http\JsonResponse;

final readonly class LoginController
{
    public function __construct(
        private AttemptLogin $attemptLogin,
    ) {}

    public function __invoke(LoginRequest $request): JsonResponse
    {
        $token = $this->attemptLogin->handle($request->payload());

        return new JsonDataResponse(
            data: [
                'accessToken' => $token,
            ],
            message: 'Login Successfull',
        );
    }
}
