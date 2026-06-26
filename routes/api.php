<?php

use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UsersController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/registered-by-month', [UsersController::class, 'registeredByMonth']);
    Route::get('/users/by-role', [UsersController::class, 'byRole']);
    Route::get('/get_users', [UsersController::class, 'getUsers']);
    Route::post('/create_user', [UsersController::class, 'createUser']);
    Route::put('/update_user/{id}', [UsersController::class, 'updateUser']);
    Route::delete('/delete_user/{id}', [UsersController::class, 'deleteUser']);
});

