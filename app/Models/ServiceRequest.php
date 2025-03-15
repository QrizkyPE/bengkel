<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $table = 'service_requests';

    protected $fillable = [
        'user_id',
        'work_order_id',
        'sparepart_name',
        'quantity',
        'satuan', //
        'kebutuhan_part',
        'keterangan',
        'no_spk',
        'no_polisi',
        'type_kendaraan',
        'kilometer',
        'user',
        'tanggal',
        'keluhan',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function items()
    {
        return $this->hasMany(ServiceRequestItem::class);
    }

    public function estimationItems()
    {
        return $this->hasMany(EstimationItem::class);
    }

    // public $timestamps = true;
}
