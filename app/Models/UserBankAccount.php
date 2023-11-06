<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        "bankid",
        "user_id",
        "bank_name",
        "account_no",
        "account_name",
        "is_default",
        "status",
        "name",
        "sys_bank_id"

    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bankAllowed()
    {
        return $this->belongsTo(BankAllowed::class, 'sys_bank_id', "sysbankcode")->select(["sysbankcode",'name']);
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        //return Carbon::createFromFormat('Y-m-d H:i:s', $dates)->diffForHumans();
        // OR
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
    }


}

