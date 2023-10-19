<?php

use App\Http\Controllers\NewUserController;
use Illuminate\Support\Facades\Route;


Route::controller(NewUserController::class)->group(function () {
    Route::get('newuser', 'getAllUsers');
    Route::post('newuser', 'addNewUser');
    Route::get('newuser/{userid}', 'getUserById');
});
