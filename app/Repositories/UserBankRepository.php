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
        // cast it with the relationship declared in model
        $userBankAccounts = UserBankAccount::with('bankAllowed')->where('user_id', $userid)->orderBy('id', 'desc')->get();

        $userBankAccounts->each(function ($bankAccount) {
            // Access the related banlAllowed information
            $bankName = $bankAccount->bankAllowed->name;
        
            // Set the values directly in the bankAccount
            $bankAccount->bankName = $bankName;
        
            // Unset the bankAllowed relationship if you don't need it anymore
            unset($bankAccount->bankAllowed);
            unset($bankAccount->updated_at);
        });

        return $userBankAccounts;
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