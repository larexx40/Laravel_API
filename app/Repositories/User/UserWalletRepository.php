<?php

namespace App\Repositories;

use App\Interfaces\UserWalletInterface;
use App\Models\UserWallet;

class UserWalletRepository implements UserWalletInterface{
    //addNewUserWallet', 'updateUserWallet', 'getAllUserWallet', 'changeUserWalletStatus', 'getUserWalletByUser', 'getUserWalletByid', 'deleteUserWallet'
    public function addNewUserWallet(array $newDetails){
        return UserWallet::create($newDetails);
    }

    public function updateUserWallet(array $newDetails)
    {
        return UserWallet::where('userid', $newDetails['userid'])->update($newDetails);
    }
    public function getAllUserWallet(){
        return UserWallet::all();
    }
    public function changeUserWalletStatus($userid, $status){
        return UserWallet::where('userid', $userid)->update(['status' => $status] );
    }
    public function getUserWalletByUserid($userid){
        return UserWallet::where('userid', $userid)->first();
    }
    public function deleteUserWallet($walletid){
        return UserWallet::where('wallettrackid', $walletid)->delete();
    }

    public function getUserWalletByid($walletid){
        return UserWallet::where('wallettrackid', $walletid)->first();
    }

    public function getWalletData($column, $value, $whatToGet=[]){
        if (empty($whatToGet)) {
            // If $whatToGet is not specified, get all columns
            return UserWallet::where($column, $value)->first();
        }
        // If $whatToGet is specified, fetch only those columns
        return UserWallet::where($column, $value)->select($whatToGet)->first();
    }
}
