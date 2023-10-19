<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserBankAccountController;
use Illuminate\Support\Facades\Route;

    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::get('me', 'me');

    });

    Route::controller(UserBankAccountController::class)->group(function () {
        Route::get('bankaccounts', 'index');
        Route::get('bankaccounts/{id}', 'show');
        Route::post('bankaccounts', 'store');
    });
