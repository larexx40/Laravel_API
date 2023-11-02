<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimpleApiDetails extends Model
{
    use HasFactory;
    //SELECT `id`, `channel_id`, `public_key`, `secret_key`, `status`, `created_at`, `updated_at` FROM `simpuapidetails` WHERE 1
    protected $fillable = [
        'channel_id',
        'public_key',
        'secret_key',
        'status',
        'created_at',
        'updated_at',
    ];
}
