<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTokens extends Model
{
    use HasFactory;
    protected $fillable = [
        "userid",
        "user_identity",
        "identity_type",
        "token",
        "token_type",
        "expire_at",
    ];

    protected $dates = ['created_at','updated_at'];
    protected function serializeDate(DateTimeInterface $date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
    }
}
