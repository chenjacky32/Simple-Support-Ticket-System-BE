<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard\V1;

use App\Actions\Dashboard\V1\GetDashboardStat;
use App\Http\Requests\Dashboard\V1\GetDashboardStatRequest;
use App\Http\Responses\JsonDataResponse;

final readonly class GetDashboardStatController
{
    public function __construct(
        private GetDashboardStat $getDashboardStat
    ){
    }

    public function __invoke(GetDashboardStatRequest $request): JsonDataResponse
    {
        $dashboardStat = $this->getDashboardStat->handle(
            $request->payload()
        );

        return new JsonDataResponse(
            data: $dashboardStat,
            message: 'Fetch dashboard stats successfully'
        );
    }
}