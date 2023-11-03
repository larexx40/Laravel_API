<?php

namespace App\Repositories;

use App\Interfaces\BankAllowedInterface;
use App\Models\BankAllowed;

class BankAllowedRepository implements BankAllowedInterface{
    //'addNewBank', 'updateBank', 'getAllBanks', 'changeBankStatus', 'getbank', 'deleteBankid', 'getBankData'
    public function addNewBank(array $newDetails){
        return BankAllowed::create($newDetails);
    }

    public function updateBank(array $newDetails)
    {
        return BankAllowed::where('sysbankcode', $newDetails['sysbankcode'])->update($newDetails);
    }
    public function getAllBanks(){
        return BankAllowed::all();
    }
    public function changeBankStatus($bankid, $status){
        return BankAllowed::where('sysbankcode', $bankid)->update(['status' => $status] );
    }
    public function getbank($bankid){
        return BankAllowed::where('sysbankcode', $bankid)->first();
    }
    public function deleteBank($bankid){
        return BankAllowed::where('sysbankcode', $bankid)->delete();
    }

    public function getBankData($column, $value, $whatToGet=[]){
        if (empty($whatToGet)) {
            // If $whatToGet is not specified, get all columns
            return BankAllowed::where($column, $value)->first();
        }
        // If $whatToGet is specified, fetch only those columns
        return BankAllowed::where($column, $value)->select($whatToGet)->first();
    }
}
