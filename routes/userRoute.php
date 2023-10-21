<?php

use App\Http\Controllers\NewUserController;
use Illuminate\Support\Facades\Route;


Route::controller(NewUserController::class)->group(function () {
    Route::get('newuser', 'getAllUsers')->middleware('jwt.auth');
    Route::post('newuser', 'addNewUser');
    Route::get('newuser/{userid}', 'getUserById');
});

// Route::group(['middleware' => 'jwt.auth'], function () {
//     // Define your protected routes here
//     Route::get('newuser', 'getAllUsers');
// });
