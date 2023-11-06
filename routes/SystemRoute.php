<?php

use App\Http\Controllers\CurrencySystemController;
use App\Http\Controllers\SystemSettingsController;
use Illuminate\Support\Facades\Route;
    // Unprotected (public) routes
    // Route::prefix('admin/thirdparty')->group(function () {
    //     Route::get('/', 'AdminController@index'); 
    //     // Add more public routes as needed
    // });

    // Protected (authenticated) routes
    Route::prefix('admin/system-settings')->middleware(['auth.admin'])->group(function () {
        Route::get('/', [SystemSettingsController::class,'getSendgrid']);
        // Add more protected routes as needed
    });

    //Zepto
    Route::prefix('admin/currency-system')->middleware(['auth.admin'])->group(function () {
        Route::get('/', [CurrencySystemController ::class,'getAllCurrencySystem']);
        Route::get('/{curencytag}', [CurrencySystemController::class,'getCurrencySystemByid']);
        Route::post('add', [CurrencySystemController::class, 'addCurrencySytem']);
        Route::put('update', [CurrencySystemController::class, 'updateCurrencySystem']);
        Route::put('change-status', [CurrencySystemController::class, 'changeCurrencySystemStatus']);
        Route::delete('delete/{curencytag}', [CurrencySystemController::class,'deleteCurrencySystem']);
        // Add more protected routes as needed
    });