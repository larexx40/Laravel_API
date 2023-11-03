<?php

namespace App\Interfaces;

Interface CurrencySystemInterface{
    public function addCurrencySystem(array $newDetails);
    public function updateCurrencySystem(array $newDetails);
    public function getAllCurrencySystem();
    public function changeCurrencySystemStatus($currebcyid, $status);
    public function getCurrencySystemByid($currencyid);
    public function deleteCurrencySystem($currencyid);
    public function getCurrencySystemData($column, $value, $whatToGet='');
}
