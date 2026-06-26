<?php

use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UsersController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/registered-by-month', [UsersController::class, 'registeredByMonth']);
    Route::get('/users/by-role', [UsersController::class, 'byRole']);
    Route::get('/get-users', [UsersController::class, 'getUsers']);
    Route::post('/create-user', [UsersController::class, 'createUser']);
    Route::put('/update-user/{id}', [UsersController::class, 'updateUser']);
    Route::delete('/delete-user/{id}', [UsersController::class, 'deleteUser']);
});

