<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBankAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'bankid' => $this->bankid,
            'user_id' => $this->user_id,
            'bank_name' => $this->bank_name,
            'account_no' => $this->account_no,
            'account_name' => $this->account_name,
            'is_default' => $this->is_default,
            'status' => $this->status,
            "sys_bank_id" => $this->sys_bank_id,
            'created_at' => $this->created_at->format('d/m/Y'),
        ];
    }
}

