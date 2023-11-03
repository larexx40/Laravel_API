<?php

namespace App\Interfaces;

Interface UserWalletInterface{
    public function addNewUserWallet(array $newDetails);
    public function updateUserWallet(array $newDetails);
    public function getAllUserWallet();
    public function changeUserWalletStatus($walletid, $status);
    public function getUserWalletByUserid($userid);
    public function getUserWalletByid($walletid);
    public function deleteUserWallet($walletid);
    public function getWalletData($column, $value, $whatToGet='');
    public function checkIfExist($column, $value,);

}
