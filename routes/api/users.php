<?php

declare(strict_types=1);

use App\Http\Controllers\Users\V1\ListUserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.jwt'])->group(function () {

// Users Routes collection
    Route::get('/list', ListUserController::class);
});
