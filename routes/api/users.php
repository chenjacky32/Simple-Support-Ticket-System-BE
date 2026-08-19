<?php

declare(strict_types=1);

use App\Http\Controllers\Users\V1\UpdateUserStatusController;
use App\Http\Controllers\Users\V1\UpdateUserController;
use App\Http\Controllers\Users\V1\GetUserDetailController;
use App\Http\Controllers\Users\V1\GetUserProfileController;
use App\Http\Controllers\Users\V1\ListUserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.jwt', 'allow.only.roles:SUPERADMIN,ADMIN,USERS'])->group(function () {
    Route::get('/profile', GetUserProfileController::class);
});

Route::middleware(['api.jwt', 'allow.only.roles:SUPERADMIN'])->group(function () {
    // Users Routes collection
    Route::get('/list', ListUserController::class);
    Route::get('/{id}', GetUserDetailController::class);
    Route::put('/{id}', UpdateUserController::class);
    Route::patch('/{id}/status', UpdateUserStatusController::class);
});
