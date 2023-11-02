<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermiApiDetails extends Model
{
    use HasFactory;
    //SELECT `id`, `sendfrom`, `apikey`, `status`, `name`, `smstype`, `smschannel` FROM `termiapidetails` WHERE 1
    protected $fillable = [
        'sendfrom',
        'apikey',
        'status',
        'name',
        'smstype',
        'smschannel',
    ];
}
