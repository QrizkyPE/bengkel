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
            'kilometer' => 'required|integer',
            'no_spk' => 'required|string|unique:work_orders',
            'type_kendaraan' => 'required|string',
            'customer_name' => 'required|string',
            'keluhan' => 'nullable|string',
        ]);

        $validatedData['user_id'] = Auth::id();
        
        $workOrder = WorkOrder::create($validatedData);

        return redirect()->route('requests.create', ['work_order' => $workOrder->id])
            ->with('success', 'Work Order berhasil dibuat.');
    }

    public function show(WorkOrder $workOrder)
    {
        return view('work_orders.show', compact('workOrder'));
    }
} 