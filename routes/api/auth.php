<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\V1\LoginController;
use App\Http\Controllers\Auth\V1\RegisterController;
use Illuminate\Support\Facades\Route;

// Auth Routes collection
Route::post('/login', LoginController::class);
Route::post('/register', RegisterController::class);
