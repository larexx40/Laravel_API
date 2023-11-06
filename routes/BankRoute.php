<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankAllowedController;
use App\Http\Controllers\UserBankAccountController;
use Illuminate\Support\Facades\Route;

    Route::controller(UserBankAccountController::class)->group(function () {
        Route::get('bankaccounts', 'index');
        Route::get('bankaccounts/{id}', 'show');
        Route::post('bankaccounts', 'store');
    });

    Route::group(['middleware' => 'auth.jwt'], function () {
        // Define your protected routes here
        Route::get('bankaccounts', [UserBankAccountController::class, 'index']);
        Route::get('bankaccounts/{id}', [UserBankAccountController::class, 'show']);
        Route::post('bankaccounts',  [UserBankAccountController::class, 'addBank']);
    });

    //Allowed Banks
    Route::prefix('admin/banks')->middleware(['auth.admin'])->group(function () {
        Route::get('/', [BankAllowedController::class,'getAllBanks']);
        Route::get('/{id}', [BankAllowedController::class,'getSendgridByid']);
        Route::post('add', [BankAllowedController::class, 'addBank']);
        Route::put('update', [BankAllowedController::class, 'updateBank']);
        Route::put('change-status', [BankAllowedController::class, 'changeBankStatus']);
        Route::delete('delete', [BankAllowedController::class,'deleteBank']);
        // Add more protected routes as needed
    });