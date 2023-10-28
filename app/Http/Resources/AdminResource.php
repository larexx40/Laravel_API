<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
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
            'adminid' => $this->adminid,
            'name' => $this->name,
            'username' => $this->username,
            'status' => $this->status,
            "status_value" => $this->status == 1 ? "Active" : "Banned",
            'email' => $this->email,
            'phoneno' => $this->phoneno,
            'adminlevel' => $this->adminlevel,
            'adminpubkey' => $this->adminpubkey,
            'profile_updated' => $this->profile_updated,           
            'created_at' => $this->created_at->format('d/m/Y'),
        ];
    }
}
