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

    // Generate a new invoice number
    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        
        // Get the latest invoice for the current year
        $latestInvoice = self::where('invoice_number', 'like', "INVA/%/$year")
            ->orderBy('id', 'desc')
            ->first();
        
        if ($latestInvoice) {
            // Extract the sequence number from the latest invoice
            $parts = explode('/', $latestInvoice->invoice_number);
            $sequenceNumber = (int)$parts[1];
            $newSequenceNumber = $sequenceNumber + 1;
        } else {
            // Start with 1 if no invoices exist for this year
            $newSequenceNumber = 1;
        }
        
        // Format the sequence number with leading zeros (e.g., 001, 012, 123)
        $formattedSequence = str_pad($newSequenceNumber, 3, '0', STR_PAD_LEFT);
        
        // Create the new invoice number
        return "INVA/$formattedSequence/$year";
    }
} 