<?php

namespace App\Interfaces;

Interface UserTokenInterface{
    public function addUserToken(array $newDetails);
    public function getAllUserToken($identity);
    public function deleteAllUserToken($identity);
    public function getTokenByToken($token);
    public function deleteTokenByToken($token);
    public function getTokenData($column, $value, $whatToGet='');
}