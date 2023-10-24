<?php

namespace App\Interfaces;

Interface ResetPasswordInterface{
    public function addResetToken(array $newDetails);
    public function getAllUserResetToken($email);
    public function deleteAllUserToken($email);
    public function getTokenByToken($token);
    public function deleteTokenByToken($token);
}