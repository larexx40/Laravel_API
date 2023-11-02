<?php

namespace App\Models;

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
        return $this->belongsTo(BankAllowed::class, 'sys_bank_id');
    }


}

