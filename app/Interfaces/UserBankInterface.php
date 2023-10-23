<?php

namespace App\Interfaces;

Interface UserBankInterface{
    public function getAllUserBanks();
    public function getUserBankByUserid($userid);
    public function getUserBankById($bankid);
    public function deleteUSerBank($bankid);
    public function createUserBank(array $bankDetails);
    public function updateUserBank($bankid, array $newDetails);
    public function setUSerBankAsDefault($bankid, array $newDetails);
}