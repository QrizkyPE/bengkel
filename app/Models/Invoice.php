<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'estimation_id',
        'total_amount',
        'status',
        'created_by',
        'paid_at',
        'notes'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function estimation()
    {
        return $this->belongsTo(Estimation::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Generate invoice number
    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('n'); 
        
        // Convert month to Roman numeral
        $romanMonths = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        
        $romanMonth = $romanMonths[$month];
        
        // Year and month
        $latestInvoice = self::where('invoice_number', 'like', "INVA/%/$romanMonth/$year")
            ->orderBy('id', 'desc')
            ->first();
        
        if ($latestInvoice) {
            // Sequence number from the latest invoice
            $parts = explode('/', $latestInvoice->invoice_number);
            $sequenceNumber = (int)$parts[1];
            $newSequenceNumber = $sequenceNumber + 1;
        } else {
    
            $startingSequenceNumber = 13558; // <-- Start number
            $newSequenceNumber = $startingSequenceNumber;
        }
        
        // Format number
        if ($newSequenceNumber < 1000) {
            $formattedSequence = str_pad($newSequenceNumber, 3, '0', STR_PAD_LEFT);
        } else {
            $formattedSequence = (string)$newSequenceNumber;
        }
        
        // Create invoice with month and year format
        return "INVA/$formattedSequence/$romanMonth/$year";
    }
} 