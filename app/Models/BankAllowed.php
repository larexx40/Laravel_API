<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAllowed extends Model
{
    use HasFactory;
    //SELECT `id`, `name`, `image_link`, `sysbankcode`, `oneappbankcode`, `paystackbankcode`, `monifybankcode`, `shbankcodes`, `created_at`, `updated_at`, `status` FROM `bankaccountsallowed` WHERE 1
    protected $fillable = [
        "name",
        "image_link",
        "sysbankcode",
        "paystackbankcode",
        "monifybankcode",
        "shbankcode",
        "status",
    ];

}
