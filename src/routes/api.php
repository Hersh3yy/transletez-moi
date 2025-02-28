<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TranslateController;
use App\Http\Controllers\AuthController;

// Public API routes
Route::post('/login', [AuthController::class, 'apiLogin']);
Route::post('/register', [AuthController::class, 'apiRegister']);

// Protected API routes
Route::middleware('auth:api')->group(function () {
    Route::post('/translate/{target_language}', [TranslateController::class, 'translate']);
});
