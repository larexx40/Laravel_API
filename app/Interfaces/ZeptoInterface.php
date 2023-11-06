<?php

namespace App\Interfaces;

Interface ZeptoInterface{
    public function addZeptoApi(array $newDetails);
    public function updateZeptoApi(array $newDetails);
    public function getAllZepto();
    public function acrivateSendGrid($id);
    public function getZeptoByid($id);
    public function deleteZepto($id);
    public function getZepto($column, $value);
    public function getZeptoData($column, $value, $whatToGet='');
    
    public function changeStatus($id, $status);
    
    public function checkIfExist($id);
}