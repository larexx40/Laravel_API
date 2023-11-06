<?php

namespace App\Repositories;

use App\Interfaces\ZeptoInterface;
use App\Models\ZeptoApiDetails;

class ZeptoRepository implements ZeptoInterface {
    //'addZeptoApi', 'updateZeptoApi', 'getAllZepto', 'changeZeptoStatus', 'getZeptoByid', 'deleteZepto', 'getZepto', 'getZeptoData'

    public function addZeptoApi(array $newDetails){
        return ZeptoApiDetails::create($newDetails);
    }
    public function updateZeptoApi(array $newDetails){
        return ZeptoApiDetails::where('id', $newDetails['id'])->update($newDetails);
    }
    public function getAllZepto(){
        return ZeptoApiDetails::all();
    }
    public function acrivateSendGrid($id){
        return ZeptoApiDetails::where('id', $id)->update(['status' => 1]);
    }
    public function getZeptoByid($id){
        return ZeptoApiDetails::where('id', $id)->first();
    }
    public function deleteZepto($id){
        return ZeptoApiDetails::where('id', $id)->delete();
    }
    public function getZepto($column, $value){
        return ZeptoApiDetails::where($column, $value)->first();
    }
    public function getZeptoData($column, $value, $whatToGet=''){
        if(empty($whatToGet)) {
            return ZeptoApiDetails::where($column, $value)->first();
        }else{
            //select column in whatToGet WHERE column = value
            return ZeptoApiDetails::where($column, $value)->$whatToGet();
        }
    }

    public function checkIfExist($id){
        return ZeptoApiDetails::where("id", $id)->exists();
    }

    public function changeStatus($id, $status){
        return ZeptoApiDetails::where("id", $id)->update(["status"=> $status]);
    }
}