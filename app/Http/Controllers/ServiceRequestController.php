<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\WorkOrder;
use App\Models\Estimation;
use App\Models\EstimationItem;

class ServiceRequestController extends Controller
{
    public function index()
    {
        // Get all service requests for work orders that don't have approved or rejected estimations
        $requests = ServiceRequest::with('workOrder')
            ->whereHas('workOrder', function ($query) {
                $query->where('user_id', auth()->id())
                    ->whereDoesntHave('estimations', function ($q) {
                        $q->whereIn('status', ['approved', 'rejected']);
                    });
            })
            ->get();

        // Get all work orders without service requests that don't have approved or rejected estimations
        $workOrders = WorkOrder::where('user_id', auth()->id())
            ->whereDoesntHave('serviceRequests')
            ->whereDoesntHave('estimations', function ($query) {
                $query->whereIn('status', ['approved', 'rejected']);
            })
            ->get();

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
            'sparepart_id' => 'nullable|exists:spareparts,id',
            'quantity' => 'required|integer|min:1',
            'satuan' => 'required|string',
            'kebutuhan_part' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'work_order_id' => 'nullable|exists:work_orders,id',
        ]);

        $validatedData['user_id'] = auth()->id();

        DB::beginTransaction();
        try {
            // Create the service request
            $serviceRequest = ServiceRequest::create($validatedData);

            // Decrease sparepart stock if sparepart_id is provided
            if ($request->filled('sparepart_id')) {
                $sparepart = Sparepart::findOrFail($request->sparepart_id);
                
                // Check if stock is sufficient
                if ($sparepart->jumlah < $validatedData['quantity']) {
                    DB::rollBack();
                    return back()->with('error', 'Stok sparepart tidak mencukupi. Stok tersedia: ' . $sparepart->jumlah . ' ' . $sparepart->satuan)
                        ->withInput();
                }

                // Decrease stock
                $sparepart->jumlah -= $validatedData['quantity'];
                $sparepart->save();
            }

            DB::commit();

            return redirect()->route('requests.index')
                ->with('success', 'Permintaan sparepart berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Service Request Store Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(ServiceRequest $request)
    {
        return view('requests.show', compact('request'));
    }

    public function edit($id)
    {
        $request = ServiceRequest::with('sparepart')->findOrFail($id);
        return view('requests.edit', compact('request'));
    }

    public function update(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        $validatedData = $request->validate([
            'sparepart_name' => 'required|string',
            'sparepart_id' => 'nullable|exists:spareparts,id',
            'quantity' => 'required|integer|min:1',
            'satuan' => 'required|string',
            'kebutuhan_part' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldSparepartId = $serviceRequest->sparepart_id;
            $oldQuantity = $serviceRequest->quantity;
            $newSparepartId = $request->filled('sparepart_id') ? $request->sparepart_id : null;
            $newQuantity = $validatedData['quantity'];

            // Handle stock changes
            if ($oldSparepartId == $newSparepartId && $oldSparepartId) {
                // Same sparepart, just quantity changed
                $sparepart = Sparepart::findOrFail($oldSparepartId);
                
                // Calculate the difference
                $quantityDiff = $newQuantity - $oldQuantity;
                
                if ($quantityDiff > 0) {
                    // Quantity increased, need to check stock and decrease
                    if ($sparepart->jumlah < $quantityDiff) {
                        DB::rollBack();
                        return back()->with('error', 'Stok sparepart tidak mencukupi. Stok tersedia: ' . $sparepart->jumlah . ' ' . $sparepart->satuan . '. Perlu tambahan: ' . $quantityDiff)
                            ->withInput();
                    }
                    $sparepart->jumlah -= $quantityDiff;
                } else if ($quantityDiff < 0) {
                    // Quantity decreased, restore the difference
                    $sparepart->jumlah += abs($quantityDiff);
                }
                // If quantityDiff == 0, no change needed
                
                $sparepart->save();
            } else {
                // Different sparepart or sparepart_id changed
                // Restore old sparepart stock
                if ($oldSparepartId) {
                    $oldSparepart = Sparepart::find($oldSparepartId);
                    if ($oldSparepart) {
                        $oldSparepart->jumlah += $oldQuantity;
                        $oldSparepart->save();
                    }
                }

                // Decrease new sparepart stock
                if ($newSparepartId) {
                    $newSparepart = Sparepart::findOrFail($newSparepartId);
                    
                    // Check if stock is sufficient
                    if ($newSparepart->jumlah < $newQuantity) {
                        DB::rollBack();
                        return back()->with('error', 'Stok sparepart tidak mencukupi. Stok tersedia: ' . $newSparepart->jumlah . ' ' . $newSparepart->satuan)
                            ->withInput();
                    }

                    // Decrease stock
                    $newSparepart->jumlah -= $newQuantity;
                    $newSparepart->save();
                }
            }

            // Update the service request
            $serviceRequest->update($validatedData);

            DB::commit();

            return redirect()->route('requests.index')
                ->with('success', 'Permintaan sparepart berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Service Request Update Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
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
            // $data = [
            //     'requests' => $requests,
            //     'no_polisi' => $workOrder->no_polisi,
            //     'kilometer' => $workOrder->kilometer,
            //     'no_spk' => $workOrder->no_spk,
            //     'type_kendaraan' => $workOrder->type_kendaraan,
            //     'keluhan' => $workOrder->keluhan,
            //     'service_advisor' => $workOrder->service_advisor,
            //     'service_user' => $workOrder->service_user,
            // ];

            // ✅ Step 1: Pass options as an array (not Options object)
            $pdf = PDF::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'chroot' => public_path(), // allow access to /public
            ])
                ->loadView('requests.pdf', [
                    'requests' => $requests,
                    'no_polisi' => $workOrder->no_polisi,
                    'kilometer' => $workOrder->kilometer,
                    'no_spk' => $workOrder->no_spk,
                    'type_kendaraan' => $workOrder->type_kendaraan,
                    'keluhan' => $workOrder->keluhan,
                    'service_advisor' => $workOrder->service_advisor,
                    'service_user' => $workOrder->service_user,
                ]);


            $safeWorkOrderNumber = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $workOrder->no_spk);

            $filename = 'WorkOrder' . $safeWorkOrderNumber . '.pdf';

            return $pdf->download($filename);
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

    public function createEstimation(Request $request)
    {
        $workOrderId = $request->input('work_order_id');

        if (!$workOrderId) {
            return redirect()->route('requests.index')
                ->with('error', 'Work Order ID is required');
        }

        $workOrder = WorkOrder::with('serviceRequests')->findOrFail($workOrderId);

        if ($workOrder->serviceRequests->isEmpty()) {
            return redirect()->route('requests.index')
                ->with('error', 'Work Order belum ada permintaan sparepart');
        }

        return view('service.estimations.create', compact('workOrder'));
    }

    public function storeEstimation(Request $request)
    {
        $validatedData = $request->validate([
            'work_order_id' => 'required|exists:work_orders,id',
            'service_advisor' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Create the estimation
        $estimation = Estimation::create([
            'work_order_id' => $validatedData['work_order_id'],
            'service_advisor' => $validatedData['service_advisor'],
            'notes' => $validatedData['notes'] ?? null,
            'status' => 'pending',
            'created_by' => Auth::id(),
        ]);

        // Create empty estimation items for each service request
        $workOrder = WorkOrder::with('serviceRequests')->findOrFail($validatedData['work_order_id']);

        foreach ($workOrder->serviceRequests as $request) {
            EstimationItem::create([
                'estimation_id' => $estimation->id,
                'service_request_id' => $request->id,
                'part_number' => null,
                'price' => 0,
                'discount' => 0,
                'total' => 0,
            ]);
        }

        return redirect()->route('requests.index')
            ->with('success', 'Estimasi berhasil pindahkan ke halaman Estimasi');
    }

    public function submitToEstimator(Request $request)
    {
        try {
            $workOrderId = $request->input('work_order_id');

            if (!$workOrderId) {
                return back()->with('error', 'Work Order ID is required');
            }

            // Get the work order
            $workOrder = \App\Models\WorkOrder::with('serviceRequests')->findOrFail($workOrderId);

            // Check if there are any service requests
            if ($workOrder->serviceRequests->isEmpty()) {
                return back()->with('error', 'Work Order belum ada permintaan sparepart');
            }

            // Check if an estimation already exists for this work order
            $existingEstimation = \App\Models\Estimation::where('work_order_id', $workOrderId)->first();
            if ($existingEstimation) {
                return back()->with('error', 'An estimation already exists for this work order');
            }

            // Create a new estimation
            $estimation = \App\Models\Estimation::create([
                'work_order_id' => $workOrderId,
                'service_advisor' => auth()->user()->name,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            // Create estimation items for each service request
            foreach ($workOrder->serviceRequests as $serviceRequest) {
                \App\Models\EstimationItem::create([
                    'estimation_id' => $estimation->id,
                    'service_request_id' => $serviceRequest->id,
                    'part_number' => null,
                    'price' => 0,
                    'discount' => 0,
                    'total' => 0,
                ]);
            }

            return back()->with('success', 'Work Order berhasil dipindahkan ke halaman Estimasi');

        } catch (\Exception $e) {
            \Log::error('Submit to Estimator failed:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to submit to Estimator: ' . $e->getMessage());
        }
    }

    public function unfilledWorkOrders()
    {
        // Get all work orders that don't have any service requests
        $workOrders = WorkOrder::whereDoesntHave('serviceRequests')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('requests.unfilled', compact('workOrders'));
    }

    public function workOrderHistory()
    {
        // Get all work orders that have estimations with status approved or rejected
        $workOrders = WorkOrder::whereHas('estimations', function ($query) {
            $query->whereIn('status', ['approved', 'rejected']);
        })
            ->where('user_id', auth()->id())
            ->with([
                'serviceRequests',
                'estimations' => function ($query) {
                    $query->whereIn('status', ['approved', 'rejected']);
                }
            ])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('requests.history', compact('workOrders'));
    }

    public function resubmitWorkOrder(Request $request)
    {
        $workOrderId = $request->input('work_order_id');
        if (!$workOrderId) {
            return redirect()->route('requests.index')
                ->with('error', 'Work Order ID is required');
        }
        // Find the work order
        $workOrder = WorkOrder::findOrFail($workOrderId);
        // Check if the work order belongs to the current user
        if ($workOrder->user_id !== auth()->id()) {
            return redirect()->route('requests.index')
                ->with('error', 'Unauthorized action');
        }
        // Delete ALL estimations for this work order (not just rejected ones)
        // This will reset the work order back to "Not Sent" status
        Estimation::where('work_order_id', $workOrderId)->delete();
        // Always allow resubmission, even if no rejected estimation exists
        return redirect()->route('requests.index')
            ->with('success', 'Work order telah direset dan siap untuk diedit kembali.');
    }
}
