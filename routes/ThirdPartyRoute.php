<?php

use App\Http\Controllers\SendGridApiDetailsController;
use App\Http\Controllers\ZeptoApiDetailsController;
use Illuminate\Support\Facades\Route;
    // Unprotected (public) routes
    // Route::prefix('admin/thirdparty')->group(function () {
    //     Route::get('/', 'AdminController@index'); 
    //     // Add more public routes as needed
    // });

    // Protected (authenticated) routes
    //sendgrid
    Route::prefix('admin/thirdparty/sendgrid')->middleware(['auth.admin'])->group(function () {
        Route::get('/', [SendGridApiDetailsController::class,'getSendgrid']);
        Route::get('/{id}', [SendGridApiDetailsController::class,'getSendgridByid']);
        Route::post('add', [SendGridApiDetailsController::class, 'addSendGrid']);
        Route::put('update', [SendGridApiDetailsController::class, 'updateSendGrid']);
        Route::put('change-status', [SendGridApiDetailsController::class, 'changeSendGridStatus']);
        Route::delete('delete', [SendGridApiDetailsController::class,'delete']);
        // Add more protected routes as needed
    });

    //Zepto
    Route::prefix('admin/thirdparty/zepto')->middleware(['auth.admin'])->group(function () {
        Route::get('/', [ZeptoApiDetailsController::class,'getZepto']);
        Route::get('/{id}', [ZeptoApiDetailsController::class,'getZepto']);
        Route::post('add', [ZeptoApiDetailsController::class, 'addZepto']);
        Route::put('update', [ZeptoApiDetailsController::class, 'updateZepto']);
        Route::put('change-status', [ZeptoApiDetailsController::class, 'changeZeptoStatus']);
        Route::delete('delete', [ZeptoApiDetailsController::class,'delete']);
        // Add more protected routes as needed
    });