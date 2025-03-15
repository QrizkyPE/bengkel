<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_polisi',
        'kilometer',
        'no_spk',
        'type_kendaraan',
        'customer_name',
        'keluhan',
        'user_id'
    ];

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function estimations()
    {
        return $this->hasMany(Estimation::class);
    }
} 