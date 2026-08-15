<?php

declare(strict_types=1);

use App\Http\Controllers\Dashboard\V1\GetDashboardStatController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.jwt', 'allow.only.roles:USERS,ADMIN,SUPERADMIN'])->group(function () {
    Route::get('/stat', GetDashboardStatController::class);
});