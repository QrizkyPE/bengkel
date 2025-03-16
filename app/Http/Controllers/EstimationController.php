<?php

namespace App\Http\Controllers;

use App\Models\Estimation;
use App\Models\EstimationItem;
use App\Models\WorkOrder;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class EstimationController extends Controller
{
    public function index()
    {
        try {
            $estimations = Estimation::with([
                'estimationItems.serviceRequest',
                'workOrder',
                'creator'
            ])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

            return view('estimator.estimations.index', compact('estimations'));
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Estimation Index Error: ' . $e->getMessage());
            
            // Return with error message
            return back()->with('error', 'An error occurred while loading estimations: ' . $e->getMessage());
        }
    }

    public function create(Request $request)
    {
        $workOrderId = $request->input('work_order_id');
        
        if (!$workOrderId) {
            return redirect()->route('requests.index')
                ->with('error', 'Work Order ID is required');
        }
        
        $workOrder = WorkOrder::with('serviceRequests')->findOrFail($workOrderId);
        
        if ($workOrder->serviceRequests->isEmpty()) {
            return redirect()->route('requests.index')
                ->with('error', 'Work Order does not have any service requests');
        }
        
        return view('estimator.estimations.create', compact('workOrder'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'work_order_id' => 'required|exists:work_orders,id',
            'service_advisor' => 'required|string',
            'notes' => 'nullable|string',
            'part_number.*' => 'nullable|string',
            'price.*' => 'required|numeric|min:0',
            'discount.*' => 'nullable|numeric|min:0|max:100',
            'service_request_id.*' => 'required|exists:service_requests,id',
        ]);
        
        // Create the estimation
        $estimation = Estimation::create([
            'work_order_id' => $validatedData['work_order_id'],
            'service_advisor' => $validatedData['service_advisor'],
            'notes' => $validatedData['notes'] ?? null,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);
        
        // Create the estimation items
        $serviceRequestIds = $request->input('service_request_id');
        $partNumbers = $request->input('part_number');
        $prices = $request->input('price');
        $discounts = $request->input('discount');
        
        foreach ($serviceRequestIds as $index => $serviceRequestId) {
            $price = $prices[$index] ?? 0;
            $discount = $discounts[$index] ?? 0;
            $total = $price * (1 - $discount / 100);
            
            EstimationItem::create([
                'estimation_id' => $estimation->id,
                'service_request_id' => $serviceRequestId,
                'part_number' => $partNumbers[$index] ?? null,
                'price' => $price,
                'discount' => $discount,
                'total' => $total,
            ]);
        }
        
        return redirect()->route('estimations.index')
            ->with('success', 'Estimation created successfully');
    }

    public function show(Estimation $estimation)
    {
        // Load the estimation with its items
        $estimation->load([
            'estimationItems.serviceRequest',
            'workOrder'
        ]);
        
        // Get the service request directly from the estimation item
        $serviceRequest = $estimation->estimationItems->first()->serviceRequest ?? null;
        
        if (!$serviceRequest) {
            return back()->with('error', 'Request data not found for this estimation.');
        }

        return view('estimator.estimations.show', [
            'estimation' => $estimation,
            'serviceRequest' => $serviceRequest
        ]);
    }

    public function edit(Estimation $estimation)
    {
        // Load the estimation with its items and work order
        $estimation->load([
            'estimationItems.serviceRequest',
            'workOrder'
        ]);
        
        return view('estimator.estimations.edit', compact('estimation'));
    }

    public function update(Request $request, Estimation $estimation)
    {
        if ($estimation->status !== 'pending') {
            return redirect()->route('estimations.index')
                ->with('error', 'Cannot update an estimation that is not pending');
        }
        
        $validatedData = $request->validate([
            'notes' => 'nullable|string',
            'part_number.*' => 'nullable|string',
            'price.*' => 'required|string',
            'discount.*' => 'nullable|numeric|min:0|max:100',
            'estimation_item_id.*' => 'required|exists:estimation_items,id',
        ]);
        
        // Update the estimation
        $estimation->update([
            'service_advisor' => auth()->user()->name,
            'notes' => $validatedData['notes'] ?? null,
        ]);
        
        // Update the estimation items
        $estimationItemIds = $request->input('estimation_item_id');
        $partNumbers = $request->input('part_number');
        $prices = $request->input('price');
        $discounts = $request->input('discount');
        
        // Debug information
        \Log::info('Update Estimation Items', [
            'estimation_id' => $estimation->id,
            'part_numbers' => $partNumbers,
            'prices' => $prices,
            'discounts' => $discounts
        ]);
        
        foreach ($estimationItemIds as $index => $estimationItemId) {
            $estimationItem = \App\Models\EstimationItem::find($estimationItemId);
            
            // Convert comma-formatted price to numeric
            $priceStr = $prices[$index] ?? '0';
            $price = (float) str_replace(',', '', $priceStr);
            
            $discount = (float) ($discounts[$index] ?? 0);
            
            // Calculate total based on price, discount, and quantity
            $quantity = $estimationItem->serviceRequest->quantity;
            $total = $price * $quantity * (1 - $discount / 100);
            
            // Debug information
            \Log::info('Updating Item', [
                'item_id' => $estimationItemId,
                'part_number' => $partNumbers[$index] ?? null,
                'price_str' => $priceStr,
                'price_converted' => $price,
                'discount' => $discount,
                'quantity' => $quantity,
                'total' => $total
            ]);
            
            $estimationItem->update([
                'part_number' => $partNumbers[$index] ?? null,
                'price' => $price,
                'discount' => $discount,
                'total' => $total,
            ]);
        }
        
        return redirect()->route('estimations.index')
            ->with('success', 'Estimation updated successfully');
    }

    public function approve(Request $request, $id)
    {
        $estimation = Estimation::findOrFail($id);
        
        // Optional notes for approval
        $notes = $request->input('notes');
        
        $estimation->status = 'approved';
        $estimation->approved_at = now();
        $estimation->notes = $notes;
        $estimation->save();
        
        return redirect()->route('estimations.history')
            ->with('success', 'Estimasi berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        // Validate that notes are provided for rejection
        $validated = $request->validate([
            'notes' => 'required|string|max:500',
        ], [
            'notes.required' => 'Catatan wajib diisi saat menolak estimasi. Berikan alasan penolakan.'
        ]);
        
        $estimation = Estimation::findOrFail($id);
        $estimation->status = 'rejected';
        $estimation->approved_at = now(); // We still record when it was processed
        $estimation->notes = $validated['notes'];
        $estimation->save();
        
        return redirect()->route('estimations.index')
            ->with('success', 'Estimasi berhasil ditolak.');
    }

    public function createEstimation(ServiceRequest $request)
    {
        // Create estimation for the service request
        return view('estimations.create', compact('request'));
    }

    public function storeEstimation(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'estimated_cost' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $estimation = Estimation::create([
            'service_request_id' => $serviceRequest->id,
            'estimated_cost' => $request->estimated_cost,
            'notes' => $request->notes,
            'estimator_id' => auth()->id(),
        ]);

        // Update service request status
        $serviceRequest->update(['status' => 'estimated']);

        // Generate PDF
        $pdf = PDF::loadView('estimations.pdf', [
            'estimation' => $estimation,
            'serviceRequest' => $serviceRequest
        ]);

        return $pdf->download('estimation-'.$serviceRequest->id.'.pdf');
    }

    public function history()
    {
        $estimations = Estimation::with([
            'workOrder', 
            'estimationItems.serviceRequest', 
            'creator'
        ])
        ->whereIn('status', ['approved', 'rejected'])
        ->orderBy('approved_at', 'desc')
        ->get();
        
        return view('estimator.estimations.history', compact('estimations'));
    }

    public function approveEstimation(Request $request, $id)
    {
        if (auth()->user()->role !== 'estimator') {
            abort(403, 'Unauthorized action.');
        }
        
        return $this->approve($request, $id);
    }

    public function generatePDF(Request $request, $id)
    {
        $estimation = Estimation::with([
            'estimationItems.serviceRequest.user',
            'workOrder',
            'creator'
        ])->findOrFail($id);
        
        $pdf = PDF::loadView('estimator.estimations.estimation-pdf', [
            'estimation' => $estimation
        ]);
        
        // Sanitize the work order number to remove slashes and other problematic characters
        $safeWorkOrderNumber = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $estimation->workOrder->no_spk);
        
        // Generate a filename based on the sanitized work order number
        $filename = 'estimasi-' . $safeWorkOrderNumber . '.pdf';
        
        return $pdf->download($filename);
    }
} 