<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencySystem extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "nametag",
        "sign",
        "currency_status",
        "currencytag",
        "sidebarname",
        "imglink",
        "activatesend",
        "activatereceive",
        "maxsendamtauto",
        "defaultforusers",
    ];

    protected $dates = ['created_at','updated_at'];
    protected function serializeDate(DateTimeInterface $date)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
    }

    public function userWallets()
    {
        return $this->hasMany(UserWallet::class);
    }
}
