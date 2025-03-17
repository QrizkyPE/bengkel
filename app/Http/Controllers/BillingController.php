<?php

namespace App\Http\Controllers;

use App\Models\Estimation;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class BillingController extends Controller
{
    public function index()
    {
        try {
            // Get all approved estimations that don't have invoices yet
            $pendingEstimations = Estimation::with([
                'estimationItems.serviceRequest',
                'workOrder',
                'creator',
                'invoice'
            ])
            ->where('status', 'approved')
            ->whereDoesntHave('invoice')
            ->orderBy('approved_at', 'desc')
            ->get();
            
            // Get all invoices
            $invoices = Invoice::with([
                'estimation.estimationItems.serviceRequest',
                'estimation.workOrder',
                'creator'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
            
            return view('billing.index', compact('pendingEstimations', 'invoices'));
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Billing Index Error: ' . $e->getMessage());
            
            // Return with error message
            return back()->with('error', 'An error occurred while loading billing data: ' . $e->getMessage());
        }
    }
    
    public function createInvoice(Request $request, $estimationId)
    {
        try {
            $estimation = Estimation::with([
                'estimationItems.serviceRequest',
                'workOrder',
                'creator'
            ])->findOrFail($estimationId);
            
            // Check if this estimation already has an invoice
            if ($estimation->invoice) {
                return redirect()->route('billing.index')
                    ->with('error', 'This estimation already has an invoice');
            }
            
            // Calculate the total amount
            $totalAmount = $estimation->estimationItems->sum('total');
            
            // Generate a new invoice number
            $invoiceNumber = Invoice::generateInvoiceNumber();
            
            // Create the invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'estimation_id' => $estimation->id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);
            
            return redirect()->route('billing.index')
                ->with('success', 'Invoice created successfully');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Create Invoice Error: ' . $e->getMessage());
            
            // Return with error message
            return back()->with('error', 'An error occurred while creating the invoice: ' . $e->getMessage());
        }
    }
    
    public function markAsPaid(Request $request, $invoiceId)
    {
        try {
            $invoice = Invoice::findOrFail($invoiceId);
            
            // Update the invoice status
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
                'notes' => $request->input('notes')
            ]);
            
            return redirect()->route('billing.index')
                ->with('success', 'Invoice marked as paid');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Mark Invoice as Paid Error: ' . $e->getMessage());
            
            // Return with error message
            return back()->with('error', 'An error occurred while updating the invoice: ' . $e->getMessage());
        }
    }
    
    public function generatePDF(Request $request, $invoiceId)
    {
        $invoice = Invoice::with([
            'estimation.estimationItems.serviceRequest.user',
            'estimation.workOrder',
            'creator'
        ])->findOrFail($invoiceId);
        
        $pdf = PDF::loadView('billing.invoice-pdf', [
            'invoice' => $invoice
        ]);
        
        // Sanitize the invoice number to remove slashes and other problematic characters
        $safeInvoiceNumber = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $invoice->invoice_number);
        
        // Generate a filename based on the sanitized invoice number
        $filename = 'invoice-' . $safeInvoiceNumber . '.pdf';
        
        return $pdf->download($filename);
    }
    
    public function history()
    {
        $invoices = Invoice::with([
            'estimation.estimationItems.serviceRequest',
            'estimation.workOrder',
            'creator'
        ])
        ->whereIn('status', ['paid', 'cancelled'])
        ->orderBy('paid_at', 'desc')
        ->get();
        
        return view('billing.history', compact('invoices'));
    }
} 