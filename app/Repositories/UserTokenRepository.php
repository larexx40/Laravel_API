<?php

namespace App\Repositories;

use App\Interfaces\UserTokenInterface;
use App\Models\UserTokens;

class UserTokenRepository implements UserTokenInterface {
    //'getAllUserToken', 'deleteAllUserToken', 'getTokenByToken', 'deleteTokenByToken
    public function addUserToken(array $newDetails){
        return UserTokens::create($newDetails);
    }
    public function getAllUserToken($identity){
        return UserTokens::where('identity', $identity)->get();
    }
    public function deleteAllUserToken($identity){
        return UserTokens::where('identity', $identity)->delete();
    }
    public function getTokenByToken($token){
        return UserTokens::where('token', $token)->first();
    }
    public function deleteTokenByToken($token){
        return UserTokens::where('token', $token)->delete();
    }
    public function getTokenData($column, $value, $whatToGet=''){
        if(empty($whatToGet)){
            return UserTokens::where($column, $value)->first();
        }
        return UserTokens::where($column, $value)->$whatToGet();
    }
    
}