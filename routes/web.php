<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(
        [
            'message' => 'Welcome to Simple Support Ticket System API'
        ]
    );
});
