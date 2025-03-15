<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estimation extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'service_advisor',
        'status',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
        'service_request_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function estimationItems()
    {
        return $this->hasMany(EstimationItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getServiceRequestAttribute()
    {
        return $this->estimationItems->first()->serviceRequest ?? null;
    }
} 