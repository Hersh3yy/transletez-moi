<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TranslateController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('translate/{target_language}', [TranslateController::class, 'translate']);
});
