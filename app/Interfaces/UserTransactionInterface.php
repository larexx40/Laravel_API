<?php

namespace App\Interfaces;

Interface UserTransactionInterface{
    public function getAllTransactions();// for admin
    public function getTransaction($transactionid);
    public function newTransaction(array $newDetails);
    public function updateTransaction(array $newDetails);
    public function getMytransaction($userid);
    public function getUserTransactionByid($userid, $transactionid);
    public function getTransactionData($column, $value, $whatToGet='');
    public function checkIfExist($userid, $column, $value,);

}
