<?php

namespace App\Repositories;

use App\Interfaces\CurrencySystemInterface;
use App\Models\CurrencySystem;

class CurrencySystemRepository implements CurrencySystemInterface{
    //'addCurrencySystem', 'updateCurrencySystem', 'getAllCurrencySystem', 'changeCurrencySystemStatus', 'getCurrencySystemByid', 'deleteCurrencySystem', 'getCurrencySystemData'
    public function addCurrencySystem(array $newDetails){
        return CurrencySystem::create($newDetails);
    }

    public function updateCurrencySystem(array $newDetails)
    {
        return CurrencySystem::where('currencytag', $newDetails['currencytag'])->update($newDetails);
    }
    public function getAllCurrencySystem(){
        return CurrencySystem::all();
    }
    public function changeCurrencySystemStatus($currencytag, $status){
        return CurrencySystem::where('currencytag', $currencytag)->update(['status' => $status] );
    }
    public function getCurrencySystemByid($currencytag){
        return CurrencySystem::where('currencytag', $currencytag)->first();
    }
    public function deleteCurrencySystem($currencytag){
        return CurrencySystem::where('currencytag', $currencytag)->delete();
    }

    public function getCurrencySystemData($column, $value, $whatToGet=[]){
        if (empty($whatToGet)) {
            // If $whatToGet is not specified, get all columns
            return CurrencySystem::where($column, $value)->first();
        }
        // If $whatToGet is specified, fetch only those columns
        return CurrencySystem::where($column, $value)->select($whatToGet)->first();
    }
}
