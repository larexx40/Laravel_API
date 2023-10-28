<?php

use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;


    // unprotected route
    Route::controller(OnboardingController::class)->group(function () {
        Route::post('register-email', 'registerEmail');
        Route::post('verify-email', 'verifyMail');
        Route::post('resend/mailotp', 'resendMailOTP');
        Route::post('resend/phoneotp', 'resendPhoneOTP');

    });

    //protected route
    Route::group(['middleware' => 'auth.jwt'], function () {
        // Define your protected routes here
        Route::post('register-phone', [OnboardingController::class, 'registerPhone']);
        Route::post('setpin', [OnboardingController::class, 'setPin']);
    });



