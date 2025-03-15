<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        return view('work_orders.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'no_polisi' => 'required|string',
            'kilometer' => 'required|numeric',
            'no_spk' => 'required|string',
            'type_kendaraan' => 'required|string',
            'customer_name' => 'required|string',
            'keluhan' => 'nullable|string',
        ]);
        
        // Add the user_id to the validated data
        $validatedData['user_id'] = auth()->id();
        
        $workOrder = WorkOrder::create($validatedData);
        
        return redirect()->route('requests.index')
            ->with('success', 'Work Order berhasil dibuat.');
    }

    public function show(WorkOrder $workOrder)
    {
        return view('work_orders.show', compact('workOrder'));
    }

    public function index()
    {
        $workOrders = WorkOrder::where('user_id', Auth::id())
            ->withCount('serviceRequests')
            ->get();
        
        return view('work_orders.index', compact('workOrders'));
    }

    public function destroy(WorkOrder $workOrder)
    {
        // Check if user owns this work order
        if ($workOrder->user_id !== Auth::id()) {
            return redirect()->route('requests.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus work order ini.');
        }
        
        // Delete associated service requests first
        $workOrder->serviceRequests()->delete();
        
        // Then delete the work order
        $workOrder->delete();
        
        return redirect()->route('requests.index')
            ->with('success', 'Work Order berhasil dihapus.');
    }
} 