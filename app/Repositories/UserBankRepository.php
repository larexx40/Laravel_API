<?php

namespace App\Repositories;
use App\Interfaces\UserBankInterface;
use App\Models\UserBankAccount;

class UserBankRepository implements UserBankInterface{
    // getUserBankByUserid', 'getUserBankById', 'deleteUSerBank', 'createUserBank', 'updateUserBank', 'setUSerBankAsDefault'
    public function getAllUserBanks(){
        return UserBankAccount::all();
    }
    public function getUserBankByUserid($userid){
        $userBankAccount = UserBankAccount::where('user_id', $userid)->orderBy('id', 'desc')->get();
        return $userBankAccount;
    }
    public function getUserBankById($bankid){
        $userBankAccount = UserBankAccount::where('bankid', $bankid)->first();
        return $userBankAccount;
    }

    public function deleteUSerBank($bankid){
        $userBankAccount = UserBankAccount::where('bankid', $bankid)->delete();
        return $userBankAccount;
    }

    public function createUserBank(array $bankDetails){
        $userBankAccount = new UserBankAccount($bankDetails);
        $userBankAccount->save();
        return $userBankAccount;
    }

    public function updateUserBank($bankid, array $newDetails){
        $userBankAccount = UserBankAccount::query()->where('bankid', $bankid)->update($newDetails);
        return $userBankAccount;
    }

    public function setUSerBankAsDefault($bankid, array $newDetails){
        $userBankAccount = UserBankAccount::query()->where('bankid', $bankid)->update($newDetails);
        return $userBankAccount;
    }
}