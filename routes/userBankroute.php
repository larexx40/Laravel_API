<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserBankAccountController;
use Illuminate\Support\Facades\Route;

    Route::controller(UserBankAccountController::class)->group(function () {
        Route::get('bankaccounts', 'index');
        Route::get('bankaccounts/{id}', 'show');
        Route::post('bankaccounts', 'store');
    });

    Route::group(['middleware' => 'auth.jwt'], function () {
        // Define your protected routes here
        Route::get('userdetails', [AuthController::class, 'me']);
        Route::get('bankaccounts', [UserBankAccountController::class, 'index']);
        Route::get('bankaccounts/{id}', [UserBankAccountController::class, 'show']);
        Route::post('bankaccounts',  [UserBankAccountController::class, 'addBank']);
    });