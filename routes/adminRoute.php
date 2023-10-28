<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

    Route::controller(AdminController::class)->group(function () {
        Route::post('admin/add', 'addAdmin');
    });


    // Route::group(['middleware'=> 'auth.admin'], function () {
    //     Route::get('admin/details', [AuthController::class, 'me']);
    // });
