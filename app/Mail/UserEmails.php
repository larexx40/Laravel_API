<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserEmails extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function welcomeEmail()
    {
        return $this->view('emails.welcome_email')
                    ->subject('Welcome to Our Website');
    }

    public function loginEmail()
    {
        return $this->view('emails.login_email')
                    ->subject('Login Notification');
    }

    public function emailVerificationEmail($otp)
    {
        return $this->view('emails.email_verification_email', ['otp'=> $otp])->subject('Email Verification');
    }

    public function phoneVerificationEmail($otp)
    {
        return $this->view('emails.email_verification_email')
                    ->subject('Email Verification');
    }

    public function passwordResetEmail($user, $otp)
    {
        return $this->view('emails.password_reset_email', [
            'user' => $user,
            'otp' => $otp,
        ])->subject('Password Reset Request');
    }

    // Add more methods for other email templates

}
