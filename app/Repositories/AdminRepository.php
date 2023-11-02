<?php

namespace App\Repositories;

use App\Interfaces\AdminInterface;
use App\Models\Admin;

class AdminRepository implements AdminInterface{
    //'addAdmin', 'updateAdmin', 'getAllAdmin', 'changeAdminStatus', 'getAdminByid', 'deleteAdmin', 'getAdmin', 'getAdminData'
    public function addAdmin(array $newDetails){
        return Admin::create($newDetails);
    }
    public function updateAdmin(array $newDetails){
        return Admin::where('adminid', $newDetails['adminid'])->update($newDetails);
    }
    public function deleteAdmin($adminid){
        return Admin::destroy($adminid);
    }
    public function getAllAdmin(){
        return Admin::all();
    }
    public function changeAdminStatus($adminid, $status){
        return Admin::where('adminid', $adminid)->update(['status' => $status]);
    }
    public function getAdminByid($adminid){
        return Admin::where('adminid', $adminid)->first();
    }
    public function getAdmin($column, $value){
        return Admin::where($column, $value)->first();
    }

    public function getAdminData($column, $value, $whatToGet=[]){
        if (empty($whatToGet)) {
            // If $whatToGet is not specified, get all columns
            return Admin::where($column, $value)->first();
        }
        // If $whatToGet is specified, fetch only those columns
        return Admin::where($column, $value)->select($whatToGet)->first();
    }
}
