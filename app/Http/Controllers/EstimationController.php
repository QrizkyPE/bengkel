<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Estimation;
use Illuminate\Http\Request;
use PDF;

class EstimationController extends Controller
{
    public function index()
    {
        // Get all service requests that need estimation
        $serviceRequests = ServiceRequest::where('status', 'pending')
            ->orWhere('status', 'estimated')
            ->get();
        return view('estimations.index', compact('serviceRequests'));
    }

    public function show(ServiceRequest $request)
    {
        return view('estimations.show', compact('request'));
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
} 