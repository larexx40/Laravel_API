<?php

namespace App\Repositories;

use App\Interfaces\AdminInterface;
use App\Interfaces\SystemDefaultInterface;
use App\Models\Admin;
use App\Models\SimpleApiDetails;
use App\Models\SystemSettings;
use App\Models\TermiApiDetails;
use App\Models\ZeptoApiDetails;

class SystemDefaultRepository implements SystemDefaultInterface{
    //'updateSystemSettings', 'getSystemSettings', 'getActiveTermi', 'getActiveZepto'
    public function updateSystemSettings(array $newDetails){
        return SystemSettings::where('id', $newDetails['id'])->update($newDetails);
    }
    public function getSystemSettings(){
        return SystemSettings::all();
    }
    public function getActiveTermi(){
        return TermiApiDetails::where('status', 1)->first();
    }

    public function getActiveZepto(){
        return ZeptoApiDetails::where('status', 1)->first();
    }

    public function getActiveSimpleSMS(){
        return SimpleApiDetails::where('status', 1)->first();
    }

}
