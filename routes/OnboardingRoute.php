<?php

use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;


    // unprotected route
    Route::controller(OnboardingController::class)->group(function () {
        Route::post('send-email-otp', 'registerEmail');
        Route::post('verify-email', 'verifyMail');
        Route::post('resend/mailotp', 'resendMailOTP');
        Route::post('resend/phoneotp', 'resendPhoneOTP');
        Route::post('send-phone-otp', 'registerPhone');
        Route::post('verify-phone', 'verifyPhone');
        Route::post('setpin', 'setPin');
        Route::post('verify-pin', 'verifyPin');
        Route::post('register', 'register');
        // Route::get('get-reg-summary', 'getRegSummary');
        // Route::post('upload-profile-pic', 'uploadProfilePic');

    });

    //protected route
    Route::group(['middleware' => 'auth.jwt'], function () {
        // Define your protected routes here
        Route::get('get-reg-summary', [OnboardingController::class, 'getRegSummary']);
    });



