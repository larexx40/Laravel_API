<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSettings extends Model
{
    use HasFactory;


        //`name`, `iosversion`, `androidversion`, `webversion`, `activesmssystem`, `activemailsystem`, `emailfrom`, `baseurl`, `location`, `appshortdetail`, `activepaysystem`, `activebanksystem`, `supportemail`, `appimgurl`, `referalpointforusers`, `created_at`, `updated_at`, `activate_referral_bonus`
    protected $fillable = [
        'name',
        'iosversion',
        'androidversion',
        'webversion',
        'activesmssystem',
        'activemailsystem',
        'emailfrom',
        'baseurl',
        'location',
        'appshortdetail',
        'activepaysystem',
        'activebanksystem',
        'supportemail',
        'appimgurl',
        'referalpointforusers',
        'activate_referral_bonus',

    ];
}
