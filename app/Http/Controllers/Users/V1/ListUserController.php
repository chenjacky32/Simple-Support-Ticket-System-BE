<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users\V1;

use App\Actions\Users\V1\GetUsersList;
use App\Http\Requests\Users\V1\ListUserRequest;
use App\Http\Responses\JsonDataResponse;
use Illuminate\Http\JsonResponse;

final readonly class ListUserController
{
    public function __construct(
        private GetUsersList $getUsersList,
    ) {
    }

    public function __invoke(ListUserRequest $request): JsonResponse
    {
        $paginator = $this->getUsersList->handle($request->payload());

        $data = collect($paginator->items())->map(fn ($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role?->role,
            'isActive' => $user->is_active,
        ])->toArray();

        $meta = [
            'page' => $paginator->currentPage(),
            'size' => $paginator->perPage(),
            'totalRecord' => $paginator->total(),
            'totalPage' => $paginator->lastPage(),
            'hasPrev' => $paginator->currentPage() > 1,
            'hasNext' => $paginator->hasMorePages(),
        ];

        return new JsonDataResponse(
            data: $data,
            message: 'ok',
            meta: $meta,
        );
    }
}
