<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Estimation;
use App\Models\Invoice;
use Illuminate\Http\Request;
use PDF;

class InvoiceController extends Controller
{
    public function index()
    {
        // Get all estimated service requests
        $estimations = Estimation::with('serviceRequest')
            ->whereHas('serviceRequest', function($query) {
                $query->where('status', 'estimated');
            })->get();
        return view('invoices.index', compact('estimations'));
    }

    public function show(Estimation $estimation)
    {
        return view('invoices.show', compact('estimation'));
    }

    public function createInvoice(Estimation $estimation)
    {
        return view('invoices.create', compact('estimation'));
    }

    public function storeInvoice(Request $request, Estimation $estimation)
    {
        $request->validate([
            'final_cost' => 'required|numeric',
            'payment_terms' => 'required|string',
            'due_date' => 'required|date',
            'additional_notes' => 'nullable|string',
        ]);

        $invoice = Invoice::create([
            'estimation_id' => $estimation->id,
            'final_cost' => $request->final_cost,
            'payment_terms' => $request->payment_terms,
            'due_date' => $request->due_date,
            'additional_notes' => $request->additional_notes,
            'biller_id' => auth()->id(),
        ]);

        // Update service request status
        $estimation->serviceRequest->update(['status' => 'invoiced']);

        // Generate PDF
        $pdf = PDF::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'estimation' => $estimation,
            'serviceRequest' => $estimation->serviceRequest,
            
        ]);

        return $pdf->download('Invoice '.$invoice->id.'.pdf');
    }

    public function generatePDF(Invoice $invoice)
    {
        $pdf = PDF::loadView('billing.invoice-pdf', [
            'invoice' => $invoice,
            'estimation' => $invoice->estimation
        ]);
        
        $safeInvoiceNumber = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $invoice->invoice_number);
        $filename = 'Invoice ' . $safeInvoiceNumber . '.pdf';
        
        return $pdf->download($filename);
    }
} 