<?php

namespace App\Interfaces;

Interface SendGridInterface{
    public function addSendGridApi(array $newDetails);
    public function updateSendGrid(array $newDetails);
    public function getAllSendGrid();
    public function activateSendGrid($id);
    public function getSendGridByid($id);
    public function deleteSendGrid($id);
    public function getSendGrid($column, $value);
    public function getSendGridData($column, $value, $whatToGet='');
    public function checkIfExist($id);
}
