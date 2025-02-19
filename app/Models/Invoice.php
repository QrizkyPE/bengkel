<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimation_id',
        'final_cost',
        'payment_terms',
        'due_date',
        'additional_notes',
        'biller_id',
    ];

    protected $dates = [
        'due_date',
    ];

    public function estimation()
    {
        return $this->belongsTo(Estimation::class);
    }

    public function biller()
    {
        return $this->belongsTo(User::class, 'biller_id');
    }
} 