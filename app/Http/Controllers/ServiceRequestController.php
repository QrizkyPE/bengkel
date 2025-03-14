<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\WorkOrder;

class ServiceRequestController extends Controller
{
    public function index()
    {
        // Load requests with their work orders
        $requests = ServiceRequest::with('workOrder')
            ->where('user_id', Auth::id())
            ->get();
        
        // If you have no requests with work orders, let's also load all work orders
        $workOrders = WorkOrder::where('user_id', Auth::id())->get();
        
        return view('requests.index', compact('requests', 'workOrders'));
    }

    public function create()
    {
        return view('requests.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'sparepart_name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'satuan' => 'required|string',
            'kebutuhan_part' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'work_order_id' => 'nullable|exists:work_orders,id',
        ]);

        $validatedData['user_id'] = auth()->id();

        // Create the service request
        ServiceRequest::create($validatedData);

        return redirect()->route('requests.index')
            ->with('success', 'Permintaan sparepart berhasil dibuat.');
    }

    public function show(ServiceRequest $request)
    {
        return view('requests.show', compact('request'));
    }

    public function edit($id)
    {
        $request = ServiceRequest::findOrFail($id);
        return view('requests.edit', compact('request'));
    }

    public function update(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);
        
        $validatedData = $request->validate([
            'sparepart_name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'satuan' => 'required|string',
            'kebutuhan_part' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $serviceRequest->update($validatedData);

        return redirect()->route('requests.index')
            ->with('success', 'Permintaan sparepart berhasil diperbarui.');
    }

    public function destroy(ServiceRequest $request)
    {
        $request->delete();
        return redirect()->route('requests.index')->with('success', 'Permintaan dihapus.');
    }

    public function generatePDF(Request $request)
    {
        try {
            // Get the work order ID from the request
            $workOrderId = $request->input('work_order_id');
            
            if (!$workOrderId) {
                return back()->with('error', 'Work Order ID is required');
            }
            
            // Get the work order
            $workOrder = \App\Models\WorkOrder::findOrFail($workOrderId);
            
            // Get all service requests for this work order
            $requests = ServiceRequest::where('work_order_id', $workOrderId)->get();
            
            if ($requests->isEmpty()) {
                $requests = collect(); // Empty collection if no requests
            }
            
            // Pass the work order data and requests to the PDF view
            $data = [
                'requests' => $requests,
                'no_polisi' => $workOrder->no_polisi,
                'kilometer' => $workOrder->kilometer,
                'no_spk' => $workOrder->no_spk,
                'type_kendaraan' => $workOrder->type_kendaraan,
                'keluhan' => $workOrder->keluhan,
            ];
            
            $pdf = PDF::loadView('requests.pdf', $data);
            
            return $pdf->download('work-order.pdf');
            
        } catch (\Exception $e) {
            \Log::error('PDF Generation failed:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }
}
