<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk';

    protected $fillable = [
        'sparepart_id',
        'jumlah_masuk',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'jumlah_masuk' => 'integer',
    ];

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
