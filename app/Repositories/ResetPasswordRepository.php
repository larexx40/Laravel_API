<?php

namespace App\Repositories;

use App\Interfaces\ResetPasswordInterface;
use App\Models\PasswordResetToken;

class ResetPasswordRepository implements ResetPasswordInterface {
    //addResetToken', 'getAllUserResetToken', 'deleteAllUserToken', 'getTokenByToken', 'deleteTokenByToken'

    public function getAllUserResetToken($email){
        return PasswordResetToken::where('email', $email)->get();
    }

    public function addResetToken(array $tokenDetails) 
    {
        return PasswordResetToken::create($tokenDetails);
    }
    public function deleteAllUserToken($email) 
    {
        return PasswordResetToken::where('email', $email)->delete();
    }

    public function getTokenByToken($token){
        $tokenDetails = PasswordResetToken::where('token', $token)->first();
        return $tokenDetails;
    }

    public function deleteTokenByToken($token){
        return PasswordResetToken::where('token', $token)->delete();
    }
    
}