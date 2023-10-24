<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetTokenController;
use Illuminate\Support\Facades\Route;


    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::get('me', 'me');

    });

    Route::group(['middleware' => 'auth.jwt'], function () {
        // Define your protected routes here
        Route::get('userdetails', [AuthController::class, 'me']);
    });

    Route::controller(PasswordResetTokenController::class)->group(function () {
        Route::post('forget-password', 'forgetPassword');
        Route::post('verify-token', 'verifyToken');
        Route::post('reset-password', 'resetPassword');
    });