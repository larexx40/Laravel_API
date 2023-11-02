<?php

namespace App\Interfaces;

Interface BankAllowedInterface{
    public function addNewBank(array $newDetails);
    public function updateBank(array $newDetails);
    public function getAllBanks();
    public function changeBankStatus($bankid, $status);
    public function getbank($bankid);
    public function deleteBank($bankid);
    public function getBankData($column, $value, $whatToGet=[]);
}
