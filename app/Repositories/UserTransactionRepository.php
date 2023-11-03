<?php

namespace App\Repositories;

use App\Interfaces\UserTransactionInterface;
use App\Models\Transactions;

class UserTransactionRepository implements UserTransactionInterface{
    //newTransaction', 'updateTransaction', 'getMytransaction', 'getTransactionByid', 'getTransactionData'
    public function getTransaction($transactionid){
        return Transactions::where('transactionid', $transactionid)->first();
    }
    public function newTransaction(array $newDetails){
        return Transactions::create($newDetails);
    }

    public function updateTransaction(array $newDetails)
    {
        return Transactions::where('transactionid', $newDetails['transactionid'])->update($newDetails);
    }
    public function getAllTransactions(){
        return Transactions::orderBy('id', 'desc')->get();
    }
    public function changeTransactionsStatus($userid, $status){
        return Transactions::where('userid', $userid)->update(['status' => $status] );
    }
    public function getMytransaction($userid){
        return Transactions::where('userid', $userid)->orderBy('id', 'desc')->get();
    }
    public function getUserTransactionByid($userid, $transactionid){
        return Transactions::where('userid', $userid)->where('transactionid', $transactionid)->first();
    }

    public function getTransactionData($column, $value, $whatToGet=[]){
        if (empty($whatToGet)) {
            // If $whatToGet is not specified, get all columns
            return Transactions::where($column, $value)->first();
        }
        // If $whatToGet is specified, fetch only those columns
        return Transactions::where($column, $value)->select($whatToGet)->first();
    }

    public function checkIfExist($userid, $column, $value,){
        return Transactions::where($column, $value)->where("userid", $userid)->exists();
    }
}
