<?php

use App\Http\Controllers\AccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);

Route::middleware('auth:api')->group(function () {

    // auth
    Route::get('/me', [AuthController::class,'me']);
    Route::post('/logout', [AuthController::class,'logout']);

    // user & admin
    Route::apiResource('accounts', AccountController::class);

    // admin only
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Route::get('/users', [AdminUserController::class,'index']);
        // Route::get('/accounts', [AdminAccountController::class,'index']);
    });

});

