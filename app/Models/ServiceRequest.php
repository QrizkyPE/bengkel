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
        'sparepart_name',
        'quantity',
        'satuan', //
        'kebutuhan_part',
        'keterangan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public $timestamps = true;
}
