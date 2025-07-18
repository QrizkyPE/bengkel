<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class spareparts extends Model
{
    public function serviceRequests()
    {
        return $this->hasMany(\App\Models\ServiceRequest::class, 'sparepart_id');
    }
}
