<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::get('me', 'me');

    });

    Route::group(['middleware' => 'jwt.auth'], function () {
        // Define your protected routes here
        Route::get('userdetails', [AuthController::class, 'me']);
        // Route::get('bankaccounts/{id}', 'show');
        // Route::post('bankaccounts', 'store');
    });