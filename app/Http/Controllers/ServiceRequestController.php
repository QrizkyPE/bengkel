<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $requests = ServiceRequest::where('user_id', Auth::id())->get();
        return view('requests.index', compact('requests'));
    }

    public function create()
    {
        return view('requests.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'work_order_id' => 'required|exists:work_orders,id',
            'sparepart_name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'satuan' => 'required|string',
            'kebutuhan_part' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'no_polisi' => 'required|string',
            'kilometer' => 'required|integer',
            'no_spk' => 'required|string',
            'type_kendaraan' => 'required|string',
            'keluhan' => 'nullable|string',
        ]);

        $validatedData['user_id'] = auth()->id();

        // Create the service request
        ServiceRequest::create($validatedData);

        // Redirect to generate PDF with the work order data
        return redirect()->route('requests.generatePDF', $validatedData)
            ->with('success', 'Permintaan sparepart berhasil dibuat dan PDF dihasilkan.');
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
            // Retrieve the requests for the authenticated user
            $requests = ServiceRequest::where('user_id', Auth::id())->get();
            
            // Check if there are any requests
            if ($requests->isEmpty()) {
                return back()->with('error', 'No data available for PDF generation');
            }

            // Get the work order data from the request parameters
            $data = [
                'requests' => $requests,
                'no_polisi' => $request->input('no_polisi'),
                'kilometer' => $request->input('kilometer'),
                'no_spk' => $request->input('no_spk'),
                'type_kendaraan' => $request->input('type_kendaraan'),
                'keluhan' => $request->input('keluhan'),
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
