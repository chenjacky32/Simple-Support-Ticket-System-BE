<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Http\Payloads\Users\ListUserPayload;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class GetUsersList
{
    /**
     * Get a paginated list of users using Eloquent.
     */
    public function handle(ListUserPayload $payload): LengthAwarePaginator
    {
        $query = User::query()->with('role');

        if ($payload->status !== null) {
            $isActive = match ($payload->status) {
                'ACTIVE' => true,
                'INACTIVE' => false,
                default => true,
            };
            $query->where('is_active', $isActive);
        }

        if ($payload->search !== null) {
            $search = $payload->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate(
            perPage: $payload->size,
            columns: ['*'],
            pageName: 'page',
            page: $payload->page
        );
    }
}
