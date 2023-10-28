<?php

namespace App\Interfaces;

Interface AdminInterface{
    public function addAdmin(array $newDetails);
    public function updateAdmin(array $newDetails);
    public function getAllAdmin();
    public function changeAdminStatus($adminid, $status);
    public function getAdminByid($adminid);
    public function deleteAdmin($adminid);
    public function getAdmin($column, $value);
    public function getAdminData($column, $value, $whatToGet='');
}