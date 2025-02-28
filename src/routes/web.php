<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TranslateController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Auth routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        $target_language = 'en'; // Default language is english
        return view('frontend', compact('target_language'));
    });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Add translation endpoint for web session authentication
    Route::post('/translate/{target_language}', [TranslateController::class, 'translate']);
});
