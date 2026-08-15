<?php

declare(strict_types=1);

use App\Http\Controllers\Tickets\V1\ListTicketController;
use App\Http\Controllers\Tickets\V1\CreateTicketController;
use App\Http\Controllers\Tickets\V1\GetTicketDetailController;
use App\Http\Controllers\Tickets\V1\ReplyTicketController;
use App\Http\Controllers\Tickets\V1\UpdateTicketStatusController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.jwt'])->group(function () {
    Route::get('/', ListTicketController::class)->middleware('allow.only.roles:USERS,ADMIN,SUPERADMIN');
    Route::post('/', CreateTicketController::class)->middleware('allow.only.roles:USERS');
    Route::get('/{id}', GetTicketDetailController::class)->middleware('allow.only.roles:USERS,ADMIN,SUPERADMIN');
    Route::patch('/{id}/status', UpdateTicketStatusController::class)->middleware('allow.only.roles:ADMIN');
    Route::post('/{id}/replies', ReplyTicketController::class)->middleware('allow.only.roles:ADMIN');
});