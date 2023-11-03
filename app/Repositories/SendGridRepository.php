<?php

namespace App\Repositories;

use App\Interfaces\SendGridInterface;
use App\Models\SendGridApiDetails;

class SendGridReepository implements SendGridInterface{
    //'addSendGridApi', 'updateSendGrid', 'getAllSendGrid', 'changeSendGridStatus', 'getSendGridByid', 'deleteSendGrid', 'getSendGrid', 'getSendGridData'
    public function addSendGridApi(array $newDetails){
        return SendGridApiDetails::create($newDetails);
    }
    public function updateSendGrid(array $newDetails){
        return SendGridApiDetails::where('id', $newDetails['id'])->update($newDetails);
    }
    public function getAllSendGrid(){
        return SendGridApiDetails::all();
    }
    public function activateSendGrid($id){
        // set every status to 0
        SendGridApiDetails::where('status', 1)->update(['status' => 0]);
        return SendGridApiDetails::where('id', $id)->update(['status' => 1]);
    }
    public function getSendGridByid($id){
        return SendGridApiDetails::where('id', $id)->first();
    }
    public function deleteSendGrid($id){
        return SendGridApiDetails::where('id', $id)->delete();
    }
    public function getSendGrid($column, $value){
        return SendGridApiDetails::where($column, $value)->first();
    }
    public function getSendGridData($column, $value, $whatToGet=''){
        if(empty($whatToGet)) {
            return SendGridApiDetails::where($column, $value)->first();
        }
        return SendGridApiDetails::where($column, $value)->$whatToGet();
    }

    public function checkIfExist($id){
        return SendGridApiDetails::where("id", $id)->exists();
    }
}
