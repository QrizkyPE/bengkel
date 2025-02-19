<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estimation extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_request_id',
        'estimated_cost',
        'notes',
        'estimator_id',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function estimator()
    {
        return $this->belongsTo(User::class, 'estimator_id');
    }
} 