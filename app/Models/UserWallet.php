<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        "userid",
        "currencytag",
        "wallettrackid",
        "walletbal",
        "walletpendbal",
        "walletescrowbal"
    ];

    protected $dates = ['created_at','updated_at'];
    protected function serializeDate(DateTimeInterface $date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function currencySystem()
    {
        return $this->belongsTo(CurrencySystem::class, 'currencytag');
    }
}
