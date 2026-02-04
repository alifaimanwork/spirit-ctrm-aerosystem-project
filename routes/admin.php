<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\AdminsMiddleware;
use Illuminate\Support\Facades\Route;

// superadmin and admin only
Route::middleware(AdminsMiddleware::class)->group(function () {
    Route::get('users', [UserController::class, 'view_user_list'])
        ->name('users');

    Route::post('user', [UserController::class, 'create'])
        ->name('user');

    Route::patch('user', [UserController::class, 'update']);

    Route::delete('user', [UserController::class, 'delete']);
});
