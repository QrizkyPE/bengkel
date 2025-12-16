<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jumlah',
        'satuan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];
}
