<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\FirstTimeLoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('first-time-login', [FirstTimeLoginController::class, 'view'])
        ->name('first-time-login');

    Route::post('first-time-login', [FirstTimeLoginController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'view'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'create']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
