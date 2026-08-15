<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'message' => 'Simple Ticket API is Running',]);
});

Route::prefix('v1')->group(function () {
    // Auth Group 
    Route::prefix('auth')->group(base_path('routes/api/auth.php'));
    
    // Users Group 
    Route::prefix('users')->group(base_path('routes/api/users.php'));

    // Tickets Group
    Route::prefix('tickets')->group(base_path('routes/api/tickets.php'));

    // Dashboard Group
    Route::prefix('dashboard')->group(base_path('routes/api/dashboard.php'));
});
